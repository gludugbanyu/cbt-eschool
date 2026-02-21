<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
include '../inc/dataadmin.php';

header('Content-Type: application/json');

// Ambil versi aplikasi dari database
$q = mysqli_query($koneksi, "SELECT versi_aplikasi FROM pengaturan WHERE id = 1");
$dataDb = mysqli_fetch_assoc($q);
$versi_saat_ini = $dataDb['versi_aplikasi'] ?? '0.0.0';

// Ambil versi terbaru dari GitHub Release
$url = 'https://api.github.com/repos/gludugbanyu/cbt-eschool/releases/latest';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'CBT-Update-Agent');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response = curl_exec($ch);
curl_close($ch);

if (!$response) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Tidak dapat terhubung ke GitHub'
    ]);
    exit;
}

$data = json_decode($response, true);

$versi_tag   = $data['tag_name'] ?? '';
$versi_baru  = ltrim($versi_tag, 'v');
$changelog   = $data['body'] ?? '';
$download_url = "https://github.com/gludugbanyu/cbt-eschool/archive/refs/tags/{$versi_tag}.zip";

if (version_compare($versi_baru, $versi_saat_ini, '>')) {
    echo json_encode([
        'status' => 'update',
        'versi_saat_ini' => $versi_saat_ini,
        'versi_baru' => $versi_baru,
        'changelog' => nl2br($changelog),
        'download_url' => $download_url
    ]);
} else {
    echo json_encode([
        'status' => 'uptodate',
        'versi_saat_ini' => $versi_saat_ini,
        'versi_baru' => $versi_baru
    ]);
}