<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';

check_login('admin');
only_admin();

if(isset($_POST['id'])){

    $id = mysqli_real_escape_string($koneksi,$_POST['id']);

    mysqli_begin_transaction($koneksi);

    try{

        mysqli_query($koneksi,"DELETE FROM jawaban_siswa WHERE id_siswa='$id'");
        mysqli_query($koneksi,"DELETE FROM nilai WHERE id_siswa='$id'");
        mysqli_query($koneksi,"DELETE FROM skor_game WHERE id_siswa='$id'");
        mysqli_query($koneksi,"DELETE FROM chat WHERE id_user='$id'");

        mysqli_query($koneksi,"DELETE FROM siswa WHERE id_siswa='$id'");

        mysqli_commit($koneksi);

        echo "ok";

    }catch(Exception $e){

        mysqli_rollback($koneksi);

        echo "fail";
    }
}
?>