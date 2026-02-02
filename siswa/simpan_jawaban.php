<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/datasiswa.php'; // pastikan ada $id_siswa dan $nama_siswa


function allBenarSalah($arr) {
    if (!is_array($arr)) return false;
    foreach ($arr as $v) {
        $v = strtolower(trim($v));
        if (!in_array($v, ['benar', 'salah'])) return false;
    }
    return true;
}

// Ambil data POST
$kode_soal = mysqli_real_escape_string($koneksi, $_POST['kode_soal'] ?? '');
$q_opsi = mysqli_query($koneksi, "SELECT jumlah_opsi FROM soal WHERE kode_soal='$kode_soal'");
$data_opsi = mysqli_fetch_assoc($q_opsi);
$jumlah_opsi = (int)($data_opsi['jumlah_opsi'] ?? 4);

$waktu_sisa = ceil(((int)($_POST['waktu_sisa'] ?? 0)) / 60);
$jawaban = $_POST['jawaban'] ?? [];
$soal_kiri = $_POST['soal_kiri'] ?? [];

if (!$kode_soal || empty($jawaban)) {
    $_SESSION['warning_message'] = 'Kode soal Tidak Tersedia';
    header('Location: ujian.php');
    exit;
}

// Format jawaban jadi string [nomor:jawaban]
$format_jawaban = [];
foreach ($jawaban as $nomor => $nilai) {
    $nomor = (int)$nomor;
    if (isset($soal_kiri[$nomor]) && is_array($soal_kiri[$nomor]) && is_array($nilai)) {
        // Menjodohkan
        $pasangan = [];
        foreach ($nilai as $kiri => $kanan) {
            $kiri = trim($kiri);
            $kanan = trim($kanan);
            if ($kiri !== '' && $kanan !== '') {
                $pasangan[] = "$kiri:$kanan";
            }
        }
        $format_jawaban[] = "[$nomor:" . implode('|', $pasangan) . "]";
        continue;
    }
    if (allBenarSalah($nilai)) {
        // Benar/Salah array
        $format_jawaban[] = "[$nomor:" . implode('|', array_map('trim', $nilai)) . "]";
        continue;
    }
    if (is_array($nilai)) {
        // Pilihan ganda kompleks
        $format_jawaban[] = "[$nomor:" . implode(',', array_map('trim', $nilai)) . "]";
        continue;
    }
    // PG tunggal / uraian
    $format_jawaban[] = "[$nomor:" . trim($nilai) . "]";
}
$final_jawaban = implode('', $format_jawaban);

if (empty($final_jawaban)) {
    die(json_encode(['status' => 'error', 'message' => 'Tidak ada jawaban valid']));
}

// Simpan jawaban siswa
$sql = "INSERT INTO jawaban_siswa (id_siswa, kode_soal, jawaban_siswa, waktu_sisa, nama_siswa, status_ujian)
        VALUES (?, ?, ?, ?, ?, 'Aktif')
        ON DUPLICATE KEY UPDATE 
            jawaban_siswa = VALUES(jawaban_siswa),
            waktu_sisa = VALUES(waktu_sisa),
            nama_siswa = VALUES(nama_siswa),
            status_ujian = 'Aktif'";
$stmt = mysqli_prepare($koneksi, $sql);
if (!$stmt) die(json_encode(['status' => 'error', 'message' => 'Prepare simpan jawaban gagal: ' . mysqli_error($koneksi)]));
mysqli_stmt_bind_param($stmt, "sssis", $id_siswa, $kode_soal, $final_jawaban, $waktu_sisa, $nama_siswa);
if (!mysqli_stmt_execute($stmt)) die(json_encode(['status' => 'error', 'message' => 'Execute simpan jawaban gagal: ' . mysqli_error($koneksi)]));
mysqli_stmt_close($stmt);

// Update last_activity siswa
$now = date('Y-m-d H:i:s');
$stmt = mysqli_prepare($koneksi, "UPDATE siswa SET last_activity = ? WHERE id_siswa = ?");
if (!$stmt) die(json_encode(['status' => 'error', 'message' => 'Prepare update last_activity gagal: ' . mysqli_error($koneksi)]));
mysqli_stmt_bind_param($stmt, "ss", $now, $id_siswa);
if (!mysqli_stmt_execute($stmt)) die(json_encode(['status' => 'error', 'message' => 'Execute update last_activity gagal: ' . mysqli_error($koneksi)]));
mysqli_stmt_close($stmt);

