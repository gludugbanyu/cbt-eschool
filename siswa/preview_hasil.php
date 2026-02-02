<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('siswa'); // Pastikan siswa sudah login
include '../inc/datasiswa.php';

// Check if scores are hidden in settings
$setting_query = mysqli_query($koneksi, "SELECT sembunyikan_nilai FROM pengaturan LIMIT 1");
$setting = mysqli_fetch_assoc($setting_query);

if ($setting['sembunyikan_nilai'] == 1) {
    $_SESSION['error'] = 'Nilai telah disembunyikan oleh administrator';
    header('Location: hasil.php');
    exit;
}
if (!isset($_GET['kode_soal']) || !isset($_GET['id_siswa'])) {
    echo "Parameter kode_soal dan id_siswa harus ada.";
    exit;
}

$kode_soal = $_GET['kode_soal'];
$id_siswa = $_GET['id_siswa'];

// Ambil data nilai, nama siswa dan tanggal ujian dari tabel nilai
$query_nilai = mysqli_query($koneksi, "SELECT * FROM nilai WHERE kode_soal='$kode_soal' AND id_siswa='$id_siswa' LIMIT 1");
if (!$query_nilai || mysqli_num_rows($query_nilai) == 0) {
    echo "Data nilai siswa tidak ditemukan.";
    exit;
}
$row_nilai = mysqli_fetch_assoc($query_nilai);
$jawaban_siswa_raw = $row_nilai['jawaban_siswa'];
$nilai_otomatis = $row_nilai['nilai'] ?? '-';
$nilai_uraian = $row_nilai['nilai_uraian'] ?? '-';
$nilai_siswa = $nilai_otomatis+$nilai_uraian;
$nama_siswa = $row_nilai['nama_siswa'] ?? '-';
$tanggal_ujian = $row_nilai['tanggal_ujian'] ?? '-';
// PARSE DETAIL URAIAN
$detail_uraian = $row_nilai['detail_uraian'] ?? '';
preg_match_all('/\[(\d+):([\d.]+)\]/', $detail_uraian, $matches);
$skor_uraian = array_combine($matches[1], $matches[2]);

function parseJawabanSiswa($str) {
    $pattern = '/\[(\d+):([^\]]*)\]/';
    preg_match_all($pattern, $str, $matches, PREG_SET_ORDER);
    $hasil = [];
    foreach ($matches as $m) {
        $no = (int)$m[1];
        $jawab = trim($m[2]);
        $hasil[$no] = $jawab;
    }
    return $hasil;
}

$jawaban_siswa = parseJawabanSiswa($jawaban_siswa_raw);

// Get answer key and calculate scores per question
$query_kunci = mysqli_query($koneksi, "SELECT kunci FROM soal WHERE kode_soal='$kode_soal'");
$data_kunci = mysqli_fetch_assoc($query_kunci);
$kunci_jawaban = $data_kunci['kunci'] ?? '';

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

