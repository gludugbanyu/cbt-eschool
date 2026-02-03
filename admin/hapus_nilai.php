<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_nilai'])) {

    $id = intval($_POST['id_nilai']);

    // 🔥 Ambil kode_soal dari nilai dulu
    $q = mysqli_query($koneksi, "SELECT kode_soal FROM nilai WHERE id_nilai = '$id'");
    $data = mysqli_fetch_assoc($q);

    if (!$data) {
        echo "Data tidak ditemukan.";
        exit;
    }

    $kode_soal = $data['kode_soal'];

    // 🔒 Cek hak akses (admin lewat, editor dicek kepemilikan)
    only_preview_soal_by_kode($kode_soal);

    // ✅ Baru boleh hapus
    $delete = mysqli_query($koneksi, "DELETE FROM nilai WHERE id_nilai = '$id'");

    echo $delete ? "Data berhasil dihapus." : "Gagal menghapus data.";

} else {
    echo "Permintaan tidak valid.";
}
