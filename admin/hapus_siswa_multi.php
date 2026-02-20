<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';

check_login('admin');
only_admin();

if(isset($_POST['id'])){

    $id = mysqli_real_escape_string($koneksi,$_POST['id']);

    mysqli_query($koneksi,"DELETE FROM siswa WHERE id_siswa='$id'");

    echo "ok";
}
?>