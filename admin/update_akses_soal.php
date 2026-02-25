<?php
session_start();
include '../inc/functions.php';

// 🔒 wajib login admin
check_login_api('admin');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    if (!is_ajax_request()) {
        header("Location: ../admin/dashboard.php?notallowed=1");
        exit;
    }

    http_response_code(405);
    echo 'SESSION_EXPIRED';
    exit;
}
include '../koneksi/koneksi.php';
$id_soal = $_POST['id_soal'];
$ids     = $_POST['ids'];

mysqli_query($koneksi,"UPDATE soal SET id_pembuat='$ids' WHERE id_soal='$id_soal'");

echo json_encode(['status'=>'ok']);
