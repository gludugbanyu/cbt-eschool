<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
only_admin();
include '../inc/dataadmin.php';

$response = ['status' => 'error', 'message' => 'Tidak ada aksi dilakukan'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset'])) {
    $reset = $_POST['reset'];

    mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS=0");

    foreach ($reset as $item) {
        if ($item == 'siswa') {
            if (!mysqli_query($koneksi, "TRUNCATE TABLE siswa")) {
                $response['message'] = 'Gagal reset siswa: ' . mysqli_error($koneksi);
                echo json_encode($response);
                exit;
            }
        }

        if ($item == 'soal') {
            mysqli_query($koneksi, "TRUNCATE TABLE soal");
            mysqli_query($koneksi, "TRUNCATE TABLE jawaban_siswa");
            mysqli_query($koneksi, "TRUNCATE TABLE butir_soal");

            // Hapus file gambar kecuali .htaccess
            $files = glob('../gambar/*');
            foreach ($files as $file) {
                if (is_file($file) && basename($file) !== '.htaccess') {
                    unlink($file);
                }
            }
        }

        if ($item == 'nilai') {
            // 🔴 TADINYA cuma nilai saja
            if (!mysqli_query($koneksi, "TRUNCATE TABLE nilai")) {
                $response['message'] = 'Gagal reset nilai: ' . mysqli_error($koneksi);
                echo json_encode($response);
                exit;
            }

            // ✅ TAMBAHKAN INI supaya jawaban_siswa ikut kosong
            if (!mysqli_query($koneksi, "TRUNCATE TABLE jawaban_siswa")) {
                $response['message'] = 'Gagal reset jawaban_siswa: ' . mysqli_error($koneksi);
                echo json_encode($response);
                exit;
            }
        }
    }

    mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS=1");

    $response = ['status' => 'success', 'message' => 'Data berhasil direset sesuai pilihan'];
}

header('Content-Type: application/json');
echo json_encode($response);
