<?php
session_start();

include '../koneksi/koneksi.php';
include '../inc/functions.php';

check_login('admin');

$kode = $_GET['kode_soal'] ?? '';

$stmt = mysqli_prepare(
    $koneksi,
    "SELECT COUNT(*) AS jml FROM nilai WHERE kode_soal = ?"
);

mysqli_stmt_bind_param($stmt, "s", $kode);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$d = mysqli_fetch_assoc($result);

echo json_encode([
    'jumlah' => (int)($d['jml'] ?? 0)
]);

mysqli_stmt_close($stmt);