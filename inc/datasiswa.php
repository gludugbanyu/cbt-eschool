<?php
include '../koneksi/koneksi.php';

if (!isset($_SESSION['siswa_logged_in']) || $_SESSION['siswa_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$id_siswa = $_SESSION['siswa_id'];

$query = "SELECT * FROM siswa WHERE id_siswa = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id_siswa);
if (mysqli_stmt_execute($stmt)) {
    $result = mysqli_stmt_get_result($stmt);
    if ($user = mysqli_fetch_assoc($result)) {
        $nama_siswa = $user['nama_siswa'];
        $kelas_siswa = $user['kelas'];
        $rombel_siswa = $user['rombel'];
        $id_siswa = $user['id_siswa'];

        $_SESSION['kelas']  = $kelas_siswa;
$_SESSION['rombel'] = $rombel_siswa;
        // Cek apakah dipaksa logout oleh admin
        if (!empty($user['force_logout'])) {
            mysqli_query($koneksi, "UPDATE siswa SET session_token = NULL, force_logout = FALSE WHERE id_siswa = $id_siswa");
            session_unset();
            session_destroy();
            header("Location: login.php?status=forced");
            exit;
        }

        $settings = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT login_ganda FROM pengaturan WHERE id = 1"));
        $allow_multiple = ($settings['login_ganda'] == 'izinkan');

        if (!$allow_multiple && (!isset($_SESSION['siswa_token']) || $user['session_token'] !== $_SESSION['siswa_token'])) {
            session_unset();
            session_destroy();
            header("Location: login.php?status=multi");
            exit;
        }
    } else {
        echo "Siswa tidak ditemukan.";
        exit;
    }
} else {
    echo "Error executing query.";
    exit;
}
?>