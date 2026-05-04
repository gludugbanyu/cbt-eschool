<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
include '../inc/dataadmin.php';

$targetDir = "../gambar/";
$allowedExt  = ['jpg','jpeg','png','gif','webp'];
$allowedMime = ['image/jpeg','image/png','image/gif','image/webp'];
$maxSize = 2 * 1024 * 1024; // 2MB

$responses = [];

// Pastikan folder ada
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['gambar'])) {

    $files = $_FILES['gambar'];

    for ($i = 0; $i < count($files['name']); $i++) {

        $originalName = $files['name'][$i];
        $tmpFile      = $files['tmp_name'][$i];
        $fileSize     = $files['size'][$i];
        $error        = $files['error'][$i];

        // Skip kalau error upload
        if ($error !== UPLOAD_ERR_OK) {
            $responses[] = [
                'file' => $originalName,
                'status' => 'error',
                'message' => 'Error saat upload'
            ];
            continue;
        }

        // Ambil extension
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        // VALIDASI EXTENSION
        if (!in_array($ext, $allowedExt)) {
            $responses[] = [
                'file' => $originalName,
                'status' => 'error',
                'message' => 'Format tidak didukung'
            ];
            continue;
        }

        // VALIDASI SIZE
        if ($fileSize > $maxSize) {
            $responses[] = [
                'file' => $originalName,
                'status' => 'error',
                'message' => 'Ukuran terlalu besar (maks 2MB)'
            ];
            continue;
        }

        // VALIDASI MIME TYPE (REAL FILE)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $tmpFile);
        finfo_close($finfo);

        if (!in_array($mime, $allowedMime)) {
            $responses[] = [
                'file' => $originalName,
                'status' => 'error',
                'message' => 'Mime type tidak valid'
            ];
            continue;
        }

        // VALIDASI BENAR-BENAR GAMBAR
        if (!getimagesize($tmpFile)) {
            $responses[] = [
                'file' => $originalName,
                'status' => 'error',
                'message' => 'File bukan gambar valid'
            ];
            continue;
        }

        // RENAME FILE (WAJIB - hindari konflik & exploit)
        $newName = uniqid('img_', true) . '.' . $ext;
        $targetFile = $targetDir . $newName;

        // UPLOAD FILE
        if (move_uploaded_file($tmpFile, $targetFile)) {

            // Set permission aman
            chmod($targetFile, 0644);

            $responses[] = [
                'file' => $newName,
                'status' => 'success',
                'message' => 'Berhasil diupload'
            ];

        } else {
            $responses[] = [
                'file' => $originalName,
                'status' => 'error',
                'message' => 'Gagal upload ke server'
            ];
        }
    }

    header('Content-Type: application/json');
    echo json_encode($responses);
    exit;
}