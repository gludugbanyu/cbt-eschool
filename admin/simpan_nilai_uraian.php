<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
include '../inc/dataadmin.php';
$id_siswa = $_POST['id_siswa'] ?? '';
$kode_soal = $_POST['kode_soal'] ?? '';
$nilai_per_soal = $_POST['nilai'] ?? [];
only_preview_soal_by_kode($kode_soal);
// Validasi input
if(empty($id_siswa) || empty($kode_soal)) {
    die(json_encode(['status' => 'error', 'message' => 'Data tidak valid']));
}

// Format detail uraian
$detail = [];
foreach($nilai_per_soal as $nomer => $nilai){
    // Validasi nilai
    $nilai_bersih = number_format((float)$nilai, 2, '.', '');
    $detail[] = "[{$nomer}:{$nilai_bersih}]";
}

$detail_uraian = implode('', $detail);
$total_nilai_uraian = array_sum($nilai_per_soal);

// Update database
$query = mysqli_query($koneksi, 
    "UPDATE nilai SET 
        nilai_uraian = '$total_nilai_uraian',
        detail_uraian = '$detail_uraian'
     WHERE id_siswa = '$id_siswa' 
     AND kode_soal = '$kode_soal'");

if($query) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Nilai berhasil disimpan',
        'detail' => $detail_uraian,
        'total' => number_format($total_nilai_uraian, 2)
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal menyimpan: ' . mysqli_error($koneksi)
    ]);
}
?>