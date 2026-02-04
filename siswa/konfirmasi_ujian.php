<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('siswa');
include '../inc/datasiswa.php';
// Ambil pengaturan reset login
$qset = mysqli_query($koneksi, "SELECT izinkan_lanjut_ujian FROM pengaturan WHERE id=1");
$dset = mysqli_fetch_assoc($qset);
$izinkan_lanjut = $dset['izinkan_lanjut_ujian'] ?? 'tidak';

$kode_soal = $_GET['kode_soal'] ?? '';

if (!$id_siswa || !$kode_soal) {
    $_SESSION['alert'] = true;
    $_SESSION['warning_message'] = 'Data tidak lengkap.';
    header('Location: ujian.php');
    exit;
}

// Ambil data siswa
$q_siswa = mysqli_query($koneksi, "SELECT * FROM siswa WHERE id_siswa = '$id_siswa'");
$data_siswa = mysqli_fetch_assoc($q_siswa);

// Ambil data soal
$q_soal = mysqli_query($koneksi, "SELECT * FROM soal WHERE kode_soal = '$kode_soal'");
$data_soal = mysqli_fetch_assoc($q_soal);

// Validasi data siswa & soal
if (!$data_siswa) {
    $_SESSION['alert'] = true;
    $_SESSION['warning_message'] = 'Siswa tidak ditemukan.';
    header('Location: ujian.php');
    exit;
}

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
// Cek jika tanggal hari ini kurang dari tanggal soal (belum dimulai)
$tanggal_soal = $data_soal['tanggal'];
$tanggal_hari_ini = date('Y-m-d');

if (strtotime($tanggal_hari_ini) < strtotime($tanggal_soal)) {
    $_SESSION['alert'] = true;
    $_SESSION['warning_message'] = 'Soal belum bisa dikerjakan. Jadwal ujian belum dimulai.';
    header('Location: ujian.php');
    exit;
}
$kr_siswa = $data_siswa['kelas'].$data_siswa['rombel'];

if (!in_array($kr_siswa, explode(',', $data_soal['kelas']))) {
    $_SESSION['alert'] = true;
    $_SESSION['warning_message'] = 'Soal ini bukan untuk kelas & rombel kamu.';
    header('Location: ujian.php');
    exit;
}

