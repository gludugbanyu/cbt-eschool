<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';  
check_login('admin');

// ⛔ KUNCI DI SINI
$oldKode = mysqli_real_escape_string($koneksi, $_POST['old_kode'] ?? '');
only_pemilik_soal_by_kode($oldKode);
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Metode request tidak valid']);
    exit;
}

$newKode = mysqli_real_escape_string($koneksi, $_POST['new_kode']);

// Validasi kode baru
$checkKode = mysqli_query($koneksi, "SELECT kode_soal FROM soal WHERE kode_soal = '$newKode'");
if (mysqli_num_rows($checkKode) > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Kode soal baru sudah digunakan']);
    exit;
}

mysqli_begin_transaction($koneksi);

try {
    // 1. Duplikasi data soal utama
    $soalAsal = mysqli_query($koneksi, 
        "SELECT * FROM soal WHERE kode_soal = '$oldKode'"
    );
    
    if (mysqli_num_rows($soalAsal) === 0) {
        throw new Exception("Soal asal tidak ditemukan");
    }
    
    $dataSoal = mysqli_fetch_assoc($soalAsal);
    
    // Validasi status
    if ($dataSoal['status'] === 'Aktif') {
        throw new Exception("Tidak bisa duplikat soal aktif");
    }

    // Hapus ID untuk memungkinkan auto increment
    unset($dataSoal['id_soal']);
    
    // Update data baru
    $dataSoal['kode_soal'] = $newKode;
    $dataSoal['nama_soal'] .= ' (Copy)';
    $dataSoal['status'] = 'Nonaktif';
    $dataSoal['tanggal'] = date('Y-m-d');
    
    // Insert soal baru
    $columns = array_keys($dataSoal);
    $values = array_map(function($v) use ($koneksi) {
        return "'" . mysqli_real_escape_string($koneksi, $v) . "'";
    }, $dataSoal);
    
    mysqli_query($koneksi, 
        "INSERT INTO soal (" . implode(',', $columns) . ") 
        VALUES (" . implode(',', $values) . ")"
    ) or throw new Exception("Gagal duplikasi soal: " . mysqli_error($koneksi));
    
    $newIdSoal = mysqli_insert_id($koneksi);

    // 2. Duplikasi butir soal TANPA menyertakan id_soal
    $butirAsal = mysqli_query($koneksi, 
    "SELECT 
        nomer_soal,
        pertanyaan,
        tipe_soal,
        pilihan_1,
        pilihan_2,
        pilihan_3,
        pilihan_4,
        pilihan_5,
        jawaban_benar,
        status_soal
    FROM butir_soal 
    WHERE kode_soal = '$oldKode'"
);
    
    $counter = 1;
    while ($rowButir = mysqli_fetch_assoc($butirAsal)) {
        // Update data relasi
        $rowButir['kode_soal'] = $newKode;
        $rowButir['nomer_soal'] = $counter++;
        
        // Insert butir baru TANPA kolom id_soal
        $columnsButir = array_keys($rowButir);
        $valuesButir = array_map(function($v) use ($koneksi) {
            return "'" . mysqli_real_escape_string($koneksi, $v) . "'";
        }, $rowButir);
        
        mysqli_query($koneksi, 
            "INSERT INTO butir_soal (" . implode(',', $columnsButir) . ") 
            VALUES (" . implode(',', $valuesButir) . ")"
        ) or throw new Exception("Gagal duplikasi butir: " . mysqli_error($koneksi));
    }

    mysqli_commit($koneksi);
    echo json_encode(['status' => 'success', 'message' => 'Duplikasi berhasil']);

} catch (Exception $e) {
    mysqli_rollback($koneksi);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>