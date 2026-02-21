<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';

check_login('admin');
only_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = mysqli_real_escape_string($koneksi,$_POST['id']);

    // cek siswa ada
    $cek = mysqli_query($koneksi,"SELECT id_siswa FROM siswa WHERE id_siswa='$id'");

    if(mysqli_num_rows($cek)==0){
        $_SESSION['error'] = 'Data siswa tidak ditemukan.';
        header('Location: siswa.php');
        exit;
    }

    mysqli_begin_transaction($koneksi);

    try{

        mysqli_query($koneksi,"DELETE FROM jawaban_siswa WHERE id_siswa='$id'");
        mysqli_query($koneksi,"DELETE FROM nilai WHERE id_siswa='$id'");
        mysqli_query($koneksi,"DELETE FROM skor_game WHERE id_siswa='$id'");
        mysqli_query($koneksi,"DELETE FROM chat WHERE id_user='$id'");

        mysqli_query($koneksi,"DELETE FROM siswa WHERE id_siswa='$id'");

        mysqli_commit($koneksi);

        $_SESSION['success'] = 'Siswa & semua datanya berhasil dihapus.';

    }catch(Exception $e){

        mysqli_rollback($koneksi);

        $_SESSION['error'] = 'Gagal hapus: '.$e->getMessage();
    }

    header('Location: siswa.php');
    exit;
}
?>