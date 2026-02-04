<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');

if (isset($_GET['kode_soal'])) {

    $kode_soal = mysqli_real_escape_string($koneksi, $_GET['kode_soal']);
    only_pemilik_soal_by_kode($kode_soal);

    $q = mysqli_query($koneksi, "SELECT status FROM soal WHERE kode_soal='$kode_soal'");
    $d = mysqli_fetch_assoc($q);

    if ($d['status'] == 'Aktif') {
        $_SESSION['error'] = "Soal aktif tidak bisa dihapus!";
        header('Location: soal.php');
        exit();
    }

    mysqli_begin_transaction($koneksi);

    try {

        // 1️⃣ Hapus nilai siswa dulu
        mysqli_query($koneksi, "DELETE FROM nilai WHERE kode_soal='$kode_soal'");

        // 2️⃣ Hapus butir soal
        mysqli_query($koneksi, "DELETE FROM butir_soal WHERE kode_soal='$kode_soal'");

        // 3️⃣ Hapus soal
        mysqli_query($koneksi, "DELETE FROM soal WHERE kode_soal='$kode_soal'");

        mysqli_commit($koneksi);

        $_SESSION['success'] = "Soal & seluruh nilai siswa berhasil dihapus.";

    } catch (Exception $e) {

        mysqli_rollback($koneksi);
        $_SESSION['error'] = "Gagal menghapus: " . $e->getMessage();
    }

} else {
    $_SESSION['error'] = "Parameter tidak ditemukan.";
}

header('Location: soal.php');
exit();
