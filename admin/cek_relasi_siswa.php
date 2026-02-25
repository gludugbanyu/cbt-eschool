<?php
session_start();   // 🔥 WAJIB
include '../koneksi/koneksi.php';

include '../inc/functions.php';
check_login('admin');
$ids = $_POST['ids'];
$count = 0;

foreach($ids as $id){

    $id = mysqli_real_escape_string($koneksi,$id);

    $cek1 = mysqli_query($koneksi,"SELECT 1 FROM nilai WHERE id_siswa='$id' LIMIT 1");
    $cek2 = mysqli_query($koneksi,"SELECT 1 FROM jawaban_siswa WHERE id_siswa='$id' LIMIT 1");
    $cek3 = mysqli_query($koneksi,"SELECT 1 FROM skor_game WHERE id_siswa='$id' LIMIT 1");
    $cek4 = mysqli_query($koneksi,"SELECT 1 FROM chat WHERE id_user='$id' LIMIT 1");

    if(
        mysqli_num_rows($cek1)>0 ||
        mysqli_num_rows($cek2)>0 ||
        mysqli_num_rows($cek3)>0 ||
        mysqli_num_rows($cek4)>0
    ){
        $count++;
    }
}

echo $count;
?>