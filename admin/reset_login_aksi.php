<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
include '../inc/dataadmin.php';

if (!isset($_POST['id_siswa']) || !isset($_POST['kode_soal'])) {
    $_SESSION['error'] = "Data tidak valid.";
    header("Location: reset_login.php");
    exit;
}

$id_siswa = $_POST['id_siswa'];
$kode_soal = $_POST['kode_soal'];

$berhasil = 0;

// Jika MULTI SELECT (array)
if (is_array($id_siswa)) {
    for ($i = 0; $i < count($id_siswa); $i++) {
        $id = mysqli_real_escape_string($koneksi, $id_siswa[$i]);
        $kode = mysqli_real_escape_string($koneksi, $kode_soal[$i]);

        $query = "UPDATE jawaban_siswa 
                  SET status_ujian = 'Non-Aktif' 
                  WHERE id_siswa = '$id' AND kode_soal = '$kode'";

        if (mysqli_query($koneksi, $query)) {
            $berhasil++;
        }
    }

    $_SESSION['success'] = "$berhasil siswa berhasil direset.";

} else {
    // Jika SINGLE reset (cara lama)
    $id = mysqli_real_escape_string($koneksi, $id_siswa);
    $kode = mysqli_real_escape_string($koneksi, $kode_soal);

    $query = "UPDATE jawaban_siswa 
              SET status_ujian = 'Non-Aktif' 
              WHERE id_siswa = '$id' AND kode_soal = '$kode'";

    if (mysqli_query($koneksi, $query)) {
        $_SESSION['success'] = "Login siswa berhasil direset.";
    } else {
        $_SESSION['error'] = "Gagal mereset login siswa.";
    }
}

header("Location: reset_login.php");
exit;
?>
