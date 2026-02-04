<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
include '../inc/dataadmin.php';

if (!isset($_GET['kode_soal']) || !isset($_GET['nomor'])) {
    exit('Parameter kurang');
}

$kode_soal = $_GET['kode_soal'];
$nomor     = (int)$_GET['nomor'];

only_pemilik_soal_by_kode($kode_soal);

$query_info = mysqli_query($koneksi, "SELECT * FROM soal WHERE kode_soal='$kode_soal' LIMIT 1");
$info_soal = mysqli_fetch_assoc($query_info);
$jumlah_opsi = intval($info_soal['jumlah_opsi'] ?? 4);
$opsi_huruf_full = ['A','B','C','D','E'];

$soalQ = mysqli_query($koneksi,"
    SELECT * FROM butir_soal 
    WHERE kode_soal='$kode_soal' 
    AND nomer_soal='$nomor'
");
$soal = mysqli_fetch_assoc($soalQ);

$jawaban_benar = $soal['jawaban_benar'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Preview Soal</title>
<?php include '../inc/css.php'; ?>

<style>
body{
    background:#f4f6f9;
    height:auto !important;
    overflow:auto !important;
}
.card-utama{
    width:100%;
    max-width:100%;
}
</style>
</head>

<body>

<div style="padding:20px;">

    <!-- Tombol Close -->
    <div style="text-align:right; margin-bottom:10px;">
        <button onclick="tutupModal()" 
        style="background:#dc3545;color:white;border:none;padding:8px 14px;border-radius:6px;cursor:pointer;">
    ✕ Close
</button>

    </div>

    <div class="card">
        <div class="card-body card-utama">

<?php
echo "<div class='mb-4 p-3 border rounded bg-white oke'>";
echo "<h5 style='background-color:grey;padding:5px;color:white'>
<b style='padding:5px;background-color:black;color:white;'>No. ".$soal['nomer_soal']."</b>  
<i>(".$soal['tipe_soal'].")</i></h5>";

echo "<p class='text-dark'>".$soal['pertanyaan']."</p>";

if ($soal['tipe_soal'] === 'Pilihan Ganda') {

for ($i = 1; $i <= $jumlah_opsi; $i++) {
    $opsi_label = $opsi_huruf_full[$i - 1];
    $opsi_nama = 'pilihan_' . $i;
    $nilai = $soal[$opsi_nama];
    if (empty(trim($nilai))) continue;

    $checked = (trim($jawaban_benar) === $opsi_nama) ? 'checked' : '';

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

}

elseif ($soal['tipe_soal'] === 'Pilihan Ganda Kompleks') {

$jawaban_benar = array_map('trim', explode(',', $jawaban_benar));

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

}

elseif ($soal['tipe_soal'] === 'Benar/Salah') {

$jawaban_benar = array_map('trim', explode('|', $jawaban_benar));

echo "<table style='width:100%; border-collapse:collapse; margin-top:10px;'>
<thead>
<tr style='background-color:#f0f0f0;'>
<th style='border:1px solid black; padding:8px;'>Pernyataan</th>
<th style='border:1px solid black; padding:8px; text-align:center;'>Benar</th>
<th style='border:1px solid black; padding:8px; text-align:center;'>Salah</th>
</tr>
</thead><tbody>";

for ($i = 1; $i <= 5; $i++) {
    $nilai = trim($soal['pilihan_'.$i]);
    if ($nilai === '') continue;

    $index = $i - 1;
    $is_benar = isset($jawaban_benar[$index]) && $jawaban_benar[$index] === 'Benar';
    $is_salah = isset($jawaban_benar[$index]) && $jawaban_benar[$index] === 'Salah';

    echo "<tr>
    <td style='border:1px solid black; padding:8px;'>$nilai</td>
    <td style='border:1px solid black; text-align:center;'>
        <input class='form-check-input' type='radio' ".($is_benar?'checked':'')." onclick='return false;'>
    </td>
    <td style='border:1px solid black; text-align:center;'>
        <input class='form-check-input' type='radio' ".($is_salah?'checked':'')." onclick='return false;'>
    </td>
    </tr>";
}

echo "</tbody></table>";

}

elseif ($soal['tipe_soal'] === 'Menjodohkan') {

$pasangan = explode('|', $jawaban_benar);

echo "<table style='width:100%; border-collapse: collapse; margin-top:10px;'>
<thead>
<tr>
<th style='border:1px solid #000; padding:5px;'>Pilihan</th>
<th style='border:1px solid #000; padding:5px;'>Pasangan</th>
</tr>
</thead><tbody>";

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

}

elseif ($soal['tipe_soal'] === 'Uraian') {
    echo "<p><b>Jawaban Benar:</b><br>".$jawaban_benar."</p>";
}

echo "</div>";
?>

        </div>
    </div>

</div>
<script>
function resizeIframe(){
    const h = document.body.scrollHeight;
    window.parent.document.getElementById('frameSoal').style.height = h + 'px';
}
window.onload = resizeIframe;
</script>
<script>
function tutupModal(){
    // suruh parent (halaman analisa) yang nutup modal
    window.parent.$('#modalSoal').modal('hide');
}
</script>

</body>
</html>