// Ambil kunci jawaban soal
$q_soal = mysqli_query($koneksi, "SELECT kunci FROM soal WHERE kode_soal='$kode_soal'");
$data_soal = mysqli_fetch_assoc($q_soal);
$kunci_jawaban = $data_soal['kunci'] ?? '';

if (empty($kunci_jawaban)) die(json_encode(['status' => 'error', 'message' => 'Kunci jawaban kosong']));

// Fungsi hapus koma di luar tanda []
function removeCommasOutsideBrackets($str) {
    $result = '';
    $in_brackets = false;
    for ($i = 0; $i < strlen($str); $i++) {
        $char = $str[$i];
        if ($char === '[') $in_brackets = true;
        if ($char === ']') $in_brackets = false;
        if ($char === ',' && !$in_brackets) continue;
        $result .= $char;
    }
    return $result;
}
$kuncifix = removeCommasOutsideBrackets($kunci_jawaban);

// Parsing kunci dan jawaban siswa
preg_match_all('/\[(.*?)\]/', $kuncifix, $kunci_matches);
preg_match_all('/\[(.*?)\]/', $final_jawaban, $jawaban_matches);

$kunci_array = $kunci_matches[1];
$jawaban_array = $jawaban_matches[1];

$jawaban_siswa_arr = [];
foreach ($jawaban_array as $item) {
    if (strpos($item, ':') !== false) {
        list($nomer_jawab, $isi_jawab) = explode(':', $item, 2);
        $jawaban_siswa_arr[$nomer_jawab] = $isi_jawab;
    }
}

$total_soal = count($kunci_array);
$benar = 0;
$salah = 0;
$kurang_lengkap = 0;
$nilai_total = 0;
$nilai_per_soal = $total_soal > 0 ? 100 / $total_soal : 0;

$ada_uraian = false;

for ($i = 0; $i < $total_soal; $i++) {
    list($nomer_kunci, $isi_kunci) = explode(':', $kunci_array[$i], 2);
    $isi_jawaban = $jawaban_siswa_arr[$nomer_kunci] ?? '';

    // Ambil tipe soal
    $q_tipe = mysqli_query($koneksi, "SELECT tipe_soal FROM butir_soal WHERE kode_soal = '$kode_soal' AND nomer_soal = '$nomer_kunci'");
    $data_tipe = mysqli_fetch_assoc($q_tipe);
    $tipe_soal = strtolower($data_tipe['tipe_soal'] ?? '');

    $skor = 0;

    if (in_array($tipe_soal, ['benar/salah', 'menjodohkan'])) {
        $kunci_opsi = array_map('trim', explode('|', $isi_kunci));
$jawaban_opsi = array_map('trim', explode('|', $isi_jawaban));
        $jumlah_kunci = count($kunci_opsi);
        $nilai_per_opsi = $nilai_per_soal / $jumlah_kunci;
        $jumlah_benar = 0;

        for ($j = 0; $j < $jumlah_kunci; $j++) {
            if (isset($jawaban_opsi[$j]) && $kunci_opsi[$j] === $jawaban_opsi[$j]) {
                $jumlah_benar++;
            }
        }

        $skor = $jumlah_benar * $nilai_per_opsi;

        if ($jumlah_benar == $jumlah_kunci) $benar++;
        elseif ($jumlah_benar == 0) $salah++;
        else $kurang_lengkap++;

    } elseif ($tipe_soal === 'pilihan ganda kompleks') {
        $kunci_opsi = array_map('trim', explode(',', str_replace('|', ',', $isi_kunci)));
        $jawaban_opsi = array_map('trim', explode(',', str_replace('|', ',', $isi_jawaban)));
        $jumlah_kunci = count($kunci_opsi);
        $jumlah_benar = 0;

        foreach ($jawaban_opsi as $jawab) {
            if (!in_array($jawab, $kunci_opsi)) {
                // Ada salah → skor 0
                $skor = 0;
                $salah++;
                goto selesai_pilgan_kompleks;
            }
        }

        foreach ($jawaban_opsi as $jawab) {
            if (in_array($jawab, $kunci_opsi)) $jumlah_benar++;
        }

        if ($jumlah_benar === $jumlah_kunci) {
            $skor = $nilai_per_soal;
            $benar++;
        } else {
            $skor = ($jumlah_benar / $jumlah_kunci) * $nilai_per_soal;
            $kurang_lengkap++;
        }

        selesai_pilgan_kompleks:
        ;

    } else {
        // PG tunggal atau uraian
        if ($tipe_soal === 'uraian') {
            $skor = 0;
            $kurang_lengkap++;
            $ada_uraian = true;
        } elseif (strtolower(trim($isi_kunci)) === strtolower(trim($isi_jawaban))) {
            $skor = $nilai_per_soal;
            $benar++;
        } elseif (empty($isi_jawaban)) {
            $kurang_lengkap++;
        } else {
            $salah++;
        }
    }

    $nilai_total += $skor;
}

