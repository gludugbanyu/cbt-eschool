<?php
session_start();
include '../koneksi/koneksi.php'; // Pastikan di sini sudah ada variabel $key
include '../inc/functions.php';
check_login('admin');
only_admin();

function encrypt_data($data, $key) {
    $iv = openssl_random_pseudo_bytes(16);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
    return base64_encode($iv . $encrypted);
}

function zip_folder($folderPath) {
    $zip = new ZipArchive();
    $tmpFile = tempnam(sys_get_temp_dir(), 'zip');
    if ($zip->open($tmpFile, ZipArchive::CREATE) !== TRUE) {
        return false;
    }

    $folderPath = realpath($folderPath);
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($folderPath),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $file) {
        if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($folderPath) + 1);
            $zip->addFile($filePath, $relativePath);
        }
    }

    $zip->close();
    $zipData = file_get_contents($tmpFile);
    unlink($tmpFile);
    return $zipData;
}

// Proses backup
$folderToBackup = realpath('../gambar');
if (!$folderToBackup || !is_dir($folderToBackup)) {
    die("Folder ../gambar tidak ditemukan.");
}

$zipData = zip_folder($folderToBackup);
if ($zipData === false) {
    die("Gagal membuat zip folder.");
}

$encrypted = encrypt_data($zipData, $key);
$fileName = 'backup_gambar-' . date('Ymd_His') . '.gdbk';

// Kirim sebagai download
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . strlen($encrypted));

echo $encrypted;
exit;
