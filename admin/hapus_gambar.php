<?php
session_start();
include '../inc/functions.php';

// 🔐 WAJIB LOGIN
check_login_api('admin');

// 🔐 WAJIB POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['success'=>false]));
}

if (!isset($_POST['files']) || !is_array($_POST['files'])) {
    exit(json_encode(['success'=>false]));
}

$dir = realpath('../gambar/') . DIRECTORY_SEPARATOR;
$response = [];

foreach ($_POST['files'] as $file) {

    // 🚨 HAPUS PATH TRAVERSAL
    $file = basename($file);

    $target = realpath($dir . $file);

    // 🚨 PASTIKAN FILE MEMANG DI /gambar
    if ($target && strpos($target, $dir) === 0 && is_file($target)) {

        if (unlink($target)) {
            $response[] = [
                'file'=>$file,
                'status'=>'success',
                'message'=>'Gambar berhasil dihapus'
            ];
        } else {
            $response[] = [
                'file'=>$file,
                'status'=>'error',
                'message'=>'Gagal menghapus'
            ];
        }

    } else {
        $response[] = [
            'file'=>$file,
            'status'=>'error',
            'message'=>'File tidak valid'
        ];
    }
}

echo json_encode(['success'=>true,'files'=>$response]);