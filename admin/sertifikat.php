<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
only_admin();

/* =============================
   VALIDASI
============================= */
$id   = intval($_GET['id'] ?? 0);
$rank = intval($_GET['rank'] ?? 0);
$kelasFilter = $_GET['kelas'] ?? 'all';

if(!$id || !$rank){
    die("Data tidak valid");
}

/* =============================
   DATA SISWA
============================= */
$query = mysqli_query($koneksi,"
    SELECT siswa.*, 
           COUNT(nilai.id_nilai) as jumlah_ujian,
           ROUND(AVG(nilai.nilai + IFNULL(nilai.nilai_uraian,0)),2) as rata
    FROM siswa
    LEFT JOIN nilai ON nilai.id_siswa=siswa.id_siswa
    WHERE siswa.id_siswa=$id
");

$data = mysqli_fetch_assoc($query);
if(!$data) die("Siswa tidak ditemukan");

/* =============================
   TOTAL PESERTA
============================= */
$where = "";
if($kelasFilter !== 'all'){
    $kelasFilter = mysqli_real_escape_string($koneksi,$kelasFilter);
    $where = "WHERE CONCAT(kelas,rombel)='$kelasFilter'";
}

$totalQ = mysqli_query($koneksi,"
    SELECT COUNT(DISTINCT id_siswa) as total
    FROM siswa
    $where
");
$totalPeserta = mysqli_fetch_assoc($totalQ)['total'];

/* =============================
   NOMOR SERTIFIKAT
============================= */
$nomor = str_pad($rank,3,'0',STR_PAD_LEFT)."/CBT/".date('Y');

/* =============================
   LOGO WATERMARK
============================= */
$qLogo = mysqli_query($koneksi,"SELECT logo_sekolah FROM pengaturan WHERE id=1");
$logoData = mysqli_fetch_assoc($qLogo);
$logoFile = "../assets/images/".$logoData['logo_sekolah'];

$logoBase64 = '';
if(file_exists($logoFile)){
    $type = pathinfo($logoFile, PATHINFO_EXTENSION);
    $imgData = file_get_contents($logoFile);
    $logoBase64 = 'data:image/'.$type.';base64,'.base64_encode($imgData);
}

/* =============================
   QR ENKRIPSI + LINK VERIFIKASI
============================= */
require_once '../assets/phpqrcode/qrlib.php';
$tempDir = '../assets/temp_qr/';
if(!file_exists($tempDir)) mkdir($tempDir,0777,true);

$secret = "CBT_SECRET_2026";

$payload   = $data['id_siswa']."|".$data['rata']."|".$nomor."|".$rank;
$signature = hash_hmac('sha256',$payload,$secret);
$token     = base64_encode($payload."|".$signature);

/* URL publik */
$verifyURL = project_url("verifikasi.php?token=".$token);

$qrFile = $tempDir."sertifikat_".$data['id_siswa'].".png";
QRcode::png($verifyURL,$qrFile,QR_ECLEVEL_H,5);

/* =============================
   MEDAL ICON
============================= */
$medal = "";
if($rank == 1) $medal = "🥇";
elseif($rank == 2) $medal = "🥈";
elseif($rank == 3) $medal = "🥉";
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Sertifikat</title>

<style>
body{margin:0;background:#eaeaea}

#area_pdf{
    width:1123px;
    height:794px;
    background:#fff;
    position:relative;
    font-family:'Times New Roman',serif;
}

/* Border klasik */
.frame-outer{
    position:absolute;
    inset:30px;
    border:6px solid #d4af37;
}
.frame-inner{
    position:absolute;
    inset:45px;
    border:2px solid #c9a227;
}

/* Watermark */
.watermark{
    position:absolute;
    top:50%;
    left:50%;
    transform:translate(-50%,-50%);
    width:420px;
    opacity:0.05;
}

/* Badge pojok */
.badge-gold{
    position:absolute;
    top:60px;
    left:90px;
    background:linear-gradient(45deg,#b8860b,#ffd700);
    color:#fff;
    padding:8px 22px;
    font-size:14px;
    border-radius:25px;
    font-weight:bold;
}

/* Header */
.header{
    position:absolute;
    top:120px;
    width:100%;
    text-align:center;
}
.header h1{
    font-size:48px;
    letter-spacing:5px;
    margin:0;
}

/* Peringkat */
.peringkat{
    position:absolute;
    top:240px;
    width:100%;
    text-align:center;
    font-size:32px;
    font-weight:bold;
    color:#b8860b;
}

/* Nama */
.nama{
    position:absolute;
    top:310px;
    width:100%;
    text-align:center;
    font-size:42px;
    font-weight:bold;
}

/* Detail */
.detail{
    position:absolute;
    top:380px;
    width:100%;
    text-align:center;
    font-size:19px;
    line-height:1.9;
}

/* Nomor */
.nomor{
    position:absolute;
    top:60px;
    right:90px;
    font-size:14px;
}

/* QR */
.qr{
    position:absolute;
    bottom:80px;
    right:100px;
    text-align:center;
}
.qr img{width:115px}
</style>
</head>
<body>

<div id="area_pdf">

<div class="frame-outer"></div>
<div class="frame-inner"></div>

<?php if($logoBase64): ?>
<img src="<?= $logoBase64 ?>" class="watermark">
<?php endif; ?>

<div class="badge-gold">
    PRESTASI SEKOLAH
</div>

<div class="nomor">No: <?= $nomor ?></div>

<div class="header">
    <h1>SERTIFIKAT PRESTASI</h1>
</div>

<div class="peringkat">
    <?= $medal ?> PERINGKAT <?= $rank ?>
</div>

<div class="nama">
    <?= strtoupper(htmlspecialchars($data['nama_siswa'])) ?>
</div>

<div class="detail">
    Kelas <?= $data['kelas'].$data['rombel'] ?><br>
    Rata-rata nilai <b><?= $data['rata'] ?></b><br>
    Dari <b><?= $data['jumlah_ujian'] ?></b> ujian<br>
    Total peserta <b><?= $totalPeserta ?></b> siswa
</div>

<div class="qr">
    <img src="<?= $qrFile ?>">
    <div style="font-size:11px;">Scan untuk verifikasi online</div>
</div>

</div>

<script src="../assets/html2pdf.js/dist/html2pdf.bundle.min.js"></script>
<script>
window.onload=function(){
    const element=document.getElementById("area_pdf");
    html2pdf().set({
        margin:0,
        html2canvas:{scale:2,useCORS:true},
        jsPDF:{unit:'px',format:[1123,794],orientation:'landscape'}
    }).from(element).save("Sertifikat_<?= $data['nama_siswa'] ?>.pdf")
    .then(()=>setTimeout(()=>window.close(),800));
}
</script>

</body>
</html>