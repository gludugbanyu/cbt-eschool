<?php
session_start();
header('Content-Type: application/json');

include '../koneksi/koneksi.php';

// Validasi login (khusus AJAX)
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Silakan login ulang.']);
    exit;
}

// Validasi role
if (($_SESSION['role'] ?? '') !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Hanya admin yang boleh menghapus chat.']);
    exit;
}

// Validasi method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Metode tidak diizinkan.']);
    exit;
}

// Hapus chat
if (mysqli_query($koneksi, "DELETE FROM chat")) {
    echo json_encode(['status' => 'ok', 'message' => 'Semua chat telah dihapus.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus chat.']);
}
exit;
