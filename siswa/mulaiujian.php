<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('siswa');
include '../inc/datasiswa.php';

$kode_soal = $_POST['kode_soal'] ?? $_GET['kode_soal'] ?? '';
$token = $_POST['token'] ?? '';
$id_siswa = $_POST['id_siswa'] ?? $_SESSION['id_siswa'];


if (empty($kode_soal)) {
    $_SESSION['alert'] = true;
    $_SESSION['warning_message'] = 'Kode soal Tidak Tersedia';
    header('Location: ujian.php');
    exit;
}

// Ambil data siswa
$q_siswa = mysqli_query($koneksi, "SELECT * FROM siswa WHERE id_siswa = '$id_siswa'");
$data_siswa = mysqli_fetch_assoc($q_siswa);
// Ambil data soal
$q_soal = mysqli_query($koneksi, "SELECT * FROM soal WHERE kode_soal = '$kode_soal'");
$data_soal = mysqli_fetch_assoc($q_soal);
$jumlah_opsi = (int)($data_soal['jumlah_opsi'] ?? 4);
$opsi_huruf = ['A','B','C','D','E'];
$batas_menit_selesai = (int)($data_soal['tampil_tombol_selesai'] ?? 0);

if (!$data_soal) {
    $_SESSION['alert'] = true;
    $_SESSION['warning_message'] = 'Soal tidak ditemukan.';
    header('Location: ujian.php');
    exit;
}

if (strtolower($data_soal['status']) !== 'aktif') {
    $_SESSION['alert'] = true;
    $_SESSION['warning_message'] = 'Soal Tidak Aktif! Silakan hubungi pengawas.';
    header('Location: ujian.php');
    exit;
} 

$tanggal_soal = $data_soal['tanggal'];
$tanggal_hari_ini = date('Y-m-d');

if (strtotime($tanggal_hari_ini) < strtotime($tanggal_soal)) {
    $_SESSION['alert'] = true;
    $_SESSION['warning_message'] = 'Soal belum bisa dikerjakan. Jadwal ujian belum dimulai.';
    header('Location: ujian.php');
    exit;
}
$kr_siswa = $kelas_siswa.$rombel_siswa;

