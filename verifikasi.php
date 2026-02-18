<?php
include 'koneksi/koneksi.php';

/* =============================
   INIT
============================= */
$status = false;
$data   = null;
$errorMessage = '';
$rank   = '';
$nomor  = '';

/* =============================
   AMBIL TOKEN
============================= */
$token = $_GET['token'] ?? '';

if(!$token){
    $errorMessage = "Token tidak ditemukan.";
}else{

    $decoded = base64_decode($token);
    $parts   = explode("|",$decoded);

    if(count($parts) !== 5){
        $errorMessage = "Format token tidak valid.";
    }else{

        list($id_siswa,$rata,$nomor,$rank,$signature) = $parts;
        $id_siswa = intval($id_siswa);

        /* =============================
           SECRET (HARUS SAMA DENGAN sertifikat.php)
        ============================= */
        $secret = "CBT_SECRET_2026";

        $payload = $id_siswa."|".$rata."|".$nomor."|".$rank;
        $validSignature = hash_hmac('sha256',$payload,$secret);

        if(!hash_equals($validSignature,$signature)){
            $errorMessage = "Signature tidak valid atau telah dimanipulasi.";
        }else{

            $cek = mysqli_query($koneksi,"
                SELECT siswa.nama_siswa,
                       CONCAT(siswa.kelas,siswa.rombel) as kelas,
                       ROUND(AVG(nilai.nilai + IFNULL(nilai.nilai_uraian,0)),2) as rata_db
                FROM siswa
                LEFT JOIN nilai ON nilai.id_siswa=siswa.id_siswa
                WHERE siswa.id_siswa='$id_siswa'
                GROUP BY siswa.id_siswa
            ");

            if($row = mysqli_fetch_assoc($cek)){
                if($row['rata_db'] == $rata){
                    $status = true;
                    $data   = $row;
                }else{
                    $errorMessage = "Data nilai tidak cocok dengan database.";
                }
            }else{
                $errorMessage = "Data siswa tidak ditemukan.";
            }
        }
    }
}

/* =============================
   AMBIL LOGO & NAMA APLIKASI
============================= */
$qLogo = mysqli_query($koneksi,"SELECT logo_sekolah,nama_aplikasi FROM pengaturan WHERE id=1");
$logoData = mysqli_fetch_assoc($qLogo);

$logoBase64 = "";
if(!empty($logoData['logo_sekolah'])){
    $logoFile = "assets/images/".$logoData['logo_sekolah'];
    if(file_exists($logoFile)){
        $type = pathinfo($logoFile, PATHINFO_EXTENSION);
        $imgData = file_get_contents($logoFile);
        $logoBase64 = 'data:image/'.$type.';base64,'.base64_encode($imgData);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verifikasi Sertifikat</title>
<link rel="icon" type="image/png" href="assets/images/icon.png" />
<style>
body{
    margin:0;
    font-family:Arial, sans-serif;
    background:linear-gradient(135deg,#0f2027,#203a43,#2c5364);
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.card{
    background:#fff;
    width:520px;
    padding:40px;
    border-radius:14px;
    text-align:center;
    box-shadow:0 20px 50px rgba(0,0,0,0.4);
    position:relative;
    overflow:hidden;
}

/* Gold Top Bar */
.card::before{
    content:'';
    position:absolute;
    top:0;
    left:0;
    width:100%;
    height:6px;
    background:linear-gradient(90deg,#b8860b,#ffd700,#b8860b);
}

.logo{
    width:180px;
    margin-bottom:15px;
}

.valid{
    color:#198754;
    font-size:22px;
    font-weight:bold;
}

.invalid{
    color:#dc3545;
    font-size:22px;
    font-weight:bold;
}

.rank-badge{
    margin:15px auto;
    width:150px;
    padding:8px;
    border-radius:30px;
    background:linear-gradient(90deg,#b8860b,#ffd700,#b8860b);
    color:#000;
    font-weight:bold;
}

.alert-error{
    margin-top:15px;
    padding:15px;
    background:#f8d7da;
    border-radius:8px;
    color:#842029;
}

hr{
    margin:20px 0;
}
</style>
</head>
<body>

<div class="card">

<?php if($logoBase64): ?>
    <img src="<?= $logoBase64 ?>" class="logo">
<?php endif; ?>

<h3><?= htmlspecialchars($logoData['nama_aplikasi'] ?? 'CBT E-School') ?></h3>
<p>Verifikasi Sertifikat Prestasi</p>

<hr>

<?php if($status): ?>

    <div class="valid">✔ SERTIFIKAT VALID</div>

    <div class="rank-badge">
        PERINGKAT <?= htmlspecialchars($rank) ?>
    </div>

    <p><b>Nama:</b> <?= htmlspecialchars($data['nama_siswa']) ?></p>
    <p><b>Kelas:</b> <?= htmlspecialchars($data['kelas']) ?></p>
    <p><b>Rata-rata Nilai:</b> <?= htmlspecialchars($data['rata_db']) ?></p>
    <p><b>Nomor Sertifikat:</b> <?= htmlspecialchars($nomor) ?></p>

<?php else: ?>

    <div class="invalid">✖ SERTIFIKAT TIDAK VALID</div>

    <div class="alert-error">
        <?= htmlspecialchars($errorMessage ?: "Token tidak sah atau telah dimodifikasi.") ?>
    </div>

<?php endif; ?>

</div>

</body>
</html>