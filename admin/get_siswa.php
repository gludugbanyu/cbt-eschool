<?php
session_start();
include '../inc/functions.php';

// 🔒 wajib login admin
check_login_api('admin');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../admin/dashboard.php?notallowed=1");
    exit;
}
include '../koneksi/koneksi.php';

$kelas = $_POST['kelas'];
$rombel = $_POST['rombel'];

$siswaQuery = mysqli_query($koneksi, "SELECT * FROM siswa WHERE kelas='$kelas' AND rombel='$rombel' ORDER BY nama_siswa ASC");
$options = "";
while($siswa = mysqli_fetch_assoc($siswaQuery)){
    $options .= "<option value='{$siswa['nama_siswa']}'>{$siswa['nama_siswa']}</option>";
}
echo $options;
?>
