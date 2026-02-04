<?php
session_start();
include '../koneksi/koneksi.php';

$roleSession = $_SESSION['role'] ?? '';
$myAdminId   = $_SESSION['admin_id'] ?? 0;
$mySiswaId   = $_SESSION['siswa_id'] ?? 0;

$id = intval($_POST['id'] ?? 0);

// Ambil data chat
$q = mysqli_query($koneksi, "SELECT * FROM chat WHERE id='$id'");
$chat = mysqli_fetch_assoc($q);

if (!$chat) {
    http_response_code(404);
    exit('Chat tidak ditemukan');
}

$selisih = time() - strtotime($chat['waktu']);
$bisa_hapus = false;

// ✅ ADMIN bebas
if ($roleSession === 'admin') {
    $bisa_hapus = true;
}

// ✅ EDITOR: hanya chat admin miliknya <=15 detik
elseif ($roleSession === 'editor') {
    if (
        $chat['role'] === 'admin' &&
        $chat['id_user'] == $myAdminId &&
        $selisih <= 15
    ) {
        $bisa_hapus = true;
    }
}

// ✅ SISWA: hanya chat miliknya <=15 detik
elseif ($chat['role'] === 'siswa') {
    if (
        $chat['id_user'] == $mySiswaId &&
        $selisih <= 15
    ) {
        $bisa_hapus = true;
    }
}

if (!$bisa_hapus) {
    http_response_code(403);
    exit('Tidak punya hak menghapus pesan ini');
}

// Lolos validasi
mysqli_query($koneksi, "UPDATE chat SET deleted = 1 WHERE id='$id'");
echo 'ok';
