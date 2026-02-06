<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
require_once '../assets/phpqrcode/qrlib.php';
include '../inc/dataadmin.php';

// Cek login
check_login('admin');
only_admin();

// Cek koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Folder QR
$qr_temp_dir = '../assets/temp_qr/';
if (!file_exists($qr_temp_dir)) {
    mkdir($qr_temp_dir, 0777, true);
}

// Filter data berdasarkan kelas & nama
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
$sql       = "SELECT * FROM siswa $where_sql ORDER BY nama_siswa ASC";
$result    = mysqli_query($koneksi, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Kartu Siswa</title>
    <style>
@page {
    size: A4 landscape;
    margin: 10mm;
}

body {
    margin: 0;
    padding: 0;
    font-family: sans-serif;
    font-size: 10pt;
}

.page {
    display: grid;
    grid-template-columns: repeat(3, 1fr); /* 3 kolom */
    grid-template-rows: repeat(3, 1fr);    /* 3 baris */
    gap: 10px;
    width: 100%;
    height: 100%;
    page-break-after: always;
    padding: 10px;
    box-sizing: border-box;
}

.kartu {
    border: 1px solid #ccc;
    padding: 8px;
    display: flex;
    flex-direction: column;
    font-size: 10pt;
    height: 100%;
    border-radius: 6px;
    background-color: #fdfdfd;
    box-shadow: 1px 1px 3px rgba(0,0,0,0.1);
}

.info table {
    width: 100%;
    border-collapse: collapse;
}

.info th,
.info td {
    padding: 2px 4px;
    vertical-align: middle;
    line-height: 1.2;
}

.info th {
    text-align: left;
    font-weight: bold;
    width: 30%;
}

@media print {
    .noprint {
        display: none;
    }
}
    </style>
</head>
<body>

<?php
include '../inc/encrypt.php';

$counter    = 0;
$total_rows = $result ? mysqli_num_rows($result) : 0;

if ($total_rows > 0):
    // Wrapper untuk html2pdf
    echo '<div id="canvas_div_pdf">';
    echo '<div class="page">';

    while ($row = mysqli_fetch_assoc($result)) {
        // Decode password terenkripsi
        $encoded = $row['password'];
        $decoded = base64_decode($encoded);
        $iv_length = openssl_cipher_iv_length($method);
        $iv2 = substr($decoded, 0, $iv_length);
        $encrypted_data = substr($decoded, $iv_length);
        $decrypted = openssl_decrypt($encrypted_data, $method, $rahasia, 0, $iv2);

        // QR code berdasarkan username
        $qr_filename = $qr_temp_dir . $row['username'] . '.png';
        if (!file_exists($qr_filename)) {
            QRcode::png($row['username'], $qr_filename, QR_ECLEVEL_L, 3);
        }

        $thn_sekarang  = date('Y');
        $thn_pelajaran = $thn_sekarang . '/' . ($thn_sekarang + 1);
        ?>
        <div class="kartu">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 20%;">
                        <center>
                            <img src="../assets/images/kemdikbud.png" alt="Logo" style="height: 35px;">
                        </center>
                    </td>
                    <td style="width: 80%; text-align: center; vertical-align: middle; font-size: 12px;">
                        <center>
                            <strong>KARTU PESERTA UJIAN CBT</strong><br>
                            TAHUN PELAJARAN <?php echo $thn_pelajaran; ?>
                        </center>
                    </td>
                </tr>
            </table>
            <hr style="border:0; border-top:3px double #000; margin:5px 0;">
            <table style="width: 100%; font-size: 12px; padding:10px;">
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
                    <td>Username</td>
                    <td>:</td>
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
        <?php
        $counter++;
        if ($counter % 9 == 0 && $counter < $total_rows) {
            // Tutup page lama, buka page baru
            echo '</div><div class="page">';
        }
    }

    echo '</div>'; // tutup .page terakhir
    echo '</div>'; // tutup #canvas_div_pdf
else:
    echo "<p style='text-align:center;'>Tidak ada data siswa untuk ditampilkan.</p>";
endif;
?>
<script>
var kelasParam = "<?php echo isset($_GET['kelas']) ? $_GET['kelas'] : ''; ?>";
var namaParam  = "<?php echo isset($_GET['nama']) ? $_GET['nama'] : ''; ?>";

var fileLabel;

if (kelasParam && kelasParam !== 'all') {
    fileLabel = kelasParam;
} else if (namaParam) {
    fileLabel = namaParam;
} else {
    fileLabel = 'semua';
}

// bersihkan karakter aneh supaya aman untuk nama file
fileLabel = fileLabel.replace(/[^a-zA-Z0-9]/g, '');
</script>
<script src="../assets/html2pdf.js/dist/html2pdf.bundle.min.js"></script>
<script>
window.addEventListener("load", function () {
    var element = document.getElementById('canvas_div_pdf');

    // Cek elemen ada
    if (!element) {
        alert('Tidak ada data kartu untuk dicetak.');
        return;
    }

    // Cek script html2pdf sudah termuat
    if (typeof html2pdf === 'undefined') {
        alert('Script html2pdf tidak ditemukan.\nPeriksa path: ../assets/html2pdf.js/dist/html2pdf.bundle.min.js');
        return;
    }

    // Delay sedikit supaya layout benar-benar ter-render
    setTimeout(function () {
    html2pdf().set({
        margin: 0.2,
        filename: 'kartuujian_' + fileLabel + '.pdf',
        image: { type: 'jpeg', quality: 0.8 },
        html2canvas: { scale: 2, logging: false },
        jsPDF: { unit: 'in', format: 'a4', orientation: 'landscape' }
    }).from(element).save().then(function () {
        // kasih jeda lagi sedikit setelah proses save selesai
        setTimeout(function () {
            try {
                window.close();
            } catch (e) {
                // kalau diblokir browser, ya tabnya tetap terbuka
                console.log('Gagal menutup jendela:', e);
            }
        }, 800);
    });
}, 500);

});
</script>
</body>
</html>
