<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');

if (!isset($_GET['id_soal'])) {
    header('Location: soal.php');
    exit();
}

$id_soal = mysqli_real_escape_string($koneksi, $_GET['id_soal']);

// 🔥 Ambil kode_soal dari butir
$qButir = mysqli_query($koneksi, "
    SELECT kode_soal 
    FROM butir_soal 
    WHERE id_soal='$id_soal'
");

if (mysqli_num_rows($qButir) == 0) {
    header('Location: soal.php?error=notfound');
    exit();
}

$dataButir = mysqli_fetch_assoc($qButir);
$kode_soal = $dataButir['kode_soal'];


// 🔥 Sekarang cek ownership pakai fungsi yang BENAR
only_pemilik_soal_by_kode($kode_soal);


// 🔥 Hapus butir
$hapus = mysqli_query($koneksi, "
    DELETE FROM butir_soal 
    WHERE id_soal='$id_soal'
");

if ($hapus) {
    header('Location: daftar_butir_soal.php?kode_soal=' . urlencode($kode_soal) . '&success=1');
} else {
    header('Location: daftar_butir_soal.php?kode_soal=' . urlencode($kode_soal) . '&error=1');
}
exit();