if (!in_array($kr_siswa, explode(',', $data_soal['kelas']))) {
    $_SESSION['alert'] = true;
    $_SESSION['warning_message'] = 'Soal ini bukan untuk kelas & rombel kamu.';
    header('Location: ujian.php');
    exit;
}
// Cek apakah ini siswa lanjut ujian (sudah punya data jawaban_siswa aktif)
// Cek apakah siswa sudah pernah mulai ujian (punya waktu_sisa)
$qcek = mysqli_query($koneksi, "
    SELECT waktu_sisa 
    FROM jawaban_siswa 
    WHERE id_siswa='$id_siswa' 
    AND kode_soal='$kode_soal'
");
$dcek = mysqli_fetch_assoc($qcek);

$lanjut_ujian = ($dcek && $dcek['waktu_sisa'] > 0);


// Kalau bukan lanjut ujian, baru cek token
if (!$lanjut_ujian) {
    if ($token !== $data_soal['token']) {
        $_SESSION['alert'] = true;
        $_SESSION['warning_message'] = 'Token tidak Valid.';
        header('Location: ujian.php');
        exit;
    }
}


// Cek jika siswa sudah pernah mengerjakan
$q_nilai = mysqli_query($koneksi, "SELECT * FROM nilai WHERE id_siswa = '$id_siswa' AND kode_soal = '$kode_soal'");
if (mysqli_num_rows($q_nilai) > 0) {
    $_SESSION['alert'] = true;
    $_SESSION['warning_message'] = 'Kamu sudah mengerjakan soal ini.';
    header('Location: ujian.php');
    exit;
}

// Get remaining time and answers
$waktu_sisa = 0;
$get_waktu = mysqli_query($koneksi, 
    "SELECT waktu_sisa, jawaban_siswa 
     FROM jawaban_siswa 
     WHERE kode_soal='$kode_soal' AND id_siswa='$id_siswa'");
$jawaban_tersimpan = [];

if ($w = mysqli_fetch_assoc($get_waktu)) {
    // Sudah pernah mulai → pakai waktu yang tersimpan
    $waktu_sisa = (int) $w['waktu_sisa'];
    $string_jawaban = $w['jawaban_siswa'];
    preg_match_all('/\[(\d+):([^\]]+)\]/', $string_jawaban, $matches, PREG_SET_ORDER);

    foreach ($matches as $match) {
        $nomor = (int) $match[1];
        $jawab = $match[2];

        if (strpos($jawab, '|') !== false && substr_count($jawab, ':') > 1) {
            $pasangan = explode('|', $jawab);
            $hasil = [];
            foreach ($pasangan as $p) {
                if (substr_count($p, ':') === 1) {
                    [$kiri, $kanan] = explode(':', $p, 2);
                    $kiri = trim($kiri);
                    $kanan = trim($kanan);
                    if (!empty($kiri) && !empty($kanan)) {
                        $hasil[$kiri] = $kanan;
                    }
                }
            }
            $jawaban_tersimpan[$nomor] = $hasil;
        }
        elseif (strpos($jawab, '|') !== false) {
            $jawaban_tersimpan[$nomor] = explode('|', $jawab);
        } elseif (strpos($jawab, ',') !== false) {
            $jawaban_tersimpan[$nomor] = explode(',', $jawab);
        } else {
            $jawaban_tersimpan[$nomor] = $jawab;
        }
    }

} else {
    // ⚠️ Tambahan: pertama kali mulai → ambil waktu dari soal
    $waktu_sisa = (int)$data_soal['waktu_ujian']; // MENIT
    // Buat baris jawaban_siswa supaya nanti bisa diupdate
    mysqli_query($koneksi, 
        "INSERT INTO jawaban_siswa (id_siswa, kode_soal, waktu_sisa, status_ujian, jawaban_siswa)
         VALUES ('$id_siswa', '$kode_soal', '$waktu_sisa', 'Aktif', '')");
}


$stmt = $koneksi->prepare("UPDATE jawaban_siswa SET status_ujian = 'Aktif' WHERE id_siswa = ? AND kode_soal = ?");
if (!$stmt) {
    die("Prepare gagal: " . $koneksi->error);
}

$stmt->bind_param("ss", $id_siswa, $kode_soal);
if (!$stmt->execute()) {
    die("Eksekusi gagal: " . $stmt->error);
}

// Get all questions
$tampil = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT tampilan_soal FROM soal WHERE kode_soal='$kode_soal' LIMIT 1"));
$tampilan = $tampil['tampilan_soal'] ?? 'Urut';

// Simpan urutan soal ke session
if ($tampilan === 'Acak') {

    // 1️⃣ Pilihan Ganda
    $pg = mysqli_query($koneksi, "
        SELECT * FROM butir_soal 
        WHERE kode_soal='$kode_soal' 
        AND tipe_soal='Pilihan Ganda'
        ORDER BY RAND()
    ");

    // 2️⃣ Pilihan Ganda Kompleks
    $pgx = mysqli_query($koneksi, "
        SELECT * FROM butir_soal 
        WHERE kode_soal='$kode_soal' 
        AND tipe_soal='Pilihan Ganda Kompleks'
        ORDER BY RAND()
    ");

    // 3️⃣ Benar / Salah
    $bs = mysqli_query($koneksi, "
        SELECT * FROM butir_soal 
        WHERE kode_soal='$kode_soal' 
        AND tipe_soal='Benar/Salah'
        ORDER BY RAND()
    ");

    // 4️⃣ Menjodohkan
    $mj = mysqli_query($koneksi, "
        SELECT * FROM butir_soal 
        WHERE kode_soal='$kode_soal' 
        AND tipe_soal='Menjodohkan'
        ORDER BY RAND()
    ");

    // 5️⃣ Uraian (paling akhir)
    $ur = mysqli_query($koneksi, "
        SELECT * FROM butir_soal 
        WHERE kode_soal='$kode_soal' 
        AND tipe_soal='Uraian'
        ORDER BY RAND()
    ");

    $_SESSION['soal_order'] = [];
    $soal = [];

    // Susun sesuai urutan tipe
    foreach ([$pg, $pgx, $bs, $mj, $ur] as $group) {
        while ($s = mysqli_fetch_assoc($group)) {
            $soal[] = $s;
            $_SESSION['soal_order'][] = $s['nomer_soal'];
        }
    }

} else {

    $q = mysqli_query($koneksi, "SELECT * FROM butir_soal WHERE kode_soal='$kode_soal' ORDER BY nomer_soal ASC");
    $_SESSION['soal_order'] = [];
    while ($s = mysqli_fetch_assoc($q)) {
        $soal[] = $s;
        $_SESSION['soal_order'][] = $s['nomer_soal'];
    }
}

// Buat mapping nomer_soal ke indeks array
$soal_index_map = [];
foreach ($soal as $index => $s) {
    $soal_index_map[$s['nomer_soal']] = $index;
}

$q_tema = mysqli_query($koneksi, "SELECT * FROM pengaturan WHERE id = 1 LIMIT 1");
$data_tema = mysqli_fetch_assoc($q_tema);
$warna_tema = $data_tema['warna_tema'] ?? '#0d6efd';
$interval_ms = ((int)$data_tema['waktu_sinkronisasi']) * 1000;
$query = "SELECT jawaban_siswa FROM jawaban_siswa 
          WHERE id_siswa = '$id_siswa' AND kode_soal = '$kode_soal'";
$result = mysqli_query($koneksi, $query);
$row = mysqli_fetch_assoc($result);

// Parse format [1:...][2:...]
$jawaban = $row['jawaban_siswa'] ?? '';
preg_match_all('/\[(\d+):([^\]]*)\]/', $jawaban, $matches, PREG_SET_ORDER);

// Buat array status
$status_soal = [];
foreach ($matches as $match) {
    $nomor = $match[1];
    $isi = trim($match[2]);
    $status_soal[$nomor] = !empty($isi);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ujian Siswa</title>
    <?php include '../inc/css.php'; ?>
    <?php include '../inc/cssujian.php'; ?>
    <script>
    const syncInterval = <?= $interval_ms ?>;
    const batasMenitSelesai = <?= $batas_menit_selesai ?>;
    </script>
</head>

<body>
    <!-- Loading Spinner Overlay -->
    <div id="loadingOverlay">
        <div class="spinner-container">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Memuat soal...</p>
        </div>
    </div>
    <div style="height: 75px;"></div>
    <div class="wrapper">
        <div class="main">
            <nav class="navbar navbar-expand navbar-light navbar-bg fixed-top"
                style="border-bottom:5px solid <?php echo htmlspecialchars($warna_tema); ?> !important;">
                <a class="navbar-brand ms-3" href="#">
                    <img src="../assets/images/<?php echo $data_tema['logo_sekolah']; ?>" alt="Logo"
                        style="height: 36px;">
                </a>
                <div class="navbar-collapse collapse">
                    <ul class="navbar-nav navbar-align ms-auto">
                        <li class="nav-item">
                            <a href="#" class="nav-link px-3" id="darkModeToggle">
                                <i class="fas fa-moon"></i>
                            </a>
                        </li>
                        <li class="nav-item me-3">
                            <div class="dropdown-toggle" href="#" style="font-weight: bold; font-size: 1.2rem;"
                                id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i>
                            </div>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-wide">
                                <li><span class="dropdown-item-text"><strong><?php echo $nama_siswa; ?></strong></span>
                                </li>
                                <li><span class="dropdown-item-text"><?php echo $kelas_siswa . $rombel_siswa; ?></span>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
            <main class="content">
                <div class="container-fluid p-0">
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow">
                                <div class="card-header text-white d-flex justify-content-between align-items-center"
                                    style="background-color:<?= htmlspecialchars($warna_tema) ?>">

                                    <h4 class="mb-0 text-white">
                                        <b style="background-color:#ffffff;padding:5px 10px;border-radius:20px;color:black;font-size:15px;"
                                            id="currentQuestionNumber">Nomor</b>
                                        <b id="texttimer">
                                            <i class="fa-regular fa-clock"></i> Sisa Waktu: <span
                                                id="timer">00:00</span>
                                        </b>
                                    </h4>

                                    <div class="btn-group" role="group" aria-label="Font size controls">
                                        <button onclick="changeFontSize(1)" class="btn btn-sm btn-light"
                                            title="Perbesar">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <button onclick="changeFontSize(-1)" class="btn btn-sm btn-light"
                                            title="Perkecil">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <button onclick="resetFontSize()" class="btn btn-sm btn-light"
                                            title="Reset Ukuran">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </div>

                                </div>

                                <div class="card-body wadah">
                                    <div id="autoSaveStatus"></div>
                                    <form id="formUjian" method="post" action="simpan_jawaban.php">
                                        <input type="hidden" name="waktu_sisa" id="waktu_sisa">
                                        <input type="hidden" name="kode_soal"
                                            value="<?= htmlspecialchars($kode_soal) ?>">

                                        <?php foreach ($soal as $index => $s):
                                            $no_asli = $s['nomer_soal'];
                                            $no_urut = $index + 1;
                                            $tipe = $s['tipe_soal'];
                                            $pertanyaan = $s['pertanyaan'];
                                            $jawaban = $jawaban_tersimpan[$no_asli] ?? '';
                                            ?>
                                        <div class="question-container" id="soal-<?= $index ?>">
                                            <span class="badge" style="font-size: 12px !important;border:solid 1px grey;color:grey;"><?= $tipe ?></span><br><br>
                                            <div class="question-text"><?= $pertanyaan ?></div>

                                            <?php if ($tipe == 'Pilihan Ganda'): ?>
                                            <?php
                                                    for ($i = 1; $i <= $jumlah_opsi; $i++):
                                                        $huruf = $opsi_huruf[$i - 1];
                                                    ?>
                                            <label class="option-circle">
                                                <input type="radio" name="jawaban[<?= $no_asli ?>]"
                                                    value="pilihan_<?= $i ?>"
                                                    <?= $jawaban == 'pilihan_' . $i ? 'checked' : '' ?>>
                                                <span><?= $huruf ?></span>
                                                <?= $s['pilihan_' . $i] ?>
                                            </label>
                                            <?php endfor; ?>


                                            <?php elseif ($tipe == 'Pilihan Ganda Kompleks'):
                                                    if (!is_array($jawaban)) {
                                                        $jawaban = [$jawaban];
                                                    }
                                                    for ($i = 1; $i <= $jumlah_opsi; $i++):
                                                        $huruf = $opsi_huruf[$i - 1];
                                                ?>
                                            <label class="option-circle">
                                                <input type="checkbox" name="jawaban[<?= $no_asli ?>][]"
                                                    value="pilihan_<?= $i ?>"
                                                    <?= in_array('pilihan_' . $i, $jawaban) ? 'checked' : '' ?>>
                                                <span><?= $huruf ?></span>
                                                <?= $s['pilihan_' . $i] ?>
                                            </label>
                                            <?php endfor; ?>


                                            <?php elseif ($tipe == 'Benar/Salah'): ?>
                                            <table class="matching-table">
                                                <?php for ($i = 1; $i <= 5; $i++):
                                                            $pernyataan = $s['pilihan_' . $i];
                                                            if (trim($pernyataan) == '')
                                                                continue;
                                                            $jawab = $jawaban[$i - 1] ?? '';
                                                            ?>
                                                <tr>
                                                    <td><?= $pernyataan ?></td>
                                                    <td>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio"
                                                                name="jawaban[<?= $no_asli ?>][<?= $i ?>]" value="Benar"
                                                                <?= $jawab == 'Benar' ? 'checked' : '' ?>>
                                                            <label class="form-check-label">Benar</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio"
                                                                name="jawaban[<?= $no_asli ?>][<?= $i ?>]" value="Salah"
                                                                <?= $jawab == 'Salah' ? 'checked' : '' ?>>
                                                            <label class="form-check-label">Salah</label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endfor; ?>
                                            </table>

                                            <?php elseif ($tipe == 'Menjodohkan'):
                                                    $raw = trim($s['jawaban_benar'], "[]");
                                                    $raw = preg_replace('/^\d+\s*:/', '', $raw);
                                                    $pasangan_list = explode('|', $raw);

                                                    $opsi = [];
                                                    foreach ($pasangan_list as $item) {
                                                        if (strpos($item, ':') !== false) {
                                                            list($kiri, $kanan) = explode(':', $item, 2);
                                                            $kiri = trim($kiri);
                                                            $kanan = trim($kanan);
                                                            if ($kiri !== '' && $kanan !== '') {
                                                                $opsi[] = ['kiri' => $kiri, 'kanan' => $kanan];
                                                            }
                                                        }
                                                    }

                                                    $jawaban_soal = (is_array($jawaban)) ? $jawaban : [];
                                                    $daftar_kanan = array_values(array_unique(array_column($opsi, 'kanan')));
                                                    shuffle($daftar_kanan);
                                                    ?>

                                            <?php if (count($opsi) === 0): ?>
                                            <div class="alert alert-warning">Soal menjodohkan belum memiliki pasangan
                                                yang valid.</div>
                                            <?php else: ?>
                                            <?php foreach ($opsi as $p): ?>
                                            <input type="hidden" name="soal_kiri[<?= $no_asli ?>][]"
                                                value="<?= htmlspecialchars($p['kiri']) ?>">
                                            <?php endforeach; ?>

                                            <table class="matching-table">
                                                <?php foreach ($opsi as $p):
                                                                $kiri = $p['kiri'];
                                                                $selected = $jawaban_soal[$kiri] ?? '';
                                                            ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($kiri) ?></td>
                                                    <td>
                                                        <select
                                                            name="jawaban[<?= $no_asli ?>][<?= htmlspecialchars($kiri) ?>]"
                                                            class="form-select">
                                                            <option value="">-- Pilih --</option>
                                                            <?php foreach ($daftar_kanan as $dk): ?>
                                                            <option value="<?= htmlspecialchars($dk) ?>"
                                                                <?= ($selected === $dk) ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($dk) ?>
                                                            </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </table>
                                            <?php endif; ?>

                                            <?php elseif ($tipe == 'Uraian'): ?>
                                            <textarea name="jawaban[<?= $no_asli ?>]"
                                                class="essay-textarea"><?= htmlspecialchars($jawaban) ?></textarea>
                                            <?php endif; ?>
                                        </div>
                                        <?php endforeach; ?>

                                        <div class="navigation-buttons">
                                            <div style="flex: 1;">
                                                <button type="button" class="btn btn-primary" id="prevBtn"
                                                    onclick="prevSoal()" style="display: none; float: left;">
                                                    <i class="fas fa-arrow-left me-1"></i> Sebelumnya
                                                </button>
                                            </div>
                                            <div style="flex: 1; text-align: right;">
                                                <button type="button" class="btn btn-primary" id="nextBtn"
                                                    onclick="nextSoal()" style="float: right;">
                                                    Berikutnya <i class="fas fa-arrow-right ms-1"></i>
                                                </button>
                                                <button type="submit" class="btn btn-success" id="submitBtn"
                                                style="display: none !important; float: right;">
                                                    <i class="fas fa-check-circle me-1"></i> Selesai
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    <button id="navToggle" class="btn btn-primary rounded-circle"
                                        style="border:none;position: fixed; bottom: 80px; right: 20px; z-index: 1000; width: 50px; height: 50px;background-color:<?php echo htmlspecialchars($warna_tema); ?>;">
                                        <i class="fas fa-list"></i>
                                    </button>
                                    <div class="question-nav-container">
                                        <div class="card shadow">
                                            <div class="card-header text-white"
                                                style="background-color:<?php echo htmlspecialchars($warna_tema); ?>;">
                                                Daftar Soal
                                                <button type="button" class="close text-black" aria-label="Close"
                                                    style="float: right;">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="card-body p-2">
                                                <div class="question-nav d-flex flex-wrap" style="gap: 5px;">
                                                    <?php foreach ($soal as $index => $s): ?>
                                                    <?php
                                                            $no_urut = $index + 1;
                                                            $no_asli = $s['nomer_soal'];
                                                            $is_answered = isset($status_soal[$no_asli]) && $status_soal[$no_asli]; // TRUE jika sudah dijawab
                                                        ?>
                                                    <button type="button" class="nav-btn"
                                                        onclick="tampilSoal(<?= $index ?>); hideNav()"
                                                        data-nomor="<?= $no_asli ?>" data-urut="<?= $no_urut ?>"
                                                        <?= $is_answered ? 'data-answered="true"' : '' ?>>
                                                        <?= $no_urut ?>
                                                    </button>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <div id="imageModal" class="modal-img" onclick="closeModal(event)">
        <span class="close-btn">&times;</span>
        <img id="modalImage" class="modal-content-img" alt="Preview">
    </div>
    <?php
                                                $q = mysqli_query($koneksi, "SELECT * FROM pengaturan WHERE id = 1");
                                                $data = mysqli_fetch_assoc($q);
                                                ?>
    <footer class="footer mt-auto py-3 bg-dark sticky-bottom">
        <div class="container-fluid">
            <div class="row text-grey">
                <div class="col-6 text-start">
                    <p class="mb-0">
                        <a href="#" id="enc" style="color:grey;text-decoration:none;"></a>
                    </p>
                </div>
            </div>
        </div>
    </footer>
    <script src="../assets/adminkit/static/js/app.js"></script>
    <script src="../assets/js/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/sweetalert.js"></script>
    <script src="../assets/datatables/datatables.js"></script>
    <?php include '../inc/check_activity.php'; ?>
    <?php include '../inc/script_ujian.php'; ?>
    <script>
        document.addEventListener('contextmenu', e => e.preventDefault()); // klik kanan
document.addEventListener('keydown', function(e) {
    if (
      e.key === 'F12' || // DevTools
      (e.ctrlKey && e.shiftKey && (e.key.toLowerCase() === 'i' || e.key.toLowerCase() === 'j')) || // Ctrl+Shift+I/J
      (e.ctrlKey && e.key.toLowerCase() === 'u') // Ctrl+U (view source)
    ) {
        e.preventDefault();
    }
});
document.addEventListener('keydown', function(e) {
  // Cegah Ctrl+C (Copy)
  if (e.ctrlKey && e.key.toLowerCase() === 'c') {
    e.preventDefault();
  }
  // Cegah Ctrl+V (Paste)
  if (e.ctrlKey && e.key.toLowerCase() === 'v') {
    e.preventDefault();
  }
});

// Tambahan untuk event copy dan paste untuk jaga-jaga
document.addEventListener('copy', function(e) {
  e.preventDefault();
});
document.addEventListener('paste', function(e) {
  e.preventDefault();
});
// Fungsi tampilkan peringatan di halaman
function tampilkanPeringatan(msg) {
  let peringatan = document.getElementById('peringatan-keamanan');
  if (!peringatan) {
    peringatan = document.createElement('div');
    peringatan.id = 'peringatan-keamanan';
    peringatan.style.position = 'fixed';
    peringatan.style.top = '10px';
    peringatan.style.right = '10px';
    peringatan.style.backgroundColor = 'rgba(63, 63, 63, 0.8)';
    peringatan.style.color = 'white';
    peringatan.style.padding = '10px 20px';
    peringatan.style.borderRadius = '5px';
    peringatan.style.zIndex = '9999';
    peringatan.style.fontWeight = 'bold';
    peringatan.style.fontSize = '12px';
    document.body.appendChild(peringatan);
  }
  peringatan.innerHTML = `<i class="fa fa-exclamation-circle"></i> ${msg}`;
  // Hilangkan peringatan setelah 5 detik
  setTimeout(() => {
    if (peringatan) peringatan.remove();
  }, 3000);
}

// Cegah klik kanan
document.addEventListener('contextmenu', function(e) {
  e.preventDefault();
  tampilkanPeringatan('Klik kanan dilarang saat ujian!');
});

// Cegah Ctrl+C, Ctrl+V, Ctrl+U, F12, Ctrl+Shift+I/J
document.addEventListener('keydown', function(e) {
  if (
    e.key === 'F12' ||
    (e.ctrlKey && e.shiftKey && (e.key.toLowerCase() === 'i' || e.key.toLowerCase() === 'j')) ||
    (e.ctrlKey && e.key.toLowerCase() === 'u') ||
    (e.ctrlKey && e.key.toLowerCase() === 'c') ||
    (e.ctrlKey && e.key.toLowerCase() === 'v')
  ) {
    e.preventDefault();
    tampilkanPeringatan('Shortcut keyboard dilarang saat ujian!');
  }
});

// Deteksi pindah tab/window
document.addEventListener('visibilitychange', function() {
  if (document.hidden) {
    tampilkanPeringatan('Jangan pindah tab saat ujian berlangsung!');
    // Bisa juga kirim sinyal ke server untuk catat pelanggaran
  }
});

    </script>
</body>

</html>