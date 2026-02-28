<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
require_once '../assets/phpqrcode/qrlib.php';
include '../inc/dataadmin.php';
check_login('admin');

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
$logoPath = '../assets/images/kemdikbud.png';
$logoData = base64_encode(file_get_contents($logoPath));
$logoSrc = 'data:image/png;base64,' . $logoData;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Hadir</title>
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
                        <div class="col-12">
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Form Cetak Daftar Hadir</h5>
                                    <button class="btn btn-sm btn-outline-secondary" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#formCetak" aria-expanded="false"
                                        aria-controls="formCetak">
                                        Tampilkan Form
                                    </button>
                                </div>
                                <div class="card-body collapse" id="formCetak">
                                    <form method="POST">
                                        <div class="row mb-3">
                                            <div class="col-md-6 mt-3">
                                                <label for="kode_soal" class="form-label">Kode Soal</label>
                                                <select name="kode_soal" id="kode_soal" class="form-control" required>
                                                    <option value="">Pilih Kode Soal</option>
                                                    <?php
                                                    $soalQuery = mysqli_query($koneksi, "SELECT * FROM soal");
                                                    while ($soal = mysqli_fetch_assoc($soalQuery)) {
                                                        echo "<option value='{$soal['kode_soal']}'>{$soal['kode_soal']} - {$soal['mapel']}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3 mt-3">
                                                <label for="kelas" class="form-label">Kelas</label>
                                                <select name="kelas" id="kelas" class="form-control" required>
                                                    <option value="">Pilih</option>
                                                    <?php
                                                    $kelasQuery = mysqli_query($koneksi, "SELECT DISTINCT kelas FROM siswa ORDER BY kelas ASC");
                                                    while ($k = mysqli_fetch_assoc($kelasQuery)) {
                                                        echo "<option value='{$k['kelas']}'>{$k['kelas']}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3 mt-3">
                                                <label for="rombel" class="form-label">Rombel</label>
                                                <select name="rombel" id="rombel" class="form-control" required>
                                                    <option value="">Pilih</option>
                                                    <?php
                                                    $rombelQuery = mysqli_query($koneksi, "SELECT DISTINCT rombel FROM siswa ORDER BY rombel ASC");
                                                    while ($r = mysqli_fetch_assoc($rombelQuery)) {
                                                        echo "<option value='{$r['rombel']}'>{$r['rombel']}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <label for="nama_ujian" class="form-label">Nama Ujian</label>
                                                <input type="text" name="nama_ujian" id="nama_ujian"
                                                    class="form-control" required>
                                            </div>
                                            <div class="col-md-6 mt-3">
                                                <label for="nama_sekolah" class="form-label">Nama Sekolah</label>
                                                <input type="text" name="nama_sekolah" id="nama_sekolah"
                                                    class="form-control" required>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label for="pengawas" class="form-label">Nama Pengawas</label>
                                                <textarea name="pengawas" id="pengawas" class="form-control" rows="3"
                                                    placeholder="Pisahkan dengan baris baru jika lebih dari satu"
                                                    required></textarea>
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <button type="submit" name="tampilkan"
                                                    class="btn btn-secondary w-100">Tampilkan</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <?php if (isset($_POST['tampilkan'])): ?>
                            <?php
                                $kode_soal = mysqli_real_escape_string($koneksi, $_POST['kode_soal']);
                                $kelas = mysqli_real_escape_string($koneksi, $_POST['kelas']);
                                $rombel = mysqli_real_escape_string($koneksi, $_POST['rombel']);
                                $nama_ujian = mysqli_real_escape_string($koneksi, htmlspecialchars($_POST['nama_ujian']));
                                $nama_sekolah = mysqli_real_escape_string($koneksi, htmlspecialchars($_POST['nama_sekolah']));
                                $pengawas_input = trim($_POST['pengawas']);
                                $daftar_pengawas = preg_split('/\r\n|\r|\n/', $pengawas_input, -1, PREG_SPLIT_NO_EMPTY);

                                $soalInfo = mysqli_query($koneksi, "SELECT * FROM soal WHERE kode_soal = '$kode_soal' LIMIT 1");
                                $soal = mysqli_fetch_assoc($soalInfo);

                                $siswaQuery = mysqli_query($koneksi, "SELECT * FROM siswa WHERE kelas='$kelas' AND rombel='$rombel' ORDER BY nama_siswa ASC");

                                $tanggal_hari_ini = date('d-m-Y');
                                ?>
                            <div class="card">
                                <div class="card-header d-flex justify-content-between">
                                    <div>
                                        <button onclick="exportPDF()" class="btn btn-danger btn-sm me-2"><i
                                                class="fas fa-file-pdf"></i> Export PDF</button>
                                        <button onclick="printDiv()" class="btn btn-secondary btn-sm"><i
                                                class="fas fa-print"></i> Cetak</button>

                                    </div>
                                </div>

                                <div class="card-body" id="canvas_div_pdf">

                                    <table style="width: 100%; margin-bottom: 10px; border-collapse: collapse;">
                                        <tr>
                                            <!-- Logo di kiri -->
                                            <td style="width: 80px; vertical-align: middle;">
                                                <img src="<?= $logoSrc ?>" alt="Logo" style="height: 60px;">
                                            </td>

                                            <!-- Judul di tengah -->
                                            <td style="text-align: center;">
                                                <h4 style="margin-bottom: 5px;text-align: center;">DAFTAR HADIR</h4>
                                                <h3 style="margin-bottom: 5px;text-align: center;">
                                                    <?= strtoupper($nama_ujian) ?></h3>
                                                <h4 style="margin-bottom: 0;text-align: center;">
                                                    <?= strtoupper($nama_sekolah) ?></h4>
                                            </td>

                                            <!-- Kolom kanan kosong untuk keseimbangan -->
                                            <td style="width: 80px;"></td>
                                        </tr>
                                    </table>

                                    <hr style="border-top: 4px double black;">
                                    <table class="table table-borderless mb-3" style="width: 100%; font-size: 12px;">
                                        <tr>
                                            <!-- Kolom kiri -->
                                            <td style="width: 50%;">
                                                <table style="width: 100%;">
                                                    <tr>
                                                        <td><strong>Kode Soal</strong></td>
                                                        <td class="px-2">:</td>
                                                        <td><?= $soal['kode_soal'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Waktu Ujian</strong></td>
                                                        <td class="px-2">:</td>
                                                        <td><?= $soal['waktu_ujian'] ?> menit</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Tanggal</strong></td>
                                                        <td class="px-2">:</td>
                                                        <td><?= $tanggal_hari_ini ?></td>
                                                    </tr>
                                                </table>
                                            </td>

                                            <!-- Kolom kanan -->
                                            <td style="width: 50%; text-align: right;">
                                                <table style="width: 100%;">
                                                    <tr>
                                                        <td style="text-align: right;"><strong>Mata Pelajaran</strong>
                                                        </td>
                                                        <td class="px-2 text-end">:</td>
                                                        <td class="text-end"><?= $soal['mapel'] ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="text-align: right;"><strong>Kelas / Rombel</strong>
                                                        </td>
                                                        <td class="px-2 text-end">:</td>
                                                        <td class="text-end"><?= $kelas ?> / <?= $rombel ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3">&nbsp;</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>

                                    <table class="table table-bordered mt-2">
                                        <thead class="table-dark">
                                            <tr>
                                                <th style="width: 5%;">No</th>
                                                <th>Nama Siswa</th>
                                                <th>Username</th>
                                                <th>Kelas</th>
                                                <th style="width: 25%;">Tanda Tangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $no = 1;
                                            while ($siswa = mysqli_fetch_assoc($siswaQuery)) {
                                                echo "<tr>
                                                        <td>{$no}</td>
                                                        <td>{$siswa['nama_siswa']}</td>
                                                        <td>{$siswa['username']}</td>
                                                        <td>{$siswa['kelas']}{$siswa['rombel']}</td>
                                                        <td>{$no}.</td>
                                                    </tr>";
                                                $no++;
                                            }
                                            ?>
                                        </tbody>
                                    </table>

                                    <!-- TAMPILKAN DAFTAR PENGAWAS DENGAN KOLOM TANDA TANGAN -->
                                    <div class="mt-5">
                                        <h6><strong>Pengawas Ujian:</strong></h6>
                                        <table class="table table-bordered" style="width: 100%;">
                                            <?php
                                                $totalPengawas = count($daftar_pengawas);
                                                for ($i = 0; $i < $totalPengawas; $i += 2) {
                                                    echo "<tr>";
                                                    // Kolom pertama
                                                    echo "<td style='height: 80px; width: 50%; vertical-align: bottom;'>
                                                            <div>" . htmlspecialchars($daftar_pengawas[$i]) . "</div>
                                                            <div style='margin-top: 40px;'>Tanda Tangan: ...................</div>
                                                        </td>";
                                                    // Kolom kedua (jika ada)
                                                    if (isset($daftar_pengawas[$i + 1])) {
                                                        echo "<td style='height: 80px; width: 50%; vertical-align: bottom;'>
                                                                <div>" . htmlspecialchars($daftar_pengawas[$i + 1]) . "</div>
                                                                <div style='margin-top: 40px;'>Tanda Tangan: ...................</div>
                                                            </td>";
                                                    } else {
                                                        echo "<td></td>";
                                                    }
                                                    echo "</tr>";
                                                }
                                                ?>
                                        </table>

                                    </div>
                                    <br><br>
                                    <p class="text-center" id="encr" style="font-size:9px;color:grey;"></p>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <?php include '../inc/js.php'; ?>
    <script src="../assets/html2pdf.js/dist/html2pdf.bundle.min.js"></script>
    <style>
    @media print {

        body,
        html {
            margin: 0;
            padding: 0;
        }

        body * {
            visibility: hidden !important;
        }

        #canvas_div_pdf,
        #canvas_div_pdf * {
            visibility: visible !important;
        }

        #canvas_div_pdf {
            width: 7.5in !important;
            /* Kunci ke 7.5in */
            padding: 0.2in !important;
            box-sizing: border-box !important;
            position: absolute;
            top: 0;
            left: 0;
            min-height: 100vh;
            margin: 0 !important;
        }

        #canvas_div_pdf table {
            width: 100%;
            table-layout: fixed;
            /* Pastikan semua kolom proporsional */
        }

        #canvas_div_pdf td,
        #canvas_div_pdf th {
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .btn,
        .btn * {
            display: none !important;
        }

        .card,
        .card-body {
            box-shadow: none !important;
            border: none !important;
        }

        .card-header {
            /* Tambahan penting */
            display: none !important;
        }
    }
    </style>
    <script>
    function printDiv() {
        window.print();
    }
    </script>
    <script>
function exportPDF() {

    var element = document.getElementById('canvas_div_pdf');

    // =========================
    // SIMPAN STATUS DARK MODE
    // =========================
    var htmlEl = document.documentElement;
    var bodyEl = document.body;

    var htmlHadDark = htmlEl.classList.contains('dark-mode');
    var bodyHadDark = bodyEl.classList.contains('dark-mode');

    // =========================
    // MATIKAN DARK MODE TOTAL
    // =========================
    htmlEl.classList.remove('dark-mode');
    bodyEl.classList.remove('dark-mode');

    // Paksa background putih
    element.style.background = "#ffffff";
    element.style.color = "#000000";

    var images = element.getElementsByTagName('img');
    var totalImages = images.length;
    var imagesLoaded = 0;

    if (totalImages === 0) generatePDF();
    else {
        for (var i = 0; i < totalImages; i++) {
            if (images[i].complete) {
                imagesLoaded++;
                if (imagesLoaded === totalImages) generatePDF();
            } else {
                images[i].addEventListener('load', function() {
                    imagesLoaded++;
                    if (imagesLoaded === totalImages) generatePDF();
                });
            }
        }
    }

    function generatePDF() {
        html2pdf().set({
            margin: [0.3, 0.5, 0.5, 0.5],
            filename: 'DaftarHadir_<?= $kode_soal ?>_<?= $kelas . $rombel ?>.pdf',
            image: { type: 'jpeg', quality: 1 },
            html2canvas: {
                scale: 2,
                backgroundColor: '#ffffff'
            },
            jsPDF: {
                unit: 'in',
                format: 'a4',
                orientation: 'portrait'
            }
        }).from(element).save().then(function () {

            // =========================
            // KEMBALIKAN DARK MODE
            // =========================
            if (htmlHadDark) htmlEl.classList.add('dark-mode');
            if (bodyHadDark) bodyEl.classList.add('dark-mode');

        });
    }
}
</script>
</body>

</html>