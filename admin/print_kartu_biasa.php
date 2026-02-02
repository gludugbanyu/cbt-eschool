<?php
include '../koneksi/koneksi.php';
require_once '../assets/phpqrcode/qrlib.php';

$qr_temp_dir = '../assets/temp_qr/';
if (!file_exists($qr_temp_dir)) {
    mkdir($qr_temp_dir, 0777, true);
}

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Siswa</title>
    <?php include '../inc/css.php'; ?>
    <style>
        body { font-family: Arial; }
        .kartu {
            width: 32%;
            border: 1px solid #000;
            padding: 10px;
            margin: 5px;
            float: left;
            font-size: 12px;
        }
        img { max-height: 50px; }
        @media print {
            .kartu { page-break-inside: avoid; }
        }
    </style>
</head>
<body onload="window.print()">

<?php while ($row = mysqli_fetch_assoc($result)):
    include '../inc/encrypt.php';
    $decoded = base64_decode($row['password']);
    $iv_length = openssl_cipher_iv_length($method);
    $iv2 = substr($decoded, 0, $iv_length);
    $encrypted_data = substr($decoded, $iv_length);
    $decrypted = openssl_decrypt($encrypted_data, $method, $rahasia, 0, $iv2);
    $qr_filename = $qr_temp_dir . $row['username'] . '.png';

    if (!file_exists($qr_filename)) {
        QRcode::png($row['username'], $qr_filename, QR_ECLEVEL_L, 3);
    }
?>

<div class="kartu">
    <center><strong>KARTU PESERTA UJIAN CBT</strong></center><br>
    Nama: <?= $row['nama_siswa'] ?><br>
    Kelas: <?= $row['kelas'] . $row['rombel'] ?><br>
    Username: <?= $row['username'] ?><br>
    Password: <?= $decrypted ?><br><br>
    <div style="text-align:right">
        <img src="<?= $qr_filename ?>">
    </div>
</div>

<?php endwhile; ?>

</body>
</html>
