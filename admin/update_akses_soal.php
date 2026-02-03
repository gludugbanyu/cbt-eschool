<?php
include '../koneksi/koneksi.php';

$id_soal = $_POST['id_soal'];
$ids     = $_POST['ids'];

mysqli_query($koneksi,"UPDATE soal SET id_pembuat='$ids' WHERE id_soal='$id_soal'");

echo json_encode(['status'=>'ok']);
