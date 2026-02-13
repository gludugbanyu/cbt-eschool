<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: soal.php');
    exit();
}

// Ambil kode soal dan data JSON dari form POST
$kode_soal = mysqli_real_escape_string($koneksi, $_POST['kode_soal']);
$data_json = $_POST['data_json'];

// Decode JSON yang dikirim dari halaman preview
$soal_list = json_decode($data_json, true);
$target_dir = "../gambar/";

// Pastikan folder gambar tersedia
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

if (empty($soal_list)) {
    $_SESSION['alert_error'] = "Data soal tidak ditemukan atau format tidak valid.";
    header("Location: soal.php");
    exit();
}

/**
 * FUNGSI: Mencari string Base64 di dalam teks, menyimpannya sebagai file,
 * dan mengganti string tersebut dengan tag HTML <img> dengan path ../gambar/
 */
function prosesTeksDanGambar($koneksi, $teks, $kode_soal, $target_dir) {
    if (empty($teks)) return "";

    // Regex untuk menangkap marker [IMG_BASE64:...]
    $pattern = '/\[IMG_BASE64:data:image\/(\w+);base64,([^\]]+)\]/';

    if (preg_match_all($pattern, $teks, $matches)) {
        foreach ($matches[0] as $index => $full_marker) {
            $extension = $matches[1][$index]; 
            $base64_data = $matches[2][$index];
            $decoded_file = base64_decode($base64_data);
            
            // Generate nama file unik
            $nama_file = "IMG_" . $kode_soal . "_" . uniqid() . "." . $extension;
            $path_full = $target_dir . $nama_file;
            
            // Simpan file ke folder fisik
            if (file_put_contents($path_full, $decoded_file)) {
                // Gunakan path relatif ../ agar gambar muncul di halaman admin/user
                $url_database = "../gambar/" . $nama_file; 
                
                $html_img = "<br><img src=\"$url_database\">";
                $teks = str_replace($full_marker, $html_img, $teks);
            }
        }
    }
    
    // Bersihkan sisa marker jika ada proses yang gagal
    $teks = preg_replace('/\[IMG_BASE64:.*?\]/', '', $teks);
    
    return mysqli_real_escape_string($koneksi, trim($teks));
}

$berhasil = 0;
$gagal = 0;

$map_tipe = [
    'PG'  => 'Pilihan Ganda',
    'PGX' => 'Pilihan Ganda Kompleks',
    'BS'  => 'Benar/Salah',
    'U'   => 'Uraian',
    'MJD' => 'Menjodohkan'
];

$map_kunci = [
    'A' => 'pilihan_1', 
    'B' => 'pilihan_2', 
    'C' => 'pilihan_3', 
    'D' => 'pilihan_4', 
    'E' => 'pilihan_5'
];

foreach ($soal_list as $s) {
    $nomer = mysqli_real_escape_string($koneksi, $s['nomer_soal']);
    $tipe_raw = strtoupper($s['tipe_soal']);
    $tipe_db = $map_tipe[$tipe_raw] ?? 'Pilihan Ganda';
    $jawaban_final = trim($s['jawaban_benar']);

    // --- 1. PROSES PERTANYAAN ---
    $teks_pertanyaan = $s['pertanyaan'];
    if (!empty($s['gambar_base64'])) {
        $teks_pertanyaan .= " [IMG_BASE64:" . $s['gambar_base64'] . "]";
    }
    $pertanyaan_final = prosesTeksDanGambar($koneksi, $teks_pertanyaan, $kode_soal, $target_dir);

    // --- 2. PROSES OPSI JAWABAN ---
    $p_1 = prosesTeksDanGambar($koneksi, ($s['pilihan']['A'] ?? ($s['pilihan'][1] ?? '')), $kode_soal, $target_dir);
    $p_2 = prosesTeksDanGambar($koneksi, ($s['pilihan']['B'] ?? ($s['pilihan'][2] ?? '')), $kode_soal, $target_dir);
    $p_3 = prosesTeksDanGambar($koneksi, ($s['pilihan']['C'] ?? ($s['pilihan'][3] ?? '')), $kode_soal, $target_dir);
    $p_4 = prosesTeksDanGambar($koneksi, ($s['pilihan']['D'] ?? ($s['pilihan'][4] ?? '')), $kode_soal, $target_dir);
    $p_5 = prosesTeksDanGambar($koneksi, ($s['pilihan']['E'] ?? ($s['pilihan'][5] ?? '')), $kode_soal, $target_dir);

    // --- 3. LOGIKA KUNCI JAWABAN ---
    if ($tipe_db == 'Menjodohkan') {
        $kunci_mentah = explode('|', $jawaban_final);
        $hasil_mapping = [];
        foreach ($kunci_mentah as $item) {
            $parts = explode(':', $item);
            if (count($parts) == 2) {
                $label_kiri = trim($parts[0]);
                $label_kanan = trim($parts[1]);
                $t_kiri = isset($s['pilihan'][$label_kiri]) ? strip_tags(preg_replace('/\[IMG_BASE64:.*?\]/', '', $s['pilihan'][$label_kiri])) : $label_kiri;
                $t_kanan = isset($s['opsi_angka'][$label_kanan]) ? strip_tags($s['opsi_angka'][$label_kanan]) : $label_kanan;
                $hasil_mapping[] = trim($t_kiri) . ":" . trim($t_kanan);
            }
        }
        $jawaban_final = implode('|', $hasil_mapping);
    } 
    elseif ($tipe_db == 'Pilihan Ganda') {
        $jawaban_final = $map_kunci[$jawaban_final] ?? $jawaban_final;
    } 
    elseif ($tipe_db == 'Pilihan Ganda Kompleks') {
        $kunci_array = explode(',', $jawaban_final);
        $new_kunci = [];
        foreach ($kunci_array as $k) {
            $k = trim($k);
            if (isset($map_kunci[$k])) $new_kunci[] = $map_kunci[$k];
        }
        $jawaban_final = implode(',', $new_kunci);
    }

    $jawaban_sql = mysqli_real_escape_string($koneksi, $jawaban_final);

    // --- 4. EKSEKUSI INSERT ---
    $query = "INSERT INTO butir_soal (
                nomer_soal, kode_soal, pertanyaan, tipe_soal, 
                pilihan_1, pilihan_2, pilihan_3, pilihan_4, pilihan_5, 
                jawaban_benar, status_soal
              ) VALUES (
                '$nomer', '$kode_soal', '$pertanyaan_final', '$tipe_db', 
                '$p_1', '$p_2', '$p_3', '$p_4', '$p_5', 
                '$jawaban_sql', 'Aktif'
              )";

    if ($koneksi->query($query)) {
        $berhasil++;
    } else {
        $gagal++;
    }
}

// Notifikasi Akhir
if ($gagal == 0) {
    $_SESSION['alert_success'] = "Berhasil mengimpor $berhasil soal dan mengunggah gambar terkait.";
} else {
    $_SESSION['alert_info'] = "Proses selesai. Berhasil: $berhasil, Gagal: $gagal.";
}

header("Location: daftar_butir_soal.php?kode_soal=$kode_soal");
exit();