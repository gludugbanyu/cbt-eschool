<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['src'])) {
    exit(json_encode(['status'=>'invalid_request']));
}

$src = $_POST['src'];

// ambil nama file saja
$filename = basename(parse_url($src, PHP_URL_PATH));

// validasi nama file (anti ../ dll)
if (!preg_match('/^[a-zA-Z0-9._-]+$/', $filename)) {
    exit(json_encode(['status'=>'invalid_filename']));
}

$baseDir = realpath(__DIR__ . '/../gambar/');
$file    = realpath($baseDir . '/' . $filename);

// pastikan file masih di dalam folder gambar
if ($file && strpos($file, $baseDir) === 0 && file_exists($file)) {
    unlink($file);
    echo json_encode(['status'=>'success']);
} else {
    echo json_encode(['status'=>'not_found']);
}