<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
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


$mapel = $_POST['mapel'] ?? '';

$id_admin = $_SESSION['admin_id'] ?? 0;
$role     = $_SESSION['role'] ?? '';

$filter_owner = "";
if ($role != 'admin') {
$filter_owner = "AND FIND_IN_SET('$id_admin', id_pembuat)";
}

$q=mysqli_query($koneksi,"
SELECT kode_soal 
FROM soal 
WHERE mapel='$mapel'
$filter_owner
ORDER BY kode_soal ASC
");

echo "<option value=''>-- Pilih Kode Soal --</option>";

while($d=mysqli_fetch_assoc($q)){
echo "<option value='{$d['kode_soal']}'>{$d['kode_soal']}</option>";
}