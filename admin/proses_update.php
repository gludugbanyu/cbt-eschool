<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
only_admin();
include '../inc/dataadmin.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (
    empty($data['csrf_token']) ||
    !hash_equals($_SESSION['csrf_update'], $data['csrf_token'])
) {
    echo json_encode([
        'success'=>false,
        'message'=>'CSRF token tidak valid'
    ]);
    exit;
}

$versi_baru = $data['versi_baru'] ?? '';
$url        = $data['url'] ?? '';
$hash       = $data['hash'] ?? '';

if (!$hash) {
    echo json_encode([
        'success'=>false,
        'message'=>'Hash tidak ada'
    ]);
    exit;
}

if (!$versi_baru || !$url) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

// Fungsi untuk menyalin rekursif
function copyRecursive($source, $dest, $root_path) {

    if (is_dir($source)) {
        @mkdir($dest, 0755, true);
        $files = scandir($source);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;

            $srcPath  = $source . DIRECTORY_SEPARATOR . $file;
            $destPath = $dest   . DIRECTORY_SEPARATOR . $file;

$targetDir = dirname($destPath);

if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

$realDest = realpath($targetDir);

if ($realDest === false || strpos($realDest, $root_path) !== 0) {
    continue;
}

$rel = str_replace($root_path . DIRECTORY_SEPARATOR, '', $destPath);

if (preg_match('#^koneksi(/|\\\\)#', $rel)) {
    continue;
}

            copyRecursive($srcPath, $destPath, $root_path);
        }

    } elseif (file_exists($source)) {
        copy($source, $dest);
    }
}

// Fungsi untuk menghapus folder rekursif
function hapusFolder($folderPath) {
    if (!is_dir($folderPath)) return;
    $items = scandir($folderPath);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $folderPath . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) {
            hapusFolder($path);
        } else {
            @unlink($path);
        }
    }
    @rmdir($folderPath);
}

$tmp_zip = __DIR__ . '/update.zip';
$folder_extract = __DIR__ . '/update_temp/';
$root_path = realpath(__DIR__ . '/../'); // Path root aplikasi

// Download update
file_put_contents($tmp_zip, file_get_contents($url));
$local_hash = hash_file('sha256', $tmp_zip);

if ($local_hash !== $hash) {
    unlink($tmp_zip);
    echo json_encode([
        'success'=>false,
        'message'=>'Update dibatalkan! File tidak valid.'
    ]);
    exit;
}
// Ekstrak file ZIP
$zip = new ZipArchive();
if ($zip->open($tmp_zip) === TRUE) {
    if (!is_dir($folder_extract)) {
        mkdir($folder_extract, 0755, true);
    }

    for ($i = 0; $i < $zip->numFiles; $i++) {

    $entry = $zip->getNameIndex($i);

    // ❌ Tolak path traversal
    if (strpos($entry, '..') !== false) continue;
    if (substr($entry, 0, 1) === '/') continue;
    if (substr($entry, 0, 1) === '\\') continue;
    if (substr($entry, 0, 1) === '.') continue;

    $targetPath = $folder_extract . $entry;
    $targetDir  = dirname($targetPath);

    // ❌ Pastikan tetap di dalam folder_extract
    $realBase = realpath($folder_extract);
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $realTargetDir = realpath($targetDir);
    if ($realTargetDir === false || strpos($realTargetDir, $realBase) !== 0) {
        continue;
    }

    // ❌ Block file berbahaya
    $ext = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
    $blocked = ['exe','sh','bat','cmd','msi'];

    if (in_array($ext, $blocked)) continue;

    if (substr($entry, -1) === '/') {
        @mkdir($targetPath, 0755, true);
    } else {
        copy("zip://".$tmp_zip."#".$entry, $targetPath);
    }
}

$zip->close();
unlink($tmp_zip);

    $folders = array_diff(scandir($folder_extract), ['.', '..']);
    $source_folder = null;
    
    foreach ($folders as $folder) {
        if (is_dir($folder_extract . $folder)) {
            $source_folder = $folder_extract . $folder;
            break;
        }
    }

    if ($source_folder) {
        copyRecursive($source_folder, $root_path, $root_path);
        hapusFolder($folder_extract);

        // 🔐 Escape versi sebelum update database
        $versi_baru_safe = mysqli_real_escape_string($koneksi, $versi_baru);
        mysqli_query($koneksi, "UPDATE pengaturan SET versi_aplikasi = '$versi_baru_safe' WHERE id = 1");

        // 📝 Log setiap update (disimpan di file update_log.txt)
        file_put_contents(
            __DIR__ . '/update_log.txt',
            "[" . date('Y-m-d H:i:s') . "] Update berhasil → versi baru: $versi_baru_safe\n",
            FILE_APPEND
        );

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Struktur folder update tidak valid']);
    }
} else {
    @unlink($tmp_zip);
    echo json_encode(['success' => false, 'message' => 'Gagal ekstrak file ZIP']);
}