$skor_per_soal = [];
if (!empty($kunci_jawaban)) {
    $kuncifix = removeCommasOutsideBrackets($kunci_jawaban);
    preg_match_all('/\[(.*?)\]/', $kuncifix, $kunci_matches);
    $kunci_array = $kunci_matches[1];
    
    $total_soal = count($kunci_array);
    $nilai_per_soal = $total_soal > 0 ? 100 / $total_soal : 0;
    
    foreach ($kunci_array as $i => $item) {
        list($nomer_kunci, $isi_kunci) = explode(':', $item, 2);
        $nomer_kunci = (int)$nomer_kunci;
        $isi_jawaban = $jawaban_siswa[$nomer_kunci] ?? '';
        
        $q_tipe = mysqli_query($koneksi, "SELECT tipe_soal FROM butir_soal WHERE kode_soal = '$kode_soal' AND nomer_soal = '$nomer_kunci'");
        $data_tipe = mysqli_fetch_assoc($q_tipe);
        $tipe_soal = strtolower($data_tipe['tipe_soal'] ?? '');

        if($tipe_soal === 'uraian') {
            $skor_per_soal[$nomer_kunci] = (float)($skor_uraian[$nomer_kunci] ?? 0);
            continue;
        }
        $skor = 0;
        
        if (in_array($tipe_soal, ['benar/salah', 'menjodohkan'])) {
            $kunci_opsi = array_map('strtolower', array_map('trim', explode('|', $isi_kunci)));
            $jawaban_opsi = array_map('strtolower', array_map('trim', explode('|', $isi_jawaban)));
            $jumlah_kunci = count($kunci_opsi);
            $nilai_per_opsi = $nilai_per_soal / $jumlah_kunci;
            $jumlah_benar = 0;
            
            for ($j = 0; $j < $jumlah_kunci; $j++) {
                if (isset($jawaban_opsi[$j]) && $kunci_opsi[$j] === $jawaban_opsi[$j]) {
                    $jumlah_benar++;
                }
            }
            $skor = $jumlah_benar * $nilai_per_opsi;
            
        } elseif ($tipe_soal === 'pilihan ganda kompleks') {
            $kunci_opsi = array_map('strtolower', array_map('trim', explode(',', str_replace('|', ',', $isi_kunci))));
            $jawaban_opsi = array_map('strtolower', array_map('trim', explode(',', str_replace('|', ',', $isi_jawaban))));
            
            $jumlah_kunci = count($kunci_opsi);
            $jumlah_benar = 0;
            
            foreach ($jawaban_opsi as $jawab) {
                if (!in_array($jawab, $kunci_opsi)) {
                    $skor = 0;
                    goto selesai_pilgan_kompleks;
                }
            }
            
            foreach ($jawaban_opsi as $jawab) {
                if (in_array($jawab, $kunci_opsi)) $jumlah_benar++;
            }
            
            if ($jumlah_benar === $jumlah_kunci) {
                $skor = $nilai_per_soal;
            } else {
                $skor = ($jumlah_benar / $jumlah_kunci) * $nilai_per_soal;
            }
            
            selesai_pilgan_kompleks:
            ;
            
        } else {
            // PG tunggal atau uraian
            if (strtolower(trim($isi_kunci)) === strtolower(trim($isi_jawaban))) {
                $skor = $nilai_per_soal;
            }
        }
        
        $skor_per_soal[$nomer_kunci] = $skor;
    }
}

$query_soal = mysqli_query($koneksi, "SELECT * FROM butir_soal WHERE kode_soal='$kode_soal' ORDER BY nomer_soal ASC");
$q_opsi = mysqli_query($koneksi, "SELECT jumlah_opsi FROM soal WHERE kode_soal='$kode_soal'");
$d_opsi = mysqli_fetch_assoc($q_opsi);
$jumlah_opsi = (int)($d_opsi['jumlah_opsi'] ?? 4);

