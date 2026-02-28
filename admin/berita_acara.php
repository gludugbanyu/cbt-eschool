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
    <title>Berita Acara</title>
    <?php include '../inc/css.php'; ?>
    <style>
    /* Style for Select2 */
    .select2-container--default .select2-selection--multiple {
        min-height: 70px !important;
        border: 1px solid #ced4da !important;
        /* Default Bootstrap border color */
        border-radius: 0.25rem !important;
        /* Bootstrap default border radius */
        padding: 0.375rem 0.75rem !important;
        /* Bootstrap default padding */
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        margin-top: 0.35rem !important;
        margin-right: 0.5rem !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__clear {
        margin-top: 0.5rem !important;
    }
    </style>
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
                                    <h5 class="card-title mb-0">Form Cetak Berita Acara</h5>
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
                                                    required></textarea>
                                                <small class="text-muted">Pisahkan dengan baris baru jika lebih dari
                                                    satu</small>
                                            </div>
                                            <div class="col-md-3 mt-3">
                                                <label for="jumlah_hadir" class="form-label">Jumlah Hadir</label>
                                                <input type="number" name="jumlah_hadir" id="jumlah_hadir"
                                                    class="form-control" required>
                                            </div>
                                            <div class="col-md-9 mt-3">
                                                <label for="catatan" class="form-label">Catatan</label>
                                                <input type="text" name="catatan" id="catatan" class="form-control">
                                            </div>
                                            <div class="col-md-12 mt-3">
                                                <label for="tidak_hadir" class="form-label">Siswa Tidak Hadir</label>
                                                <select name="tidak_hadir[]" id="tidak_hadir"
                                                    class="form-control select2" multiple="multiple"
                                                    style="width: 100%;height">
                                                    <option value="">Pilih Kelas dan Rombel Dulu</option>
                                                </select>
                                                <small class="text-muted">Pilih Kelas, rombel dan Ketik untuk mencari
                                                    atau pilih beberapa siswa</small>
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
                                $jumlah_hadir = $_POST['jumlah_hadir'];
                                $catatan = htmlspecialchars($_POST['catatan']);
                                $tidak_hadir = isset($_POST['tidak_hadir']) ? $_POST['tidak_hadir'] : [];        
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

                                <div class="card-body">
                                    <div class="pdf-scroll">
                                        <div id="canvas_div_pdf">

                                            <table style="width: 100%; margin-bottom: 10px; border-collapse: collapse;">
                                                <tr>
                                                    <!-- Logo di kiri -->
                                                    <td style="width: 80px; vertical-align: middle;">
                                                        <img src="<?= $logoSrc ?>" alt="Logo" style="height: 60px;">
                                                    </td>

                                                    <!-- Judul di tengah -->
                                                    <td style="text-align: center;">
                                                        <h4 style="margin-bottom: 5px;text-align: center;">BERITA ACARA
                                                            PELAKSANAAN</h4>
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
                                            <?php
                                // Mendapatkan informasi tanggal hari ini
                                $hariArray = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
                                $bulanArray = [
                                    '01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni',
                                    '07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'
                                ];

                                $hariIni = $hariArray[date('w')]; // 0 = Minggu, 6 = Sabtu
                                $tanggal = date('d');
                                $bulan = $bulanArray[date('m')];
                                $tahun = date('Y');
                            ?>
                                            <p style="text-align: justify; font-size: 13px;padding:10px;">
                                                Pada hari ini, <strong><?= $hariIni ?></strong> tanggal
                                                <strong><?= $tanggal ?></strong> bulan <strong><?= $bulan ?></strong>
                                                tahun
                                                <strong><?= $tahun ?></strong>,
                                                di <strong><?= strtoupper($nama_sekolah) ?></strong>, telah
                                                diselenggarakan
                                                <strong><?= strtoupper($nama_ujian) ?></strong> untuk mata pelajaran
                                                <strong><?= strtoupper($soal['mapel']) ?></strong> dengan kode soal
                                                <strong><?= $soal['kode_soal'] ?></strong>.
                                            </p>
                                            <table class="table table-bordered mb-3"
                                                style="width: 100%; font-size: 12px; border-collapse: collapse;">
                                                <tr>
                                                    <td style="width: 30%;"><strong>Kode Soal</strong></td>
                                                    <td>: <?= $soal['kode_soal'] ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Waktu Ujian</strong></td>
                                                    <td>: <?= $soal['waktu_ujian'] ?> menit</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Tanggal</strong></td>
                                                    <td>: <?= $tanggal_hari_ini ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Mata Pelajaran</strong></td>
                                                    <td>: <?= $soal['mapel'] ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Kelas / Rombel</strong></td>
                                                    <td>: <?= $kelas ?> / <?= $rombel ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Jumlah Hadir</strong></td>
                                                    <td>: <?= $jumlah_hadir ?></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Siswa Tidak Hadir</strong></td>
                                                    <td>
                                                        <?php
                                    if (count($tidak_hadir) > 0) {
                                        if (count($tidak_hadir) == 1) {
                                            // Jika hanya satu siswa, tampilkan tanpa koma
                                            echo $tidak_hadir[0];
                                        } else {
                                            // Jika lebih dari satu, gabungkan dengan koma sebagai pemisah
                                            echo implode(", ", $tidak_hadir);
                                        }
                                    } else {
                                        echo "Tidak ada.";
                                    }
                                    ?>
                                                    </td>
                                                </tr>
                                            </table>
                                            <div class="mt-4">
                                                <h6><strong>Catatan :</strong></h6>
                                                <table class="table table-bordered"
                                                    style="width: 100%;border-collapse: collapse;">
                                                    <tr>
                                                        <td style="text-align: center;height:100px;">
                                                            <?= nl2br($catatan) ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>


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
                                                        <div>{$daftar_pengawas[$i]}</div>
                                                        <div style='margin-top: 40px;'>Tanda Tangan: ...................</div>
                                                    </td>";
                                                // Kolom kedua (jika ada)
                                                if (isset($daftar_pengawas[$i + 1])) {
                                                    echo "<td style='height: 80px; width: 50%; vertical-align: bottom;'>
                                                            <div>{$daftar_pengawas[$i + 1]}</div>
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
                    </div>
                </div>
            </main>
        </div>
    </div>
    <?php include '../inc/js.php'; ?>
    <link href="../assets/js/select2.min.css" rel="stylesheet" />
    <script src="../assets/html2pdf.js/dist/html2pdf.bundle.min.js"></script>
    <script src="../assets/js/select2.min.js"></script>
    <script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Pilih siswa",
            allowClear: true
        });
    });
    </script>
    <style>
        .pdf-scroll {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

#canvas_div_pdf {
    min-width: 800px; /* atau 8.27in kalau mau presisi A4 */
}
    @media print {
        @page {
            size: A4 portrait;
            margin: 0;
        }

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
            width: 7.7in !important;
            margin: 0 auto !important;
            padding: 0 !important;
        }

        #canvas_div_pdf table {
            width: 100%;
            table-layout: fixed;
            /* Pastikan semua kolom proporsional */
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

    /* ===============================
   A4 FIX LAYOUT
================================ */

    #canvas_div_pdf {
        width: 8.27in;
        /* Lebar A4 */
        min-height: 11.69in;
        /* Tinggi A4 */
        margin: 0 auto;
        padding: 0.3in 0.5in 0.5in 0.5in;
        /* atas lebih kecil */
        background: #ffffff;
        box-sizing: border-box;
        overflow-x: hidden;
    }

    /* Hilangkan margin bawaan heading */
    #canvas_div_pdf h1,
    #canvas_div_pdf h2,
    #canvas_div_pdf h3,
    #canvas_div_pdf h4,
    #canvas_div_pdf h5,
    #canvas_div_pdf h6,
    #canvas_div_pdf p {
        margin-top: 0;
    }

    /* Table lebih presisi */
    #canvas_div_pdf table {
        width: 100%;
        border-collapse: collapse;
    }

    /* PRINT MODE */
    @media print {
        @page {
            size: A4 portrait;
            margin: 0;
        }

        body,
        html {
            margin: 0;
            padding: 0;
            background: #ffffff !important;
        }

        body * {
            visibility: hidden !important;
        }

        #canvas_div_pdf,
        #canvas_div_pdf * {
            visibility: visible !important;
        }

        #canvas_div_pdf {
            position: absolute;
            top: 0;
            left: 0;
        }

        .card-header,
        .btn {
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

        const element = document.getElementById('canvas_div_pdf');

        const htmlEl = document.documentElement;
        const bodyEl = document.body;

        // Simpan status dark mode
        const htmlHadDark = htmlEl.classList.contains('dark-mode');
        const bodyHadDark = bodyEl.classList.contains('dark-mode');

        // Matikan dark mode total
        htmlEl.classList.remove('dark-mode');
        bodyEl.classList.remove('dark-mode');

        // Paksa putih
        element.style.background = "#ffffff";
        element.style.color = "#000000";

        // Tunggu gambar load
        const images = element.getElementsByTagName('img');
        let loaded = 0;

        if (images.length === 0) {
            generatePDF();
        } else {
            for (let i = 0; i < images.length; i++) {
                if (images[i].complete) {
                    loaded++;
                    if (loaded === images.length) generatePDF();
                } else {
                    images[i].addEventListener('load', function() {
                        loaded++;
                        if (loaded === images.length) generatePDF();
                    });
                }
            }
        }

        function generatePDF() {

            html2pdf().set({
                margin: 0, // TIDAK ADA MARGIN
                filename: 'BeritaAcara_<?= $kode_soal ?>_<?= $kelas . $rombel ?>.pdf',
                image: {
                    type: 'jpeg',
                    quality: 1
                },
                html2canvas: {
                    scale: 2,
                    backgroundColor: '#ffffff'
                },
                jsPDF: {
                    unit: 'in',
                    format: 'a4',
                    orientation: 'portrait'
                }
            }).from(element).save().then(() => {

                // Kembalikan dark mode
                if (htmlHadDark) htmlEl.classList.add('dark-mode');
                if (bodyHadDark) bodyEl.classList.add('dark-mode');

                element.removeAttribute("style");
            });
        }
    }
    </script>
    </script>
    <script>
    $(document).ready(function() {
        function loadSiswa() {
            var kelas = $("#kelas").val();
            var rombel = $("#rombel").val();
            if (kelas !== "" && rombel !== "") {
                $.ajax({
                    url: "get_siswa.php",
                    type: "POST",
                    data: {
                        kelas: kelas,
                        rombel: rombel
                    },
                    success: function(data) {
                        $("#tidak_hadir").html(data);
                    }
                });
            }
        }

        $("#kelas, #rombel").change(function() {
            loadSiswa();
        });
    });
    </script>

</body>

</html>