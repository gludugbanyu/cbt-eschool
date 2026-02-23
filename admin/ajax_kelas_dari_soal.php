<?php
include '../koneksi/koneksi.php';

$kode=$_POST['kode'];

$q=mysqli_query($koneksi,"
SELECT DISTINCT CONCAT(s.kelas,s.rombel) as r
FROM nilai n
JOIN siswa s ON n.id_siswa=s.id_siswa
WHERE n.kode_soal='$kode'
");

echo "<option value=''>-- Pilih Kelas --</option>";
while($d=mysqli_fetch_assoc($q)){
echo "<option value='{$d['r']}'>{$d['r']}</option>";
}