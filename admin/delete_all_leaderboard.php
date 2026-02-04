<?php
session_start();
header('Content-Type: application/json');

include '../koneksi/koneksi.php';

// VALIDASI LOGIN KHUSUS AJAX
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Silakan login ulang.']);
    exit;
}

// VALIDASI ROLE
if (($_SESSION['role'] ?? '') !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Halaman tersebut hanya bisa diakses oleh Admin.']);
    exit;
}

// VALIDASI METHOD
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Metode tidak diizinkan.']);
    exit;
}

// HAPUS DATA
if (mysqli_query($koneksi, "DELETE FROM skor_game")) {
    echo json_encode(['status' => 'ok', 'message' => 'Semua data telah dihapus.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data.']);
}
exit;
