<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');

$kode = $_GET['kode_soal'] ?? '';

$q = mysqli_query($koneksi,"
    SELECT COUNT(*) as jml 
    FROM nilai 
    WHERE kode_soal='$kode'
");

$d = mysqli_fetch_assoc($q);

echo json_encode([
    'jumlah' => (int)$d['jml']
]);
 
