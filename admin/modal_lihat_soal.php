<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');

if (!isset($_GET['kode_soal']) || !isset($_GET['nomor'])) {
    exit('Parameter kurang');
}

$kode_soal = $_GET['kode_soal'];
$nomor     = (int)$_GET['nomor'];

/* ambil jumlah opsi dari tabel soal */
$qInfo = mysqli_query($koneksi,"SELECT jumlah_opsi FROM soal WHERE kode_soal='$kode_soal'");
$jInfo = mysqli_fetch_assoc($qInfo);
$jumlah_opsi = intval($jInfo['jumlah_opsi'] ?? 4);

$opsi_huruf_full = ['A','B','C','D','E'];

$soalQ = mysqli_query($koneksi,"
    SELECT * FROM butir_soal 
    WHERE kode_soal='$kode_soal' 
    AND nomer_soal=$nomor
");
$soal = mysqli_fetch_assoc($soalQ);

if(!$soal){
    exit("Soal tidak ditemukan");
}

$jawaban_benar = $soal['jawaban_benar'];
?>

<style>
.preview-soal img{
    max-width:450px;
    max-height:300px;
}
.preview-soal table{
    width:100%;
    margin-bottom:6px;
}
.preview-soal td{
    vertical-align:top;
}
.preview-header{
    background:grey;
    color:white;
    padding:6px;
}
.preview-no{
    background:black;
    color:white;
    padding:4px 6px;
}
.form-check-input:checked{
    background-color:#198754 !important;
    border-color:#198754 !important;
}
.table-garis{
    border-collapse: collapse;
    width: 100%;
    margin-top:10px;
}
.table-garis,
.table-garis th,
.table-garis td{
    border:1px solid #333 !important;
}
.table-garis th{
    background:#f2f2f2;
    text-align:center;
}
</style>

<div class="preview-soal p-3">

<div class="preview-header">
    <b class="preview-no">No. <?= $soal['nomer_soal'] ?></b>
    <i>(<?= $soal['tipe_soal'] ?>)</i>
</div>

<p class="mt-3"><?= $soal['pertanyaan'] ?></p>

<?php if(!empty($soal['gambar'])): ?>
<img src="../assets/img/butir_soal/<?= $soal['gambar'] ?>">
<?php endif; ?>

<hr>

<?php
/* ================= PILIHAN GANDA ================= */
if ($soal['tipe_soal'] === 'Pilihan Ganda') {

for ($i=1;$i<=$jumlah_opsi;$i++){
    $opsi = $soal['pilihan_'.$i];
    if(trim($opsi)=='') continue;

    $huruf = $opsi_huruf_full[$i-1];
    $checked = ($jawaban_benar == 'pilihan_'.$i) ? 'checked' : '';

    echo "
    <table>
        <tr>
            <td width='30'><b>$huruf.</b></td>
            <td width='30'>
                <input class='form-check-input' type='radio' $checked disabled>
            </td>
            <td>$opsi</td>
        </tr>
    </table>";
}
}

/* ================= PG KOMPLEKS ================= */
elseif ($soal['tipe_soal'] === 'Pilihan Ganda Kompleks') {

$jawaban = explode(',', $jawaban_benar);

for ($i=1;$i<=$jumlah_opsi;$i++){
    $opsi = $soal['pilihan_'.$i];
    if(trim($opsi)=='') continue;

    $huruf = $opsi_huruf_full[$i-1];
    $checked = in_array('pilihan_'.$i, $jawaban) ? 'checked' : '';

    echo "
    <table>
        <tr>
            <td width='30'><b>$huruf.</b></td>
            <td width='30'>
                <input class='form-check-input' type='checkbox' $checked disabled>
            </td>
            <td>$opsi</td>
        </tr>
    </table>";
}
}

/* ================= BENAR SALAH ================= */
elseif ($soal['tipe_soal'] === 'Benar/Salah') {

$jawab = explode('|',$jawaban_benar);

echo "<table border='1' class='table-garis'>
<tr>
<th>Pernyataan</th>
<th>Benar</th>
<th>Salah</th>
</tr>";

for($i=1;$i<=5;$i++){
    $opsi = $soal['pilihan_'.$i];
    if(trim($opsi)=='') continue;

    $benar = ($jawab[$i-1]=='Benar') ? 'checked':'';
    $salah = ($jawab[$i-1]=='Salah') ? 'checked':'';

    echo "<tr>
    <td>$opsi</td>
    <td align='center'><input class='form-check-input' type='radio' $benar disabled></td>
    <td align='center'><input class='form-check-input' type='radio' $salah disabled></td>
    </tr>";
}
echo "</table>";
}

/* ================= MENJODOHKAN ================= */
elseif ($soal['tipe_soal'] === 'Menjodohkan') {

$pasangan = explode('|',$jawaban_benar);

echo "<table border='1' class='table-garis'>
<tr>
<th>Pilihan</th>
<th>Pasangan</th>
</tr>";

foreach($pasangan as $p){
    list($kiri,$kanan) = explode(':',$p);
    echo "<tr>
    <td>$kiri</td>
    <td>$kanan</td>
    </tr>";
}
echo "</table>";
}

/* ================= URAIAN ================= */
elseif ($soal['tipe_soal'] === 'Uraian') {
    echo "<div class='mt-3'><b>Jawaban Benar:</b><br>$jawaban_benar</div>";
}
?>

</div>
