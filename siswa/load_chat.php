<?php
session_start();
include '../koneksi/koneksi.php';

$isAdmin = isset($_SESSION['admin_id']);
$isSiswa = isset($_SESSION['siswa_id']);

if (!$isAdmin && !$isSiswa) {
    http_response_code(403);
    exit('Akses ditolak');
}

// Identitas pengguna yang login
$my_id = $isAdmin ? $_SESSION['admin_id'] : $_SESSION['siswa_id'];
$my_role = $isAdmin ? 'admin' : 'siswa';

// Ambil 50 pesan terbaru (admin dan siswa)
$q_chat = mysqli_query($koneksi, "
    SELECT c.*,
       CASE
           WHEN c.role = 'siswa' THEN s.nama_siswa
           WHEN c.role = 'admin' THEN a.nama_admin
       END AS nama_pengirim
FROM (
    SELECT * FROM chat WHERE deleted = 0 ORDER BY waktu DESC LIMIT 50
) c
LEFT JOIN siswa s 
    ON c.role = 'siswa' AND c.id_user = s.id_siswa
LEFT JOIN admins a
    ON c.role = 'admin' AND c.id_user = a.id
ORDER BY c.waktu ASC

");


while ($chat = mysqli_fetch_assoc($q_chat)) {
    $isMe = ($chat['id_user'] == $my_id && $chat['role'] == $my_role);
    $additionalClass = '';

    if ($chat['role'] === 'admin') {
        $additionalClass = ' admin';
    } else if ($chat['role'] === 'siswa') {
        $additionalClass = ' siswa';
    }

    echo '<div class="chat-line ' . ($isMe ? 'right' : 'left') . $additionalClass . '">';

// Nama pengirim + centang jika admin
echo '<small class="chat-sender">';

// Tampilkan icon user-circle di kiri jika bukan kamu atau admin
if ($chat['role'] === 'siswa' && !$isMe) {
    echo '<i class="fas fa-user-circle" style="color: grey; margin-right: 4px;"></i>';
}

// Admin juga icon user-circle di kiri
if ($chat['role'] === 'admin') {
    echo '<i class="fas fa-user-circle" style="color: grey; margin-right: 4px;"></i>';
}

// Nama pengirim
echo htmlspecialchars($chat['nama_pengirim']);

// Jika admin, tampilkan centang di kanan
if ($chat['role'] === 'admin') {
    echo ' <i class="fas fa-check-circle text-primary" title="Admin"></i>';
}

// Jika siswa dan ini adalah pesan dari diri sendiri, tampilkan icon user di kanan
if ($chat['role'] === 'siswa' && $isMe) {
    echo ' <i class="fas fa-user-circle" style="color: grey; margin-left: 4px;"></i>';
}

echo '</small>';



// Isi pesan
echo '<div class="chat-message">';
echo nl2br(htmlspecialchars($chat['pesan']));
echo '<span class="chat-timestamp">' . date('H:i', strtotime($chat['waktu'])) . '</span>';
echo '</div>';

$roleSession = $_SESSION['role'] ?? 'siswa'; // admin / editor / siswa
$myAdminId   = $_SESSION['admin_id'] ?? 0;
$mySiswaId   = $_SESSION['siswa_id'] ?? 0;

$selisih = time() - strtotime($chat['waktu']);
$bisa_hapus = false;

// ✅ ADMIN boleh hapus semua chat kapan saja
if ($roleSession === 'admin') {
    $bisa_hapus = true;
}

// ✅ EDITOR hanya boleh hapus chat miliknya sendiri (chat admin) <=15 detik
elseif ($roleSession === 'editor') {
    if (
        $chat['role'] === 'admin' &&
        $chat['id_user'] == $myAdminId &&
        $selisih <= 15
    ) {
        $bisa_hapus = true;
    }
}

// ✅ SISWA hanya boleh hapus chat miliknya sendiri <=15 detik
elseif ($chat['role'] === 'siswa') {
    if (
        $chat['id_user'] == $mySiswaId &&
        $selisih <= 15
    ) {
        $bisa_hapus = true;
    }
}

if ($bisa_hapus) {
    echo '<br><a href="#" class="delete-chat" data-id="' . $chat['id'] . '" title="Hapus pesan">';
    echo '<small style="font-size:10px;"><i class="fas fa-close"></i> Hapus</small></a>';
}

echo '</div>';

}

?>
