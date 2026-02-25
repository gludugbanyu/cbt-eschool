<?php
include '../koneksi/koneksi.php';
include '../inc/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    if (!is_ajax_request()) {
        header("Location: ../admin/dashboard.php?notallowed=1");
        exit;
    }

    http_response_code(405);
    echo 'SESSION_EXPIRED';
    exit;
}
$kode=$_POST['kode'];
$rombel=$_POST['rombel'];

$q=mysqli_query($koneksi,"
SELECT kunci FROM soal
WHERE kode_soal='$kode'
");

$k=mysqli_fetch_assoc($q);
preg_match_all('/\[(\d+):(.*?)\]/',$k['kunci'],$x);

$kunci=[];
foreach($x[1] as $i=>$no){
$kunci[$no]=$x[2][$i];
}

$global=[];
$kelas=[];

$q=mysqli_query($koneksi,"
SELECT id_siswa,jawaban_siswa
FROM jawaban_siswa
WHERE kode_soal='$kode'
AND status_ujian='Selesai'
");

while($r=mysqli_fetch_assoc($q)){

preg_match_all('/\[(\d+):(.*?)\]/',$r['jawaban_siswa'],$j);

foreach($j[1] as $i=>$no){

if(!isset($global[$no])) $global[$no]=['b'=>0,'n'=>0];

if(trim($j[2][$i])==trim($kunci[$no])){
$global[$no]['b']++;
}
$global[$no]['n']++;

}

}

$q=mysqli_query($koneksi,"
SELECT js.id_siswa,js.jawaban_siswa
FROM jawaban_siswa js
JOIN siswa s ON js.id_siswa=s.id_siswa
WHERE js.kode_soal='$kode'
AND CONCAT(s.kelas,s.rombel)='$rombel'
AND js.status_ujian='Selesai'
");

$n=mysqli_num_rows($q);

while($r=mysqli_fetch_assoc($q)){

preg_match_all('/\[(\d+):(.*?)\]/',$r['jawaban_siswa'],$j);

foreach($j[1] as $i=>$no){

if(!isset($kelas[$no])) $kelas[$no]=['b'=>0,'n'=>0];

if(trim($j[2][$i])==trim($kunci[$no])){
$kelas[$no]['b']++;
}
$kelas[$no]['n']++;

}

}

$data=[];

foreach($kunci as $no=>$v){

$Pg=($global[$no]['n']>0)?
$global[$no]['b']/$global[$no]['n']:0;

$Pk=($kelas[$no]['n']>0)?
$kelas[$no]['b']/$kelas[$no]['n']:0;

$d=round($Pk-$Pg,2);

$st="Sesuai Global";

if($d < -0.2){
    $st="Perlu Perhatian";
}
elseif($d > 0.2){
    $st="Lebih Dikuasai";
}

$data[]=[
'no'=>$no,
'Pg'=>round($Pg,2),
'Pk'=>round($Pk,2),
'd'=>$d,
'st'=>$st
];
}

$info="";
if($n<10){
$info="
<div class='info-sampel'>
Sampel kelas <b>$rombel</b> hanya <b>$n siswa</b> 
→ interpretasi statistik kurang stabil
</div>";
}
elseif($n<20){
$info="
<div class='info-sampel'>
Sampel kelas <b>$rombel</b> masih terbatas (<b>$n siswa</b>) 
→ daya beda perlu ditafsir hati-hati
</div>";
}

echo json_encode([
'info'=>$info,
'data'=>$data
]);