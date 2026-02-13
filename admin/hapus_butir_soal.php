<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');

if (!isset($_GET['id_soal'])) {
    header('Location: soal.php');
    exit();
}

$id_soal = mysqli_real_escape_string($koneksi, $_GET['id_soal']);

// 🔥 1. Ambil data lengkap (kode_soal dan semua kolom teks yang mungkin berisi gambar)
$qButir = mysqli_query($koneksi, "
    SELECT kode_soal, pertanyaan, pilihan_1, pilihan_2, pilihan_3, pilihan_4, pilihan_5 
    FROM butir_soal 
    WHERE id_soal='$id_soal'
");

if (mysqli_num_rows($qButir) == 0) {
    header('Location: soal.php?error=notfound');
    exit();
}

$dataButir = mysqli_fetch_assoc($qButir);
$kode_soal = $dataButir['kode_soal'];

// 🔥 2. Cek ownership
only_pemilik_soal_by_kode($kode_soal);

// 🔥 3. LOGIKA HAPUS GAMBAR FISIK
// Kita gabungkan semua teks dari pertanyaan dan semua pilihan jawaban
$gabungan_teks = $dataButir['pertanyaan'] . " " . 
                 $dataButir['pilihan_1'] . " " . 
                 $dataButir['pilihan_2'] . " " . 
                 $dataButir['pilihan_3'] . " " . 
                 $dataButir['pilihan_4'] . " " . 
                 $dataButir['pilihan_5'];

/**
 * Mencari pola tag img: src="../gambar/nama_file.ext"
 * Pattern ini akan menangkap semua file yang ada di dalam atribut src
 */
if (preg_match_all('/src="..\/gambar\/(.*?)"/', $gabungan_teks, $matches)) {
    // $matches[1] berisi array nama-nama file gambar yang ditemukan
    foreach ($matches[1] as $nama_file) {
        $path_gambar = "../gambar/" . $nama_file;

        // Hapus file dari folder jika file tersebut ada
        if (!empty($nama_file) && file_exists($path_gambar)) {
            unlink($path_gambar);
        }
    }
}

// 🔥 4. Hapus data butir dari database
$hapus = mysqli_query($koneksi, "
    DELETE FROM butir_soal 
    WHERE id_soal='$id_soal'
");

if ($hapus) {
    header('Location: daftar_butir_soal.php?kode_soal=' . urlencode($kode_soal) . '&success=hapus');
} else {
    header('Location: daftar_butir_soal.php?kode_soal=' . urlencode($kode_soal) . '&error=hapus');
}
exit();