<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';

check_login('admin');

if (!isset($_GET['kode_soal'])) {
    die('Kode soal tidak ditemukan.');
}

$kode_soal = mysqli_real_escape_string($koneksi, $_GET['kode_soal']);

// ⛔ KUNCI AKSES DI SINI
only_pemilik_soal_by_kode($kode_soal);

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=butir_soal_$kode_soal.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Base URL untuk gambar
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$base_url = $protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
$base_url = rtrim($base_url, '/') . '/';

$query = mysqli_query($koneksi, "SELECT * FROM butir_soal WHERE kode_soal='$kode_soal' ORDER BY nomer_soal ASC");
$fields = mysqli_fetch_fields($query);

// Header tabel
echo "<table border='1'><tr>";
foreach ($fields as $field) {
    if ($field->name === 'id_soal') continue;
    echo "<th>" . htmlspecialchars($field->name) . "</th>";
}
echo "</tr>";

// Isi tabel
while ($row = mysqli_fetch_assoc($query)) {
    echo "<tr>";
    foreach ($fields as $field) {
        if ($field->name === 'id_soal') continue;
        $value = $row[$field->name];

        if (is_string($value) && strpos($value, '<img') !== false) {
            $value = preg_replace_callback('/<img[^>]+src="([^"]+)"/i', function ($matches) use ($base_url) {
                $src = $matches[1];
                if (strpos($src, 'http') !== 0) {
                    $src = $base_url . ltrim($src, '/');
                }
                return '<img src="' . $src . '" width="100" height="100">';
            }, $value);
        }

        echo "<td style='vertical-align: top;'>$value</td>";
    }
    echo "</tr>";
}

echo "</table>";
?>
