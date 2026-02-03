<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
// Cek jika sudah login
check_login('admin');
include '../inc/dataadmin.php';

if (!isset($_GET['kode_soal'])) {
    header('Location: soal.php');
    exit();
}

$kode_soal = $_GET['kode_soal'];
only_pemilik_soal_by_kode($kode_soal);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Soal</title>
    <?php include '../inc/css.php'; ?>
    <script src="../assets/js/jquery-3.6.0.min.js"></script>
    <link href="../assets/summernote/summernote-bs5.css" rel="stylesheet">
    <style>
    .card img {
        height: auto;
        width: auto;
        object-fit: contain;
        max-width: 450px !important;
        max-height: 300px !important;
        display: block;
    }
    @media (max-width: 768px) {
    .card img {
        width: 100% !important;
        
        }
    }

    label.note-form-label {
        display: none !important;
    }

    table th,
    table td {
        text-align: left !important;
    }

    .form-check-label strong {
        width: 24px;
        display: inline-block;
    }

    .card-utama {
        background-color: #fff !important;
        color: #000;
        padding: 30px;
    }

    .mb-4.p-3.border.rounded.bg-white {
        border: 1px solid grey !important;
        border-bottom: 1px solid grey !important;
        border-radius: 10px !important;
    }

    .custom-card {
        border: 1px solid #343a40;
        border-radius: 10px;
    }

    .custom-card-header {
        border-bottom: 1px solid #343a40;
        background-color: #f8f9fa;
        padding: 10px;
        font-weight: bold;
    }

    .custom-radio-spacing {
        margin-right: 100px;
    }

    input[type="radio"]:not(:checked) {
        border-color: black;
    }

    input[type="radio"]:checked {
        background-color: green;
        border-color: green;
    }

    input[type="checkbox"]:not(:checked) {
        border-color: black;
    }

    input[type="checkbox"]:checked {
        background-color: green;
        border-color: green;
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
                        <div class="col-lg-9">
                            <div class="card">
                                <div class="card-header">
                                    <button type="button" class="btn btn-outline-danger" onclick="exportPDF()"><i
                                            class="fa-solid fa-file-pdf"></i> Download PDF</button>
                                    <button type="button" class="btn btn-outline-secondary"
                                        onclick="printModalContent()"><i class="fa fa-print"></i> Print</button>
                                    <a href="daftar_butir_soal.php?kode_soal=<?php echo $kode_soal;?>"><button
                                            type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Kembali</button></a>
                                </div>
                                <div class="card-body card-utama" id="canvas_div_pdf">
                                    <?php
                                    $query_info = mysqli_query($koneksi, "SELECT * FROM soal WHERE kode_soal='$kode_soal' LIMIT 1");
                                    $info_soal = mysqli_fetch_assoc($query_info);
                                    $jumlah_opsi = intval($info_soal['jumlah_opsi'] ?? 4);
$opsi_huruf_full = ['A','B','C','D','E'];


                                    $tipe_soal_count = [];
                                    $query_tipe_soal = mysqli_query($koneksi, "SELECT tipe_soal, COUNT(*) as jumlah FROM butir_soal WHERE kode_soal='$kode_soal' GROUP BY tipe_soal");
                                    while ($row = mysqli_fetch_assoc($query_tipe_soal)) {
                                        $tipe_soal_count[$row['tipe_soal']] = $row['jumlah'];
                                    }

                                    $query_jumlah = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM butir_soal WHERE kode_soal='$kode_soal'");
                                    $jumlah_soal = mysqli_fetch_assoc($query_jumlah)['total'];
                                    ?>

                                    <div class="card custom-card mb-3" style="max-width: 28rem">
                                        <div class="card-header custom-card-header">&nbsp;&nbsp;Detail Soal</div>
                                        <div class="card-body text-dark">
                                            <h3 class="card-title text-dark"><strong>Kode Soal:</strong>
                                                <?= htmlspecialchars($info_soal['kode_soal']) ?></h3>
                                            <h5>Mapel:</strong> <?= htmlspecialchars($info_soal['mapel']) ?></h5>
                                            <h5>Jumlah Soal:
                                                <?php
                                                $total_soal = array_sum($tipe_soal_count);
                                                echo $total_soal;
                                                ?>
                                                |
                                                <?php
                                                $tipe_soal_list = [];
                                                foreach ($tipe_soal_count as $tipe => $count) {
                                                    switch ($tipe) {
                                                        case 'Pilihan Ganda':
                                                            $tipe = 'PG';
                                                            break;
                                                        case 'Pilihan Ganda Kompleks':
                                                            $tipe = 'PGX';
                                                            break;
                                                        case 'Benar/Salah':
                                                            $tipe = 'BS';
                                                            break;
                                                        case 'Menjodohkan':
                                                            $tipe = 'MJD';
                                                            break;
                                                        case 'Uraian':
                                                            $tipe = 'U';
                                                            break;
                                                    }
                                                    $tipe_soal_list[] = htmlspecialchars($tipe) . " " . $count;
                                                }
                                                echo implode(" | ", $tipe_soal_list);
                                                ?>
                                            </h5>
                                        </div>
                                    </div>

                                    <?php
                                    $query_preview = mysqli_query($koneksi, "SELECT * FROM butir_soal WHERE kode_soal='$kode_soal' ORDER BY nomer_soal ASC");
                                    while ($soal = mysqli_fetch_assoc($query_preview)) {
                                        echo "<div class='mb-4 p-3 border rounded bg-white oke'>";
                                        echo "<h5 style='background-color:grey;padding:5px;color:white'><b style='padding:5px;background-color:black;color:white;'>No. " . htmlspecialchars($soal['nomer_soal']) . "</b>  <i> (" . htmlspecialchars($soal['tipe_soal']) . ")</i></h5>";

                                        $jawaban_benar = $soal['jawaban_benar'];

                                        if ($soal['tipe_soal'] === 'Pilihan Ganda') {
                                            echo "<p class='text-dark'>" . $soal['pertanyaan'] . "</p>";
                                            for ($i = 1; $i <= $jumlah_opsi; $i++) {
                                                $opsi_label = $opsi_huruf_full[$i - 1];
                                                $opsi_nama = 'pilihan_' . $i;
                                                $nilai = $soal[$opsi_nama];
if (empty(trim($nilai))) continue;

$checked = (trim($soal['jawaban_benar']) === $opsi_nama) ? 'checked' : '';

echo "
<table style='width:100%; margin-bottom:6px;'>
<tr>
    <td style='width:30px; vertical-align:top; font-weight:bold;'>$opsi_label.</td>
    <td style='width:30px; vertical-align:top;'>
        <input class='form-check-input' type='radio' $checked onclick='return false;'>
    </td>
    <td style='vertical-align:top;'>$nilai</td>
</tr>
</table>";

                                            }
                                        } elseif ($soal['tipe_soal'] === 'Pilihan Ganda Kompleks') {
                                            echo "<p class='text-dark'>" . $soal['pertanyaan'] . "</p>";
                                            $jawaban_benar = array_map('trim', explode(',', $soal['jawaban_benar']));
                                            for ($i = 1; $i <= $jumlah_opsi; $i++) {
                                                $opsi_label = $opsi_huruf_full[$i - 1];
                                                $opsi_nama = 'pilihan_' . $i;
                                                $nilai = $soal[$opsi_nama];
if (empty(trim($nilai))) continue;

$checked = in_array($opsi_nama, $jawaban_benar) ? 'checked' : '';

echo "
<table style='width:100%; margin-bottom:6px;'>
<tr>
    <td style='width:30px; vertical-align:top; font-weight:bold;'>$opsi_label.</td>
    <td style='width:30px; vertical-align:top;'>
        <input class='form-check-input' type='checkbox' $checked onclick='return false;'>
    </td>
    <td style='vertical-align:top;'>$nilai</td>
</tr>
</table>";

                                            }
                                        } elseif ($soal['tipe_soal'] === 'Menjodohkan') {
                                            echo "<p class='text-dark'>" . $soal['pertanyaan'] . "</p>";
                                            $pasangan = explode('|', $jawaban_benar);
                                            echo "<table class='table-menjodohkan-print' style='width:100%; border-collapse: collapse; margin-top:10px;'>";
                                            echo "<thead><tr>
                                                    <th style='border:1px solid #000; padding:5px; background:#f0f0f0;'>Pilihan</th>
                                                    <th style='border:1px solid #000; padding:5px; background:#f0f0f0;'>Pasangan</th>
                                                </tr></thead><tbody>";
                                            foreach ($pasangan as $pair) {
                                                if (strpos($pair, ':') !== false) {
                                                    [$kiri, $kanan] = explode(':', $pair, 2);
                                                    echo "<tr>
                                                            <td style='border:1px solid #000; padding:5px;'>$kiri</td>
                                                            <td style='border:1px solid #000; padding:5px;'>$kanan</td>
                                                        </tr>";
                                                }
                                            }
                                            echo "</tbody></table>";
                                        } elseif ($soal['tipe_soal'] === 'Benar/Salah') {
                                            echo "<p class='text-dark'>" . $soal['pertanyaan'] . "</p>";
                                        
                                            $jawaban_benar = array_map('trim', explode('|', $soal['jawaban_benar']));
                                        
                                            echo "<table style='width:100%; border-collapse:collapse; margin-top:10px;'>
                                            <thead>
                                                <tr style='background-color:#f0f0f0;'>
                                                    <th style='border:1px solid black; padding:8px;'>Pernyataan</th>
                                                    <th style='border:1px solid black; padding:8px; text-align:center;'>Benar</th>
                                                    <th style='border:1px solid black; padding:8px; text-align:center;'>Salah</th>
                                                </tr>
                                            </thead>
                                            <tbody>";
                                        
                                            for ($i = 1; $i <= 5; $i++) {
                                                $nilai = trim($soal['pilihan_'.$i]);
                                                if ($nilai === '') continue;
                                        
                                                $index = $i - 1;
                                                $is_benar = isset($jawaban_benar[$index]) && $jawaban_benar[$index] === 'Benar';
                                                $is_salah = isset($jawaban_benar[$index]) && $jawaban_benar[$index] === 'Salah';
                                        
                                                echo "<tr>
                                                    <td style='border:1px solid black; padding:8px;'>$nilai</td>
                                                    <td style='border:1px solid black; text-align:center;padding-left:10px;'>
                                                        <input class='form-check-input' type='radio' ".($is_benar?'checked':'')." onclick='return false;'>
                                                    </td>
                                                    <td style='border:1px solid black; text-align:center;padding-left:10px;'>
                                                        <input class='form-check-input' type='radio' ".($is_salah?'checked':'')." onclick='return false;'>
                                                    </td>
                                                </tr>";
                                            }
                                        
                                            echo "</tbody></table>";
                                        }
                                         elseif ($soal['tipe_soal'] === 'Uraian') {
                                            echo "<p class='text-dark'>Pertanyaan : " . $soal['pertanyaan'] . "</p>";
                                            echo "<p class='mt-2'>Jawaban Benar: <span class='text-dark'>" . $soal['jawaban_benar'] . "</span></p>";
                                        }

                                        echo "</div>";
                                    }
                                    ?>
                                     <p class="text-center" id="encr" style="font-size:11px;color:grey;"></p>
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
            filename: 'Soal_<?php echo $kode_soal;?>.pdf',
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
                orientation: 'portrait'
            }
        }).from(element).save();
    }

    document.addEventListener("DOMContentLoaded", function() {
        const images = document.querySelectorAll('.card-utama img');

        images.forEach(function(img) {
            img.style.maxWidth = '200px';
            img.style.maxHeight = '200px';
        });
    });

    function printModalContent() {
        const modalBody = document.querySelector('.card-utama').innerHTML;
        const printWindow = window.open('', '', 'width=1000,height=700');

        printWindow.document.write(`
                <html>
                    <head>
                        <title>Print Preview</title>
                        <link rel="stylesheet" href="../inc/print-soal.css" type="text/css" />
                    </head>
                    <body onload="window.print(); window.close();">
                        ${modalBody}
                    </body>
                </html>
            `);

        printWindow.document.close();
    }
    </script>
</body>

</html>