// Cek jika masih ujian aktif
// Cek jika masih ujian aktif
$q_jawaban = mysqli_query($koneksi, "
    SELECT status_ujian 
    FROM jawaban_siswa 
    WHERE id_siswa = '$id_siswa' 
    AND kode_soal = '$kode_soal'
");

$d_jawaban = mysqli_fetch_assoc($q_jawaban);

if ($d_jawaban && $d_jawaban['status_ujian'] == 'Aktif') {

    if ($izinkan_lanjut === 'tidak') {
        $_SESSION['alert'] = true;
        $_SESSION['warning_message'] = 'Kamu keluar dari ujian. Hubungi pengawas untuk reset login.';
        header('Location: ujian.php');
        exit;
    }

    // ✅ kalau diizinkan, JANGAN redirect
    // biarkan tetap di halaman ini supaya isi TOKEN lagi
}



// Cek jika siswa sudah pernah mengerjakan
$q_nilai = mysqli_query($koneksi, "SELECT * FROM nilai WHERE id_siswa = '$id_siswa' AND kode_soal = '$kode_soal'");
if (mysqli_num_rows($q_nilai) > 0) {
    $_SESSION['alert'] = true;
    $_SESSION['warning_message'] = 'Kamu sudah mengerjakan soal ini.';
    header('Location: ujian.php');
    exit;
}

// Hitung jumlah soal
$q_jumlah = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM butir_soal WHERE kode_soal = '$kode_soal'");
$data_jumlah = mysqli_fetch_assoc($q_jumlah);
$jumlah_soal = $data_jumlah['total'];
$q_tema = mysqli_query($koneksi, "SELECT * FROM pengaturan WHERE id = 1 LIMIT 1");
$data_tema = mysqli_fetch_assoc($q_tema);
$warna_tema = $data_tema['warna_tema'] ?? '#0d6efd'; // default jika tidak ada data
$_SESSION['konfirmasi_ujian'] = true;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ujian Siswa</title>
    <?php include '../inc/css.php'; ?>
    <style>
           .form-check-custom {
        padding: 1rem;
        background-color: #f8f9fa;
        border-radius: 0.75rem;
        border: 1px solid #ced4da;
        transition: all 0.3s ease;
    }

    .form-check-custom:hover {
        background-color: #e9ecef;
        border-color: #adb5bd;
    }

    .form-check-input:checked {
        background-color: #198754;
        border-color: #198754;
    }

    .form-check-label {
        font-size: 12px;
        font-weight: 500;
        color: #212529;
        margin-left: 0.5rem;
    }
    html, body {
            height: 100%;
            margin: 0;
            overflow: hidden;
        }
        .wrapper {
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
       main.content {
    flex: 1;
    overflow-y: auto;
    padding-bottom: 220px; /* beri ruang sebesar tinggi footer */
    margin-bottom: 0; /* hapus margin agar tidak dobel spacing */
}

/* Tambahkan media query agar lebih lega di HP */
@media (max-width: 768px) {
    main.content {
        padding-bottom: 260px; /* beri ruang ekstra di HP */
    }
}
</style>
</head>
<body>
    <div style="height: 80px;"></div>
    <div class="wrapper">
        <div class="main">
            <nav class="navbar navbar-expand navbar-light navbar-bg fixed-top" style="border-bottom:5px solid <?php echo htmlspecialchars($warna_tema); ?> !important;">
                <!-- Logo -->
                <a class="navbar-brand ms-3" href="#">
                    <img src="../assets/images/<?php echo $data_tema['logo_sekolah']; ?>" alt="Logo" style="height: 36px;">
                </a>

                <div class="navbar-collapse collapse">
                    <ul class="navbar-nav navbar-align ms-auto">
                        <!-- User Info -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><span class="dropdown-item-text"><strong><?php echo $nama_siswa; ?></strong></span></li>
                                <li><span class="dropdown-item-text"><?php echo $kelas_siswa . $rombel_siswa; ?></span></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger btnLogout" href="logout.php"><i class="fas fa-sign-out-alt me-1"></i> Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
            <main class="content">
                <div class="container p-0">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card shadow">
                                    <div class="card-header text-white" style="background-color:<?= htmlspecialchars($warna_tema) ?>">
                                        <h4 class="mb-0 text-white"><i class="fas fa-clipboard-check"></i> Konfirmasi Ujian</h4>
                                    </div>
                                    <div class="card-body">
                                        <h5 class="mb-3">📌 Data Siswa</h5>
                                        <table class="table table-bordered">
                                            <tr><th width="30%">Nama Siswa</th><td><?= htmlspecialchars($data_siswa['nama_siswa']) ?></td></tr>
                                            <tr><th width="30%">Username</th><td><?= htmlspecialchars($data_siswa['username']) ?></td></tr>
                                            <tr><th width="30%">Kelas</th><td><?= htmlspecialchars($data_siswa['kelas']) ?></td></tr>
                                            <tr><th width="30%">Rombel</th><td><?= htmlspecialchars($data_siswa['rombel']) ?></td></tr>
                                        </table>

                                        <h5 class="mt-4 mb-3">📝 Data Soal</h5>
                                        <table class="table table-bordered">
                                            <tr><th width="30%">Kode Soal</th><td><?= htmlspecialchars($data_soal['kode_soal']) ?></td></tr>
                                            <tr><th width="30%">Mapel</th><td><?= htmlspecialchars($data_soal['mapel']) ?></td></tr>
                                            <tr><th width="30%">Tanggal Ujian</th><td><?= htmlspecialchars($data_soal['tanggal']) ?></td></tr>
                                            <tr><th width="30%">Durasi Ujian</th><td><?= htmlspecialchars($data_soal['waktu_ujian']) ?> menit</td></tr>
                                            <tr><th width="30%">Jumlah Soal</th><td><?= $jumlah_soal ?></td></tr>
                                        </table>

                                        <div class="text-start mt-4">
                                            <form action="mulaiujian.php" method="post" class="row g-2 justify-content-start align-items-center">
                                                <input type="hidden" name="id_siswa" value="<?= htmlspecialchars($id_siswa) ?>">
                                                <input type="hidden" name="kode_soal" value="<?= htmlspecialchars($kode_soal) ?>">

                                                <div class="col-12">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="persetujuan" required>
                                                        <label class="form-check-label" for="persetujuan">
                                                            Saya siap mengerjakan ujian ini secara jujur dan bertanggung jawab.
                                                        </label>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="konfirmasi_token" value="ok">
                                                <div class="col-auto">
                                                    <input type="text" name="token" class="form-control form-control-lg" placeholder="Token" required>
                                                </div>

                                                <div class="col-auto">
                                                    <button type="submit" class="btn btn-success btn-lg">
                                                        <i class="fas fa-play-circle"></i> Mulai Ujian
                                                    </button>
                                                </div>

                                                <div class="col-auto">
                                                    <a href="ujian.php" class="btn btn-secondary btn-lg">
                                                        <i class="fas fa-times-circle"></i> Batal
                                                    </a>
                                                </div>
                                            </form>
                                        </div>

                                 </div>       
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <footer class="footer mt-auto py-3 bg-dark sticky-bottom">
                                                    <div class="container-fluid">
                                                        <div class="row text-grey">
                                                            <div class="col-6 text-start">
                                                                <p class="mb-0">
                                                                <a href="#" id="enc" style="color:grey;"></a>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </footer>
<?php include '../inc/js.php'; ?>
<?php include '../inc/check_activity.php'; ?>
<script>
</script>
</body>
</html>