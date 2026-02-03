<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
only_admin();
include '../inc/dataadmin.php';
// Ambil data dari form
$nama_aplikasi = $_POST['nama_aplikasi'];
$warna_tema = $_POST['warna_tema'];
$waktu_sinkronisasi = intval($_POST['waktu_sinkronisasi']);
if ($waktu_sinkronisasi < 60) {
    $_SESSION['error'] = 'Waktu sinkronisasi tidak boleh kurang dari 60 detik.';
    header('Location: setting.php');
    exit;
}
$sembunyikan_nilai = isset($_POST['sembunyikan_nilai']) ? 1 : 0;
$login_ganda = $_POST['login_ganda'];
$chat = $_POST['chat'];
$izinkan_lanjut_ujian = $_POST['izinkan_lanjut_ujian'] ?? 'tidak';
$logo_path = ''; // default kosong

// Validasi dan handle upload logo
if (!empty($_FILES['logo_sekolah']['name'])) {
    $nama_file = $_FILES['logo_sekolah']['name'];
    $tmp_file = $_FILES['logo_sekolah']['tmp_name'];
    $ukuran = $_FILES['logo_sekolah']['size'];

    // Dapatkan ekstensi
    $ext = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
    $ekstensi_valid = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    // Validasi ekstensi, ukuran, dan MIME type
    $mime = mime_content_type($tmp_file);
    $mime_valid = in_array($mime, ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

    if (in_array($ext, $ekstensi_valid) && $mime_valid && $ukuran <= 2 * 1024 * 1024) { // max 2MB
        $nama_baru = 'logo_' . time() . '.' . $ext; // Rename file
        $tujuan = '../assets/images/' . $nama_baru;

        if (move_uploaded_file($tmp_file, $tujuan)) {
            $logo_path = $nama_baru;
        }
    } else {
        $_SESSION['error'] = 'Upload gagal: hanya file gambar (.jpg/.png/.gif/.webp) maksimal 2MB yang diperbolehkan.';
        header('Location: setting.php');
        exit;
    }
}

// Cek apakah sudah ada data
$query = mysqli_query($koneksi, "SELECT id FROM pengaturan WHERE id=1");
if (mysqli_num_rows($query) > 0) {
    // Update data
    $sql = "UPDATE pengaturan SET 
        nama_aplikasi='$nama_aplikasi',
        warna_tema='$warna_tema',
        waktu_sinkronisasi=$waktu_sinkronisasi,
        sembunyikan_nilai=$sembunyikan_nilai,
        chat='$chat',
        login_ganda='$login_ganda',
        izinkan_lanjut_ujian='$izinkan_lanjut_ujian'"
        . ($logo_path ? ", logo_sekolah='$logo_path'" : "") .
        " WHERE id=1";
} else {
    // Insert awal (jika belum ada)
$sql = "INSERT INTO pengaturan (id, nama_aplikasi, logo_sekolah, warna_tema, waktu_sinkronisasi, sembunyikan_nilai, login_ganda, chat, izinkan_lanjut_ujian)
VALUES (1, '$nama_aplikasi', '$logo_path', '$warna_tema', $waktu_sinkronisasi, $sembunyikan_nilai, '$login_ganda', '$chat', '$izinkan_lanjut_ujian')";
}

// Eksekusi query
if (mysqli_query($koneksi, $sql)) {
    $_SESSION['success'] = 'Pengaturan berhasil disimpan!';
} else {
    $_SESSION['error'] = 'Gagal menyimpan pengaturan: ' . mysqli_error($koneksi);
}

header('Location: setting.php');
exit;
?>
