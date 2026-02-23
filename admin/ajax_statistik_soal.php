<?php
session_start();
include '../koneksi/koneksi.php';

$kode = $_POST['kode'] ?? '';

$q = mysqli_query($koneksi,"
SELECT nilai 
FROM nilai 
WHERE kode_soal='$kode'
ORDER BY nilai DESC
");

$data=[];

while($d=mysqli_fetch_assoc($q)){
$data[]=(float)$d['nilai'];
}

$n=count($data);

if($n==0){
echo json_encode([
'jml'=>0,
'rata'=>0,
'p'=>0,
'ket_p'=>'-',
'd'=>0,
'ket_d'=>'-',
'sd'=>0,
'cv'=>0,
'pl'=>0,
'ef'=>0
]);
exit;
}

/* =====================
RATA-RATA
===================== */
$rata=array_sum($data)/$n;

/* =====================
STANDAR DEVIASI
===================== */
$sum=0;
foreach($data as $v){
$sum+=pow($v-$rata,2);
}
$sd=sqrt($sum/$n);

/* =====================
CV
===================== */
$cv=($rata>0)?($sd/$rata):0;

/* =====================
PROPORSI LULUS (>=75)
===================== */
$lulus=0;
foreach($data as $v){
if($v>=75) $lulus++;
}
$pl=$lulus/$n;

/* =====================
INDEKS KESUKARAN (P)
===================== */
$p=$rata/100;

if($p<0.3) $ketP='Sukar';
elseif($p<0.7) $ketP='Sedang';
else $ketP='Mudah';

/* =====================
DAYA BEDA (27%)
ANTI ERROR KECIL
===================== */
$k=max(1,ceil(0.27*$n));

$atas=array_slice($data,0,$k);
$bawah=array_slice($data,-$k);

$meanAtas=array_sum($atas)/count($atas);
$meanBawah=array_sum($bawah)/count($bawah);

$d=($meanAtas-$meanBawah)/100;

if($d<0.2) $ketD='Jelek';
elseif($d<0.4) $ketD='Cukup';
elseif($d<0.7) $ketD='Baik';
else $ketD='Sangat Baik';

/* =====================
EFEKTIVITAS GLOBAL
===================== */
$ef=$d*(1-abs(0.5-$p));

echo json_encode([
'jml'=>$n,
'rata'=>round($rata,2),
'p'=>round($p,2),
'ket_p'=>$ketP,
'd'=>round($d,2),
'ket_d'=>$ketD,
'sd'=>round($sd,2),
'cv'=>round($cv,2),
'pl'=>round($pl,2),
'ef'=>round($ef,2)
]);