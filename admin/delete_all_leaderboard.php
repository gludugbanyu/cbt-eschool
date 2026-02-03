<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
only_admin();

include '../inc/dataadmin.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Metode tidak diizinkan.']);
    exit;
}

// Eksekusi hapus semua chat
$query = mysqli_query($koneksi, "DELETE FROM skor_game");

if ($query) {
    echo json_encode(['status' => 'ok', 'message' => 'Semua data telah dihapus.']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data.']);
}
