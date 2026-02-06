<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
require_once '../assets/phpqrcode/qrlib.php';
include '../inc/encrypt.php';

$thn_sekarang = date('Y');
$thn_pelajaran = $thn_sekarang . '/' . ($thn_sekarang + 1);

// Folder QR (filesystem)
$qr_temp_dir = dirname(__DIR__) . '/assets/temp_qr/';
if (!file_exists($qr_temp_dir)) {
    mkdir($qr_temp_dir, 0777, true);
}

// Filter
$where = [];
if (!empty($_GET['kelas']) && $_GET['kelas'] !== 'all') {
    $kelas = mysqli_real_escape_string($koneksi, $_GET['kelas']);
    $where[] = "CONCAT(kelas, rombel) = '$kelas'";
}
if (!empty($_GET['nama'])) {
    $nama = mysqli_real_escape_string($koneksi, $_GET['nama']);
    $where[] = "nama_siswa LIKE '%$nama%'";
}

$where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
$sql = "SELECT * FROM siswa $where_sql ORDER BY nama_siswa ASC";
$result = mysqli_query($koneksi, $sql);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Print Kartu</title>

<?php include '../inc/css.php'; ?>

<style>
@page {
    size: A4 landscape;
    margin: 10mm;
}

@media print {
    body { margin: 0; }
    .col-lg-4 { width: 33.333% !important; float:left; }
    .kartu { page-break-inside: avoid; }
}
</style>

</head>
<body onload="window.print()">

<div class="container-fluid">
<div class="row">

<?php while ($row = mysqli_fetch_assoc($result)):

    // decrypt
    $decoded = base64_decode($row['password']);
    $iv_length = openssl_cipher_iv_length($method);
    $iv2 = substr($decoded, 0, $iv_length);
    $encrypted_data = substr($decoded, $iv_length);
    $decrypted = openssl_decrypt($encrypted_data, $method, $rahasia, 0, $iv2);

    // generate QR
    $qr_file = $row['username'] . '.png';
    $qr_path = $qr_temp_dir . $qr_file;

    if (!file_exists($qr_path)) {
        QRcode::png($row['username'], $qr_path, QR_ECLEVEL_L, 3);
    }

    $qr_url = base_url('assets/temp_qr/' . $qr_file);
?>

<div class="col-lg-4 col-md-6 mb-4">
    <div class="p-3 h-100 kartu" style="border:1px solid #000;">

        <!-- HEADER SAMA PERSIS -->
        <table style="width:100%;">
            <tr>
                <td style="width:20%;">
                    <center>
                        <img src="<?= base_url('assets/images/kemdikbud.png'); ?>" style="height:35px;">
                    </center>
                </td>
                <td style="width:80%; font-size:12px;">
                    <center>
                        <strong>KARTU PESERTA UJIAN CBT</strong><br>
                        TAHUN PELAJARAN <?= $thn_pelajaran; ?>
                    </center>
                </td>
            </tr>
        </table>
        <hr style="border:0; border-top:3px double #000; margin:5px 0;">

        <table style="width:100%; font-size:12px; padding:10px;">
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td><?= htmlspecialchars($row['nama_siswa']); ?></td>
            </tr>
            <tr>
                <td>Kelas</td>
                <td>:</td>
                <td><?= htmlspecialchars($row['kelas'] . $row['rombel']); ?></td>
            </tr>
            <tr>
                <td style="width:35%;">Username</td>
                <td style="width:5%;">:</td>
                <td><?= htmlspecialchars($row['username']); ?></td>
            </tr>
            <tr>
                <td>Password</td>
                <td>:</td>
                <td><?= htmlspecialchars($decrypted); ?></td>
            </tr>
        </table>

        <br>

        <div style="text-align:right;">
            <img src="<?= $qr_url; ?>" style="height:50px;">
        </div>

    </div>
</div>

<?php endwhile; ?>

</div>
</div>

</body>
</html>