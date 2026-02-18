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
$qPengaturan = mysqli_query($koneksi,"
    SELECT logo_sekolah, versi_aplikasi, nama_aplikasi 
    FROM pengaturan 
    WHERE id=1
");

$pengaturanData = mysqli_fetch_assoc($qPengaturan);

$logoFile = "../assets/images/" . $pengaturanData['logo_sekolah'];
$versiAplikasi = $pengaturanData['versi_aplikasi'] ?? '1.0.0';
$namaAplikasi = $pengaturanData['nama_aplikasi'] ?? 'CBT E-School';
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
$verifyURL = project_url("verifikasi?token=".$token);
$qrFile = $tempDir."sertifikat_".$data['id_siswa']."_".time().".png";
QRcode::png($verifyURL,$qrFile,QR_ECLEVEL_H,5);

/* =============================
   MEDAL ICON
============================= */
$medal = "";
if($rank == 1) $medal = "ðŸ¥‡";
elseif($rank == 2) $medal = "ðŸ¥ˆ";
elseif($rank == 3) $medal = "ðŸ¥‰";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Sertifikat - <?= htmlspecialchars($data['nama_siswa']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Playfair+Display:ital,wght@0,700;1,700&family=Raleway:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0A192F;
            --secondary: #C5A059;
            --text: #333;
        }

        body { 
            margin: 0; 
            padding: 0;
            background: #f0f0f0; 
        }

        #area_pdf {
            width: 1123px;
            height: 794px;
            background: #ffffff;
            position: relative;
            font-family: 'Montserrat', sans-serif;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        /* Dekorasi Background Geometris */
        .decor-top {
            position: absolute;
            top: 0; left: 0;
            width: 400px; height: 400px;
            background: linear-gradient(135deg, var(--primary) 0%, transparent 70%);
            opacity: 0.07;
            clip-path: polygon(0 0, 100% 0, 0 100%);
        }

        .decor-bottom {
            position: absolute;
            bottom: 0; right: 0;
            width: 450px; height: 450px;
            background: linear-gradient(-45deg, var(--secondary) 0%, transparent 70%);
            opacity: 0.1;
            clip-path: polygon(100% 0, 100% 100%, 0 100%);
        }

        /* Bingkai Minimalis */
        .border-main {
            position: absolute;
            inset: 35px;
            border: 1px solid #ddd;
            z-index: 5;
        }

        .border-accent {
            position: absolute;
            inset: 45px;
            border: 3px solid var(--secondary);
            clip-path: polygon(0 0, 15% 0, 15% 2%, 2% 2%, 2% 15%, 0 15%, 0 0, 
                               85% 0, 100% 0, 100% 15%, 98% 15%, 98% 2%, 85% 2%, 85% 0,
                               100% 85%, 100% 100%, 85% 100%, 85% 98%, 98% 98%, 98% 85%, 100% 85%,
                               0 85%, 0 100%, 15% 100%, 15% 98%, 2% 98%, 2% 85%, 0 85%);
            z-index: 6;
        }

        /* Konten Utama */
        .content {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 80px 100px;
        }

        .logo-top {
            width: 70px;
            margin-bottom: 20px;
        }

        .cert-label {
            font-size: 14px;
            letter-spacing: 6px;
            text-transform: uppercase;
            color: var(--secondary);
            font-weight: 700;
            margin-bottom: 10px;
        }

        .title {
            font-family: 'Playfair Display', serif;
            font-size: 60px;
            color: var(--primary);
            margin: 0;
            font-weight: 700;
            line-height: 1;
        }

        .subtitle {
            font-family: 'Raleway', sans-serif;
            font-size: 18px;
            color: #666;
            margin-top: 15px;
            font-style: italic;
            letter-spacing: 1px;
        }

        .present-to {
            margin-top: 40px;
            font-size: 16px;
            color: #444;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .student-name {
            font-family: 'Playfair Display', serif;
            font-size: 54px;
            color: #111;
            margin: 20px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid var(--secondary);
            display: inline-block;
            min-width: 60%;
        }

        .rank-badge {
            margin-top: 10px;
            background: var(--primary);
            color: #fff;
            display: inline-block;
            padding: 10px 35px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 20px;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(10, 25, 47, 0.2);
        }

        .description {
            margin-top: 15px;
            font-family: 'Raleway', sans-serif;
            font-size: 18px;
            line-height: 1.7;
            color: #555;
        }

        .description b { color: #000; }

        /* Footer Sertifikat */
        .footer-wrap {
            position: absolute;
            bottom: 70px;
            left: 100px;
            right: 100px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            z-index: 10;
        }

        .signature {
            text-align: center;
            width: 220px;
        }

        .signature .line {
            border-top: 1px solid #aaa;
            margin-top: 80px;
            margin-bottom: 5px;
        }

        .signature p {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
            color: var(--primary);
        }

        .qr-box {
            text-align: center;
        }

        .qr-box img {
            width: 115px;
            padding: 5px;
            background: #fff;
            border: 1px solid #eee;
        }

        .qr-box span {
            display: block;
            font-size: 9px;
            color: #999;
            margin-top: 8px;
            letter-spacing: 1px;
        }

        .cert-number {
            position: absolute;
            top: 60px;
            right: 80px;
            font-size: 11px;
            color: #aaa;
            font-weight: 600;
            z-index: 10;
        }

        .watermark-bg {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80%;
            max-height: 80%;
            padding:50px;
            opacity: 0.04;
            z-index: 1;
        }
    </style>
</head>
<body>

<div id="area_pdf">
    <div class="decor-top"></div>
    <div class="decor-bottom"></div>
    <div class="border-main"></div>
    <div class="border-accent"></div>

    <?php if($logoBase64): ?>
    <img src="<?= $logoBase64 ?>" class="watermark-bg">
    <?php endif; ?>

    <div class="cert-number">NO. REF: <?= $nomor ?></div>

    <div class="content">
        <div class="cert-label">Certificate of Excellence</div>
        <h1 class="title">SERTIFIKAT PRESTASI</h1>
        <div class="subtitle">Atas pencapaian akademik yang luar biasa pada ujian <b><?= htmlspecialchars($namaAplikasi); ?></b></div>

        <div class="present-to">Diberikan kepada:</div>
        
        <div class="student-name">
            <?= strtoupper(htmlspecialchars($data['nama_siswa'])) ?>
        </div>

        <br>
        <div class="rank-badge">
            <?= $medal ?> PERINGKAT <?= $rank ?> TERBAIK
        </div>

        <div class="description">
            Siswa kelas <b><?= $data['kelas'].$data['rombel'] ?></b> yang telah menunjukkan performa unggul<br>
            dengan nilai rata-rata <b><?= $data['rata'] ?></b> melalui <b><?= $data['jumlah_ujian'] ?></b> mata uji<br>
            di antara total <b><?= $totalPeserta ?></b> peserta didik.
        </div>
    </div>

    <div class="footer-wrap">
        <div class="signature">
            <div class="line"></div>
            <p>KEPALA SEKOLAH</p>
        </div>

        <div class="qr-box">
            <img src="<?= $qrFile ?>">
            <span>VERIFIKASI SISTEM DIGITAL</span>
        </div>

        <div class="signature">
            <div class="line"></div>
            <p>WALI KELAS</p>
        </div>
    </div>

    <div style="position: absolute; margin-top:5px;bottom: 15px; width: 100%; text-align: center; font-size: 10px; color: #bbb; z-index: 10;">
        Diterbitkan secara otomatis oleh <b>CBT E-School v<?= htmlspecialchars($versiAplikasi); ?></b>
    </div>
</div>

<script src="../assets/html2pdf.js/dist/html2pdf.bundle.min.js"></script>
<script>
window.onload = function() {
    const element = document.getElementById("area_pdf");
    const opt = {
        margin:       0,
        filename:     'Sertifikat_<?= str_replace(" ", "_", $data['nama_siswa']) ?>.pdf',
        image:        { type: 'jpeg', quality: 1 },
        html2canvas:  { scale: 3, useCORS: true, letterRendering: true, dpi: 300 },
        jsPDF:        { unit: 'px', format: [1123, 794], orientation: 'landscape' }
    };
    
html2pdf().set(opt).from(element).save()
.then(() => {
    console.log("PDF berhasil dibuat");
});
}
</script>

</body>
</html>