$status_penilaian = $ada_uraian ? 'perlu_dinilai' : 'selesai';

$tanggal_ujian = date('Y-m-d H:i:s');

$q_nilai = mysqli_query($koneksi, "SELECT * FROM nilai WHERE id_siswa='$id_siswa' AND kode_soal='$kode_soal'");

$final_jawaban_esc = mysqli_real_escape_string($koneksi, $final_jawaban);
$kunci_esc = mysqli_real_escape_string($koneksi, $kunci_jawaban);
$nama_siswa_esc = mysqli_real_escape_string($koneksi, $nama_siswa);

if (mysqli_num_rows($q_nilai) > 0) {
    $sql_upd = "UPDATE nilai SET 
                nama_siswa = '$nama_siswa_esc',
                total_soal = '$total_soal',
                jawaban_benar = '$benar',
                jawaban_salah = '$salah',
                jawaban_kurang = '$kurang_lengkap',
                jawaban_siswa = '$final_jawaban_esc',
                kunci = '$kunci_esc',
                nilai = '$nilai_total',
                tanggal_ujian = '$tanggal_ujian',
                status_penilaian = '$status_penilaian'
                WHERE id_siswa = '$id_siswa' AND kode_soal = '$kode_soal'";
    if (!mysqli_query($koneksi, $sql_upd)) die(json_encode(['status' => 'error', 'message' => 'Update nilai gagal: ' . mysqli_error($koneksi)]));
} else {
    $sql_ins = "INSERT INTO nilai 
                (id_siswa, nama_siswa, kode_soal, total_soal, jawaban_benar, jawaban_salah, jawaban_kurang, jawaban_siswa, kunci, nilai, tanggal_ujian, status_penilaian)
                VALUES
                ('$id_siswa', '$nama_siswa_esc', '$kode_soal', '$total_soal', '$benar', '$salah', '$kurang_lengkap', '$final_jawaban_esc', '$kunci_esc', '$nilai_total', '$tanggal_ujian', '$status_penilaian')";
    if (!mysqli_query($koneksi, $sql_ins)) die(json_encode(['status' => 'error', 'message' => 'Insert nilai gagal: ' . mysqli_error($koneksi)]));
}

// Update status ujian jadi Selesai (jika diperlukan)
mysqli_query($koneksi, "UPDATE jawaban_siswa SET status_ujian = 'Selesai' WHERE id_siswa='$id_siswa' AND kode_soal='$kode_soal'");
// Siapkan query update dengan prepared statement
$stmt = $koneksi->prepare("UPDATE jawaban_siswa SET status_ujian = 'Selesai' WHERE id_siswa = ? AND kode_soal = ?");
if (!$stmt) {
    die("Prepare gagal: " . $koneksi->error);
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simpan Jawaban</title>
    <?php include '../inc/css.php'; ?>
    <style>
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
</head>

<body>
    <div id="loadingOverlay" style="
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: rgba(255, 255, 255, 0.9);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    font-family: Arial, sans-serif;
    color: #333;
">
    <div class="loader" style="
        border: 5px solid #f3f3f3;
        border-top: 5px solid #3498db;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
        margin-bottom: 20px;
    "></div>
    <p style="font-size: 18px;">Menghubungi server pusat...</p>
    <p style="font-size: 16px;">Menyimpan data ke server pusat</p>
</div>
    <script src="../assets/adminkit/static/js/app.js"></script>
    <script src="../assets/js/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/sweetalert.js"></script>
    <script src="../assets/datatables/datatables.js"></script>
    <?php include '../inc/check_activity.php'; ?>
    <script>
    // Tampilkan animasi loading selama 2 detik, lalu munculkan SweetAlert
    $(document).ready(function() {
        setTimeout(function() {
            // Sembunyikan loading overlay
            $('#loadingOverlay').fadeOut(500, function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Jawaban sudah tersimpan',
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    window.location.href = 'dashboard.php';
                });
            });
        }, 2000); // Waktu delay animasi 2 detik
    });
</script>

</body>

</html>