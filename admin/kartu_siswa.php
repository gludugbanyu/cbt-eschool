<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
require_once '../assets/phpqrcode/qrlib.php';
include '../inc/dataadmin.php';
// Cek jika sudah login
check_login('admin');
only_admin();
// Pastikan koneksi berhasil
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Siswa</title>
    <?php include '../inc/css.php'; ?>
</head>
<body>
    <div class="wrapper">

        <?php include 'sidebar.php'; ?>

        <div class="main">
            <?php include 'navbar.php'; ?>
            <!-- /Navbar -->
            <!-- Content -->
            <main class="content">
                <div class="container-fluid p-0">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Kartu Siswa</h5>
                                </div>
                                <div class="card-body col-md-12">
                                    <!-- Form Filter -->
                                    <form method="GET" class="mb-3 d-flex flex-wrap gap-2">
                                        <select name="kelas" class="form-select" style="width: auto;">
                                            <option value="">Pilih Kelas</option>
                                            <option value="all"
                                                <?php echo (isset($_GET['kelas']) && $_GET['kelas'] === 'all') ? 'selected' : ''; ?>>
                                                Semua Kelas</option>
                                            <?php
                                                $kelas_query = mysqli_query($koneksi, "SELECT DISTINCT CONCAT(kelas, rombel) AS kelas_rombel FROM siswa ORDER BY kelas_rombel ASC");
                                                while ($k = mysqli_fetch_assoc($kelas_query)) {
                                                    $kelas_val = $k['kelas_rombel'];
                                                    $selected = (isset($_GET['kelas']) && $_GET['kelas'] === $kelas_val) ? 'selected' : '';
                                                    echo "<option value='$kelas_val' $selected>$kelas_val</option>";
                                                }
                                                ?>
                                        </select>
                                        <input type="text" name="nama" class="form-control" placeholder="Cari nama..."
                                            style="width: 200px;"
                                            value="<?php echo isset($_GET['nama']) ? htmlspecialchars($_GET['nama']) : ''; ?>">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <a href="kartu_siswa.php" class="btn btn-outline-secondary">Reset</a>
                                    </form>
                                    <!--<button class="btn btn-outline-danger" onclick="exportPDF()"><i class="fa-solid fa-file-pdf"></i> Download PDF</button>-->
                                    <?php if (!empty($_GET['kelas']) || !empty($_GET['nama'])): ?>
<a href="print_kartu.php?kelas=<?php echo urlencode($_GET['kelas']); ?>&nama=<?php echo urlencode($_GET['nama']); ?>"
    target="_blank" class="btn btn-danger">
    <i class="fa-solid fa-file-pdf"></i> Download Pdf
</a>

<a href="print_kartu_biasa.php?kelas=<?php echo urlencode($_GET['kelas']); ?>&nama=<?php echo urlencode($_GET['nama']); ?>"
   target="_blank"
   class="btn btn-primary ms-2">
   <i class="fa fa-print"></i> Print
</a>
<?php endif; ?>


                                    <br><br>


                                    <?php 
                                    $qr_temp_dir = '../assets/temp_qr/';
                                    if (!file_exists($qr_temp_dir)) {
                                        mkdir($qr_temp_dir, 0777, true);
                                    }

                                    // Filter query berdasarkan kelas dan nama
                                    $where = [];
                                    if (!empty($_GET['kelas']) && $_GET['kelas'] !== 'all') {
                                        $kelas = mysqli_real_escape_string($koneksi, $_GET['kelas']);
                                        $where[] = "CONCAT(kelas, rombel) = '$kelas'";
                                    }
                                    if (!empty($_GET['nama'])) {
                                        $nama = mysqli_real_escape_string($koneksi, $_GET['nama']);
                                        $where[] = "nama_siswa LIKE '%$nama%'";
                                    }
                                    $where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
                                    $sql = "SELECT * FROM siswa $where_sql ORDER BY nama_siswa ASC";
                                    $result = mysqli_query($koneksi, $sql);
                                    ?>
                                    <?php
                                    if (empty($_GET['kelas']) && empty($_GET['nama'])) {
                                        echo '<div class="alert alert-primary">Silakan pilih kelas atau cari nama untuk menampilkan kartu siswa.</div>';
                                    }
                                    ?>

                                    <?php if (($result && mysqli_num_rows($result) > 0) && (!empty($_GET['kelas']) || !empty($_GET['nama']))): ?>
                                    <div class="row col-lg-12" id="canvas_div_pdf">

                                        <?php while ($row = mysqli_fetch_assoc($result)):
                                            include '../inc/encrypt.php';
                                            $encoded = $row['password'];
                                            $decoded = base64_decode($encoded);
                                            $iv_length = openssl_cipher_iv_length($method);
                                            $iv2 = substr($decoded, 0, $iv_length);
                                            $encrypted_data = substr($decoded, $iv_length);
                                            $decrypted = openssl_decrypt($encrypted_data, $method, $rahasia, 0, $iv2);
                                            $qr_filename = $qr_temp_dir . $row['username'] . '.png';

                                            if (!file_exists($qr_filename)) {
                                                QRcode::png($row['username'], $qr_filename, QR_ECLEVEL_L, 3);
                                            }
                                            $thn_sekarang = date('Y');
                                            $thn_pelajaran = $thn_sekarang . '/' . ($thn_sekarang + 1);
                                        ?>
                                        <div class="col-lg-4 col-md-6 mb-4">
                                            <div class="p-3 h-100 kartu" style="border:1px solid #000;">
                                                <table style="width: 100%;">
                                                    <tr>
                                                        <td style="width: 20%;">
                                                        <center><img src="<?= base_url('assets/images/kemdikbud.png'); ?>" style="height:35px;"></center>
                                                        </td>
                                                        <td style="width: 80%; text-align: right; font-size: 12px;">
                                                            <center><strong>KARTU PESERTA UJIAN CBT</strong><br>
                                                            TAHUN PELAJARAN <?php echo $thn_pelajaran; ?></center>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <hr style="border:0; border-top:3px double #000; margin:5px 0;">
                                                <table style="width: 100%; font-size: 12px;padding:10px;">

                                                    <tr>
                                                        <td>Nama</td>
                                                        <td>:</td>
                                                        <td><?php echo htmlspecialchars($row['nama_siswa']); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Kelas</td>
                                                        <td>:</td>
                                                        <td><?php echo htmlspecialchars($row['kelas'] . $row['rombel']); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="width: 35%;">Username</td>
                                                        <td style="width: 5%;">:</td>
                                                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Password</td>
                                                        <td>:</td>
                                                        <td><?php echo htmlspecialchars($decrypted); ?></td>
                                                    </tr>
                                                </table>
                                                <br>
                                                <div style="text-align: right;">
                                                    <img src="<?php echo $qr_filename; ?>" alt="QR" style="height: 50px;">
                                                </div>
                                            </div>
                                        </div>
                                        <?php endwhile; ?>
                                    </div>
                                    <?php else: ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </main>
        </div>
    </div>
    <?php include '../inc/js.php'; ?>
    <script src="../assets/html2pdf.js/dist/html2pdf.bundle.min.js"></script>
    <script>
    function exportPDF() {
        var element = document.getElementById('canvas_div_pdf');
        html2pdf().set({
            margin: 0.2,
            filename: 'KartuUjianCbt.pdf',
            image: {
                type: 'jpeg',
                quality: 1
            },
            html2canvas: {
                scale: 2,
                logging: true
            },
            jsPDF: {
                unit: 'in',
                format: 'a4',
                orientation: 'landscape'
            }
        }).from(element).save();
    }
    </script>
</body>

</html>