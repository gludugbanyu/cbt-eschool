<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
include '../inc/encrypt.php';
check_login('admin');
only_admin();
include '../inc/dataadmin.php';
require 'autoload.php'; // autoload PhpSpreadsheet (via Composer)

// Import namespace
use PhpOffice\PhpSpreadsheet\IOFactory;

// Set header untuk JSON response
header('Content-Type: application/json');

// Validasi file ada
if (isset($_FILES['file']['name'])) {
    $file = $_FILES['file']['tmp_name'];
    $filename = $_FILES['file']['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $allowed_ext = ['xls', 'xlsx'];

    // Cek ekstensi file
    if (!in_array($ext, $allowed_ext)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Hanya file Excel (.xls, .xlsx) yang diperbolehkan!',
            'berhasil' => 0,
            'gagal' => 0,
            'duplikat' => 0
        ]);
        exit;
    }

    // Load file Excel
    try {
        $spreadsheet = IOFactory::load($file);
        $data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
    } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal membaca file Excel: ' . $e->getMessage(),
            'berhasil' => 0,
            'gagal' => 0,
            'duplikat' => 0
        ]);
        exit;
    }

    $berhasil = 0;
    $gagal = 0;
    $duplikat = 0;

    foreach ($data as $i => $row) {
        if ($i == 1) continue; // Lewati header

        $nama     = mysqli_real_escape_string($koneksi, htmlspecialchars(trim($row['A'] ?? '')));
        $username = mysqli_real_escape_string($koneksi, htmlspecialchars(trim($row['B'] ?? '')));
        $password = trim(preg_replace('/\s+/', '', $row['C'] ?? ''));
        $kelas    = mysqli_real_escape_string($koneksi, htmlspecialchars(trim($row['D'] ?? '')));
        $rombel   = mysqli_real_escape_string($koneksi, htmlspecialchars(trim($row['E'] ?? '')));

        if ($nama && $username && $password && $kelas && $rombel) {
            // Cek duplikat
            $cek = mysqli_query($koneksi, "SELECT * FROM siswa WHERE username = '$username'");
            if (mysqli_num_rows($cek) > 0) {
                $duplikat++;
                continue;
            }

            // Enkripsi password
            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
            $encrypted = openssl_encrypt($password, $method, $rahasia, 0, $iv);
            $final = base64_encode($iv . $encrypted);

            // Insert DB
            $insert = mysqli_query($koneksi, "INSERT INTO siswa (nama_siswa, username, password, kelas, rombel) VALUES ('$nama', '$username', '$final', '$kelas', '$rombel')");

            if ($insert) {
                $berhasil++;
            } else {
                $gagal++;
            }
        } else {
            $gagal++;
        }
    }

    echo json_encode([
        'status' => 'success',
        'message' => "Berhasil: $berhasil\nGagal: $gagal\nDuplikat: $duplikat",
        'berhasil' => $berhasil,
        'gagal' => $gagal,
        'duplikat' => $duplikat
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Tidak ada file yang diupload.',
        'berhasil' => 0,
        'gagal' => 0,
        'duplikat' => 0
    ]);
}
