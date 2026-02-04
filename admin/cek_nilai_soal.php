<?php
include '../koneksi/koneksi.php';

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
