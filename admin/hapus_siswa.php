<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
only_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    // Cek apakah data siswa ada
    $data = mysqli_query($koneksi, "SELECT * FROM siswa WHERE id_siswa = '$id'");
    if (mysqli_num_rows($data) == 0) {
        $_SESSION['error'] = 'Data siswa tidak ditemukan.';
        header('Location: siswa.php');
        exit;
    }

    // Hapus data siswa
    if (mysqli_query($koneksi, "DELETE FROM siswa WHERE id_siswa = '$id'")) {
        $_SESSION['success'] = 'Siswa berhasil dihapus.';
    } else {
        $_SESSION['error'] = 'Gagal menghapus siswa: ' . mysqli_error($koneksi);
    }

    header('Location: siswa.php');
    exit;
} else {
    header('Location: siswa.php');
    exit;
}