$opsi_huruf = ['A','B','C','D','E'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview Jawaban Siswa</title>
    <?php include '../inc/css.php'; ?>
    <style>
    /* style tambahan untuk header 2 kolom */
    .header-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
        border-bottom: 1px solid #ccc;
        padding-bottom: 10px;
    }

    .header-left,
    .header-right {
        width: 48%;
        font-weight: bold;
        font-size: 16px;
        line-height: 1.5;
    }

    .header-right {
        border: 1px solid #aaa;
        padding: 10px;
        height: 72px;
        /* kira-kira 3 baris dengan line-height 1.5 * 16px font-size */
        box-sizing: border-box;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        background-color: #f7f7f7;
    }

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

    table {
        width: 100%;
        border-collapse: collapse;
    }

    table th,
    table td {
        border: 1px solid #aaa;
        padding: 6px;
    }

    .pembahasan {
        background-color: rgb(213, 213, 213);
        background-image: radial-gradient(rgb(255, 255, 255) 1px, transparent 1px);
        background-size: 20px 20px;
        padding: 10px;
        margin-top: 10px;
        border-radius: 5px;
        color: rgb(0, 0, 0);
        font-style: italic;
        white-space: pre-wrap;
    }

    .skor-soal {
        background-color: #e8f4f8;
        padding: 8px;
        margin-top: 5px;
        border-radius: 5px;
        font-weight: bold;
    }

    ul {
        list-style-type: none;
        padding-left: 0;
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
                    <h1>Preview Hasil Ujian</h1>
                    <div class="row mb-4">
                        <div class="card-header">
                            <button type="button" class="btn btn-outline-danger" onclick="exportPDF()"><i
                                    class="fa-solid fa-file-pdf"></i> Download PDF</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="printModalContent()"><i
                                    class="fa fa-print"></i> Print</button>
                            <a href="hasil.php"><button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal">Kembali</button></a>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="col-lg-12 card-utama" id="canvas_div_pdf">
                            <!-- HEADER 2 KOLOM -->
                            <div class="row mb-4"
                                style="max-height:300px;background-color: #444; color: white; border-radius: 10px; padding: 20px;">
                                <div class="col-md-9 col-6">
                                    <p><strong>Nama Siswa:</strong> <?= htmlspecialchars($nama_siswa) ?></p>
                                    <p><strong>Kode Soal:</strong> <?= htmlspecialchars($kode_soal) ?></p>
                                    <p><strong>Tanggal Ujian:</strong> <?= htmlspecialchars($tanggal_ujian) ?></p>
                                </div>
                                <div
                                    class="col-md-3 col-6 text-center d-flex align-items-center justify-content-center">
                                    <div
                                        style="background-color: white; color: black; padding: 20px; border-radius: 15px; width: 100%; height: 100%;">
                                        <h4 class="mb-0">Nilai</h4>
                                        <h1 style="font-size: 30px;"><?= $nilai_siswa ?></h1>
                                    </div>
                                </div>
                            </div>

                            <?php while ($soal = mysqli_fetch_assoc($query_soal)): 
                    $no = (int)$soal['nomer_soal'];
                    $jawab = isset($jawaban_siswa[$no]) ? $jawaban_siswa[$no] : '';
                    $tipe = $soal['tipe_soal'];
                ?>
                            <div class="row">
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5>No. <?= $no ?> (<?= $tipe ?>)</h5>
                                        <p><?= $soal['pertanyaan'] ?></p>
                                        <?php if (!empty($soal['gambar'])): ?>
                                        <img src="../assets/img/butir_soal/<?= $soal['gambar'] ?>" alt="Gambar Soal" />
                                        <?php endif; ?>

                                        <h6>Jawaban Siswa:</h6>
                                        <?php
                        switch ($tipe) {
                            case 'Pilihan Ganda':
                                echo "<ul>";
                                for ($i=1; $i<=$jumlah_opsi; $i++) {
                                    if (empty($soal["pilihan_$i"])) continue;
                                
                                    $huruf = $opsi_huruf[$i-1];
                                    $checked = ($jawab == "pilihan_$i") ? "✓" : "";
                                    echo "<li>$huruf. " . $soal["pilihan_$i"] . " $checked</li>";
                                }
                                
                                echo "</ul>";
                                $benar_num = (int)str_replace("pilihan_", "", $soal['jawaban_benar']);
                                $benar_huruf = $opsi_huruf[$benar_num - 1];
                                echo '<div class="pembahasan"><strong>Pembahasan:</strong><br>Jawaban benar: ' . $benar_huruf . '</div>';
                                break;

                            case 'Pilihan Ganda Kompleks':
                                $jawaban_arr = array_map('trim', explode(',', $jawab));
                                echo "<ul>";
                                for ($i=1; $i<=$jumlah_opsi; $i++) {
                                    if (empty($soal["pilihan_$i"])) continue;
                                
                                    $huruf = $opsi_huruf[$i-1];
                                    $checked = in_array("pilihan_$i", $jawaban_arr) ? "✓" : "";
                                    echo "<li>$huruf. " . $soal["pilihan_$i"] . " $checked</li>";
                                }
                                
                                echo "</ul>";
                                $kunci_arr = array_map('trim', explode(',', $soal['jawaban_benar']));
                                $huruf_benar = [];
                                foreach ($kunci_arr as $k) {
                                    $num = (int)str_replace("pilihan_", "", $k);
                                    $huruf_benar[] = $opsi_huruf[$num - 1];
                                }
                                echo '<div class="pembahasan"><strong>Pembahasan:</strong><br>Jawaban benar: ' . implode(', ', $huruf_benar) . '</div>';
                                break;

                            case 'Benar/Salah':
                                $pernyataan = [];
                                for ($i=1; $i<=5; $i++) {
                                    if (!empty($soal["pilihan_$i"])) {
                                        $pernyataan[] = $soal["pilihan_$i"];
                                    }
                                }
                                
                                $jawab_arr = explode('|', $jawab);
                                echo "<table><thead><tr><th>#</th><th>Pernyataan</th><th>Benar</th><th>Salah</th></tr></thead><tbody>";
                                foreach ($pernyataan as $i => $text) {
                                    $val = isset($jawab_arr[$i]) ? $jawab_arr[$i] : '';
                                    echo "<tr><td>" . ($i+1) . "</td><td>" . $text . "</td><td>" . ($val == "Benar" ? "✓" : "") . "</td><td>" . ($val == "Salah" ? "✓" : "") . "</td></tr>";
                                }
                                echo "</tbody></table>";

                               $kunci_arr = explode('|', $soal['jawaban_benar']);
                                echo '<div class="pembahasan"><strong>Pembahasan:</strong><br>';
                                foreach ($pernyataan as $i => $text) {
                                    $nilai = $kunci_arr[$i] ?? '-';
                                    echo "Pernyataan " . ($i + 1) . ": " . htmlspecialchars($nilai) . "<br>";
                                }
                                echo '</div>';
                                break;

                            case 'Menjodohkan':
                                // Tampilkan jawaban siswa dalam tabel
                                $pairs = explode('|', $jawab);
                                echo "<table border='1' cellpadding='5' cellspacing='0'><thead><tr><th>#</th><th>Pilihan</th><th>Pasangan</th></tr></thead><tbody>";
                                foreach ($pairs as $i => $pair) {
                                    list($a, $b) = explode(':', $pair) + [null, null];
                                    echo "<tr><td>" . ($i + 1) . "</td><td>" . htmlspecialchars($a) . "</td><td>" . htmlspecialchars($b) . "</td></tr>";
                                }
                                echo "</tbody></table>";

                                // Tampilkan pembahasan (kunci jawaban) juga dalam tabel
                                $kunci_pairs = explode('|', $soal['jawaban_benar']);
                                echo '<div class="pembahasan"><strong>Pembahasan:</strong>';
                                echo "<table border='1' cellpadding='5' cellspacing='0' style='margin-top:10px;'>";
                                echo "<thead><tr><th>#</th><th>Pilihan</th><th>Pasangan</th></tr></thead><tbody>";
                                foreach ($kunci_pairs as $i => $pair) {
                                    list($a, $b) = explode(':', $pair) + [null, null];
                                    echo "<tr><td>" . ($i + 1) . "</td><td>" . htmlspecialchars($a) . "</td><td>" . htmlspecialchars($b) . "</td></tr>";
                                }
                                echo "</tbody></table>";
                                echo '</div>';
                                break;

                            case 'Uraian':
                                echo "<div class='border p-2 mb-2'>" . nl2br(htmlspecialchars($jawab)) . "</div>";
                                echo '<div class="pembahasan"><strong>Pembahasan:</strong><br>' . nl2br(htmlspecialchars($soal['jawaban_benar'])) . '</div>';
                                break;

                            default:
                                echo '<div>Jawaban tidak tersedia untuk tipe soal ini.</div>';
                                break;
                        }
                        ?>
                                        <!-- Tambahkan skor per soal di sini -->
                                        <div class="skor-soal">
                                            <strong>Skor:</strong> <?= number_format($skor_per_soal[$no] ?? 0, 2) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                            <p class="text-center" id="encr" style="font-size:11px;color:grey;"></p>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <?php include '../inc/js.php'; ?>
    <?php include '../inc/check_activity.php'; ?>
    <script src="../assets/html2pdf.js/dist/html2pdf.bundle.min.js"></script>
    <script>
    function exportPDF() {
        var element = document.getElementById('canvas_div_pdf');
        html2pdf().set({
            margin: 0.2,
            filename: '<?php echo $kode_soal;?>_<?php echo $nama_siswa;?>.pdf',
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