<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');

include '../inc/dataadmin.php';

// Pastikan ID soal ada di URL
if (!isset($_GET['id_soal'])) {
    header('Location: soal.php');
    exit();
}

$id_soal = $_GET['id_soal'];
only_pemilik_soal_by_id($id_soal);
// Ambil data soal berdasarkan ID
$query = "SELECT * FROM soal WHERE id_soal = '$id_soal'";
$result = mysqli_query($koneksi, $query);
$row = mysqli_fetch_assoc($result);
$kelas_tersimpan = explode(',', $row['kelas']);
if (!$row) {
    echo "Soal tidak ditemukan!";
    exit();
}

// ✅ Jika soal status = aktif, tampilkan SweetAlert + redirect
if ($row['status'] == 'Aktif') {
    $_SESSION['warning_message'] = 'Soal ini sudah aktif dan tidak bisa diedit!';
    header('Location: soal.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $kode_soal = mysqli_real_escape_string($koneksi, $_POST['kode_soal']);
    $nama_soal = mysqli_real_escape_string($koneksi, $_POST['nama_soal']);
    $mapel = mysqli_real_escape_string($koneksi, $_POST['mapel']);
    $kelas = implode(',', $_POST['kelasrombel']);
$kelas = mysqli_real_escape_string($koneksi, $kelas);
    $tampilan_soal = mysqli_real_escape_string($koneksi, $_POST['tampilan_soal']);
    $waktu_ujian = mysqli_real_escape_string($koneksi, $_POST['waktu_ujian']);
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $jumlah_opsi = mysqli_real_escape_string($koneksi, $_POST['jumlah_opsi']);
$tampil_tombol_selesai = mysqli_real_escape_string($koneksi, $_POST['tampil_tombol_selesai']);


    // Update data soal
    $update_query = "UPDATE soal SET 
                    kode_soal = '$kode_soal', 
                    nama_soal = '$nama_soal',
                    mapel = '$mapel', 
                    kelas = '$kelas', 
                    tampilan_soal = '$tampilan_soal', 
                    waktu_ujian = '$waktu_ujian', 
                    tanggal = '$tanggal',
                    jumlah_opsi = '$jumlah_opsi',
tampil_tombol_selesai = '$tampil_tombol_selesai'
                WHERE id_soal = '$id_soal'";

    if (mysqli_query($koneksi, $update_query)) {
        $_SESSION['success_message'] = 'Data soal berhasil diupdate!';
        header('Location: soal.php');
        exit();
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Soal</title>
    <?php include '../inc/css.php'; ?>
</head>

<body>
    <div class="wrapper">

        <?php include 'sidebar.php'; ?>

        <div class="main">
            <?php include 'navbar.php'; ?>

            <main class="content">
                <div class="container-fluid p-0">

                    <div class="row">
                        <div class="col-12 col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Edit Soal</h5>
                                </div>
                                <div class="card-body">
                                    <?php
                                        // Ambil data kelas dari tabel siswa secara DISTINCT
                                        $query_kelas = "
SELECT DISTINCT kelas, rombel 
FROM siswa 
ORDER BY kelas, rombel
";
$result_kelas = mysqli_query($koneksi, $query_kelas);
                                        ?>
                                    <form method="POST">
                                        <div class="mb-3">
                                            <h2>Kode Soal : <?php echo $row['kode_soal']; ?></h2>
                                            <input type="hidden" class="form-control" id="kode_soal" name="kode_soal" value="<?php echo $row['kode_soal']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="nama_soal" class="form-label">Nama Soal</label>
                                            <input type="text" class="form-control" id="nama_soal" name="nama_soal" value="<?php echo $row['nama_soal']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="mapel" class="form-label">Mata Pelajaran</label>
                                            <input type="text" class="form-control" id="mapel" name="mapel" value="<?php echo $row['mapel']; ?>" required>
                                        </div>
                                        <div class="mb-3">
    <label class="form-label">Pilih Kelas & Rombel</label>
    <div style="max-height:220px;overflow:auto;border:1px solid #ddd;padding:10px;border-radius:6px;">
        <?php while ($row_k = mysqli_fetch_assoc($result_kelas)): 
            $kr = $row_k['kelas'].$row_k['rombel'];
            $checked = in_array($kr, $kelas_tersimpan) ? 'checked' : '';
        ?>
            <label style="display:block">
                <input type="checkbox" name="kelasrombel[]" value="<?= $kr; ?>" <?= $checked; ?>>
                <b><?= $kr; ?></b>
            </label>
        <?php endwhile; ?>
    </div>
</div>
                                        <div class="mb-3">
                                            <label for="waktu_ujian" class="form-label">Waktu Ujian (Menit)</label>
                                            <input type="number" class="form-control" id="waktu_ujian" name="waktu_ujian" value="<?php echo $row['waktu_ujian']; ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="tampilan_soal" class="form-label">Tampilan Soal</label>
                                            <select class="form-control" id="tampilan_soal" name="tampilan_soal" required>
                                                <option value="<?php echo $row['tampilan_soal']; ?>"><?php echo $row['tampilan_soal']; ?></option>
                                                    <option value="Acak">Acak</option>
                                                    <option value="Urut">Urut</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="jumlah_opsi" class="form-label">Jumlah Pilihan Jawaban</label>
                                            <select class="form-control" id="jumlah_opsi" name="jumlah_opsi" required>
                                                <option value="4" <?php echo ($row['jumlah_opsi'] == 4) ? 'selected' : ''; ?>>4 Pilihan (A-D)</option>
                                                <option value="5" <?php echo ($row['jumlah_opsi'] == 5) ? 'selected' : ''; ?>>5 Pilihan (A-E)</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
    <label class="form-label">Tampilkan Tombol Selesai Saat Sisa Waktu (menit)</label>
    <input type="number"
           class="form-control"
           name="tampil_tombol_selesai"
           value="<?php echo $row['tampil_tombol_selesai'] ?? 0; ?>">
    <small class="text-muted">
        Isi 0 jika tombol boleh tampil dari awal.  
        Contoh isi 10 → tombol muncul saat sisa waktu tinggal 10 menit.
    </small>
</div>


                                        <div class="mb-3">
                                            <label for="tanggal" class="form-label">Tanggal Ujian</label>
                                            <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?php echo $row['tanggal']; ?>" required onclick="this.showPicker()">
                                        </div>
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
										<a href="soal.php" class="btn btn-danger">Batal</a>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </main>

        </div>
    </div>
<?php include '../inc/js.php'; ?>
</body>
</html>