<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';

if(!isset($_SESSION['admin_logged_in'])){
    echo 0;
    exit;
}

header('Content-Type: application/json');

if(isset($_POST['id'])){

    $id = mysqli_real_escape_string($koneksi,$_POST['id']);

    // 🔥 AMBIL NAMA SISWA
    $s = mysqli_query($koneksi,"SELECT nama_siswa FROM siswa WHERE id_siswa='$id'");
    $d = mysqli_fetch_assoc($s);
    $nama = $d['nama_siswa'] ?? 'Siswa';

    $cek1 = mysqli_query($koneksi,"SELECT 1 FROM nilai WHERE id_siswa='$id' LIMIT 1");
    $cek2 = mysqli_query($koneksi,"SELECT 1 FROM jawaban_siswa WHERE id_siswa='$id' LIMIT 1");
    $cek3 = mysqli_query($koneksi,"SELECT 1 FROM skor_game WHERE id_siswa='$id' LIMIT 1");
    $cek4 = mysqli_query($koneksi,"SELECT 1 FROM chat WHERE id_user='$id' LIMIT 1");

    $status = (
        mysqli_num_rows($cek1)>0 ||
        mysqli_num_rows($cek2)>0 ||
        mysqli_num_rows($cek3)>0 ||
        mysqli_num_rows($cek4)>0
    ) ? 'ada' : 'tidak';

    echo json_encode([
        'status'=>$status,
        'nama'=>$nama
    ]);
}