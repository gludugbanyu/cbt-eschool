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


$kode=$_POST['kode'];

$q=mysqli_query($koneksi,"
SELECT DISTINCT CONCAT(s.kelas,s.rombel) as r
FROM nilai n
JOIN siswa s ON n.id_siswa=s.id_siswa
WHERE n.kode_soal='$kode'
");

echo "<option value=''>-- Pilih Kelas --</option>";
while($d=mysqli_fetch_assoc($q)){
echo "<option value='{$d['r']}'>{$d['r']}</option>";
}