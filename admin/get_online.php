<?php
session_start();
include '../inc/functions.php';

// 🔥 WAJIB ADMIN LOGIN
check_login_api('admin');

include '../koneksi/koneksi.php';

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 12;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$threshold = date('Y-m-d H:i:s', strtotime('-5 minutes'));

// Query dasar dengan pencarian
$base_query = "FROM siswa WHERE last_activity >= '$threshold'";
if (!empty($search)) {
    $search = mysqli_real_escape_string($koneksi, $search);
    $base_query .= " AND (nama_siswa LIKE '%$search%' OR kelas LIKE '%$search%' OR rombel LIKE '%$search%')";
}

// Hitung total data
$count_query = "SELECT COUNT(*) as total $base_query";
$count_result = mysqli_query($koneksi, $count_query);
$total = mysqli_fetch_assoc($count_result)['total'];

// Ambil data
$query = "SELECT id_siswa, nama_siswa, kelas, rombel, last_activity, page_url $base_query 
          ORDER BY nama_siswa ASC 
          LIMIT $limit OFFSET $offset";
$result = mysqli_query($koneksi, $query);

$data = [];
$nomor = $offset + 1;
function formatTanggalIndonesia($datetime) {
    if (!$datetime) return '-';
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $timestamp = strtotime($datetime);
    $tanggal = date('d', $timestamp);
    $bulan_id = $bulan[(int)date('m', $timestamp)];
    $tahun = date('Y', $timestamp);
    $jam_menit = date('H:i', $timestamp);

    return "$jam_menit, $tanggal $bulan_id $tahun";
}
while ($row = mysqli_fetch_assoc($result)) {
    $is_online = isset($row['last_activity']) && $row['last_activity'] >= $threshold;
    $status = $is_online 
        ? '<i class="fa fa-circle blinking" style="color:green;font-size:10px;" aria-hidden="true"></i>' 
        : '<i class="fa fa-circle blinking" style="color:red;" aria-hidden="true"></i>';

    $data[] = [
        $nomor++,
        htmlspecialchars($row['nama_siswa']),
        htmlspecialchars($row['kelas']),
        htmlspecialchars($row['rombel']),
        formatTanggalIndonesia($row['last_activity']),
        htmlspecialchars($row['page_url']),
        $status,
        $row['id_siswa']
    ];
}

echo json_encode([
    'data' => $data,
    'total' => $total,
    'search' => $search
]);
?>