<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');

if (isset($_GET['kode_soal'])) {
    $kode_soal = mysqli_real_escape_string($koneksi, $_GET['kode_soal']);
    only_pemilik_soal_by_kode($kode_soal);
    $query_soal = mysqli_query($koneksi, "SELECT * FROM soal WHERE kode_soal='$kode_soal'");
    $data_soal = mysqli_fetch_assoc($query_soal);

     

    if ($data_soal['status'] == 'Aktif') {
        $_SESSION['error'] = "Soal ini sudah aktif dan tidak bisa dihapus!.";
    header('Location: soal.php');
    exit();
    }

    // Hapus butir soal
    mysqli_query($koneksi, "DELETE FROM butir_soal WHERE kode_soal = '$kode_soal'");

    // Hapus soal
    if (mysqli_query($koneksi, "DELETE FROM soal WHERE kode_soal = '$kode_soal'")) {
        $_SESSION['success'] = 'Soal berhasil dihapus.';
    } else {
        $_SESSION['error'] = 'Gagal menghapus soal: ' . mysqli_error($koneksi);
    }

    header('Location: soal.php');
    exit();
} else {
    $_SESSION['error'] = "Parameter kode_soal tidak ditemukan.";
    header('Location: soal.php');
    exit();
}
?>
