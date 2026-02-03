<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
only_admin();

$id = $_POST['id'] ?? 0;
$id = mysqli_real_escape_string($koneksi, $id);

// Cegah hapus diri sendiri
if ($id == ($_SESSION['admin_id'] ?? 0)) {
    $_SESSION['swal'] = [
        'icon'  => 'error',
        'title' => 'Ditolak!',
        'text'  => 'Anda tidak bisa menghapus akun sendiri!'
    ];
    header("Location: manajemen_user.php");
    exit;
}

// Cek role user yang akan dihapus
$qUser = mysqli_query($koneksi, "SELECT role FROM admins WHERE id='$id'");
$user = mysqli_fetch_assoc($qUser);

if (!$user) {
    header("Location: manajemen_user.php");
    exit;
}

// Pastikan minimal masih ada 1 admin
if ($user['role'] == 'admin') {
    $qAdmin = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM admins WHERE role='admin'");
    $totalAdmin = mysqli_fetch_assoc($qAdmin)['total'];

    if ($totalAdmin <= 1) {
        $_SESSION['swal'] = [
            'icon'  => 'error',
            'title' => 'Gagal!',
            'text'  => 'Minimal harus ada 1 admin di sistem!'
        ];
        header("Location: manajemen_user.php");
        exit;
    }
}

// ============================
// BERSIHKAN id_pembuat DI soal
// ============================
$qSoal = mysqli_query($koneksi, "SELECT kode_soal, id_pembuat FROM soal");

while ($s = mysqli_fetch_assoc($qSoal)) {
    $list = array_filter(array_map('trim', explode(',', $s['id_pembuat'])));

    if (in_array($id, $list)) {
        // Buang id yang dihapus
        $list = array_diff($list, [$id]);

        $baru = implode(',', $list);

        mysqli_query($koneksi, "
            UPDATE soal 
            SET id_pembuat='$baru' 
            WHERE kode_soal='{$s['kode_soal']}'
        ");
    }
}

// ============================
// HAPUS USER
// ============================
mysqli_query($koneksi, "DELETE FROM admins WHERE id='$id'");

$_SESSION['swal'] = [
    'icon'  => 'success',
    'title' => 'Berhasil!',
    'text'  => 'User berhasil dihapus dan akses soal dibersihkan.'
];

header("Location: manajemen_user.php");
exit;
