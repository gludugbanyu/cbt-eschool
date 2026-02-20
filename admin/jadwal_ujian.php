<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
include '../inc/dataadmin.php';

$id_admin = $_SESSION['admin_id'];
$role     = $_SESSION['role'];

$where = "WHERE 1=1";
if ($role != 'admin') {
    $where .= " AND FIND_IN_SET('$id_admin', s.id_pembuat)";
}

$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

$q = mysqli_query($koneksi,"
SELECT tanggal,nama_soal,mapel,kelas,waktu_ujian
FROM soal s
$where
AND MONTH(tanggal)='$bulan'
AND YEAR(tanggal)='$tahun'
ORDER BY tanggal ASC
");

$data=[];
while($r=mysqli_fetch_assoc($q)){
    $data[$r['tanggal']][]=$r;
}

$firstDay = mktime(0,0,0,$bulan,1,$tahun);
$startDay = date('N',$firstDay);
$totalDays = date('t',$firstDay);

$namaBulan=[
1=>'Januari','Februari','Maret','April','Mei','Juni',
'Juli','Agustus','September','Oktober','November','Desember'
];
function getEventColor($text){

    $hash = md5($text);

    $h = hexdec(substr($hash,0,2)) % 360;

    $bg = "hsl($h, 80%, 90%)";
    $border = "hsl($h, 70%, 50%)";

    return [$bg,$border];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Jadwal Ujian</title>
<?php include '../inc/css.php'; ?>

<style>
.calendar{width:100%;border-collapse:separate;border-spacing:0;border-radius:8px;overflow:hidden;}
.calendar th{background:#f8fafc;font-weight:600;padding:10px;border:1px solid #e2e8f0;font-size:13px;}
.calendar td{
border:1px solid #e2e8f0;
padding:6px;
height:150px;
vertical-align:top;
background:#ffffff;
transition:.2s;
width:14.28%;
position:relative;
}
.calendar td:hover{background:#f1f5f9;}
.date{font-weight:600;font-size:12px;color:#64748b;margin-bottom:6px;}
.cell{
height:150px;
display:flex;
flex-direction:column;
overflow:hidden;
}

.cell .date{
flex:0 0 auto;
}

.cell .events{
flex:1 1 auto;
overflow-y:auto;
}

.calendar{
table-layout:fixed;
min-width:1000px;
}

.calendar-wrapper{
width:100%;
overflow-x:auto;
-webkit-overflow-scrolling:touch;
}

.event{
background:linear-gradient(135deg,#eef2ff,#e0e7ff);
padding:7px 9px;
margin-bottom:6px;
font-size:12px;
border-left:4px solid #6366f1;
border-radius:6px;
color:#1e293b;
box-shadow:0 1px 3px rgba(0,0,0,0.06);
line-height:1.4;
transition:.15s;
cursor:pointer;
}
.event:hover{
transform:translateY(-2px);
box-shadow:0 4px 8px rgba(0,0,0,0.08);
}

/* DARK MODE */
.dark-mode .calendar th{background:#1e293b;border:1px solid #334155;color:#e2e8f0;}
.dark-mode .calendar td{background:#0f172a;border:1px solid #334155;color:#cbd5e1;}
.dark-mode .calendar td:hover{background:#1e293b;}
.dark-mode .date{color:#94a3b8;}
/* DARK MODE EVENT FIX */
.dark-mode .event{
    color:#0f172a !important;
}

.dark-mode .event *{
    color:#0f172a !important;
}

/* PRINT */
@media print{

@page{
    size:landscape;
    margin:10mm;
}

.sidebar,.navbar,.footer,.btn-print,.form-filter{
display:none !important;
}

.content{
margin:0 !important;
padding:0 !important;
}

.card{
border:none !important;
box-shadow:none !important;
}

/* HAPUS SCROLL WRAPPER */
.calendar-wrapper{
overflow:visible !important;
}

/* HAPUS MIN WIDTH DESKTOP */
.calendar{
min-width:100% !important;
width:100% !important;
table-layout:fixed !important;
}

/* KECILIN CELL */
.calendar th,
.calendar td{
height:90px !important;
font-size:11px !important;
padding:3px !important;
background:#fff !important;
color:#000 !important;
border:1px solid #000 !important;
}

/* EVENT */
.event{
font-size:10px !important;
padding:2px !important;
background:#fff !important;
color:#000 !important;
border-left:3px solid #000 !important;
box-shadow:none !important;
}

}
.dark-mode .calendar td{
border:1px solid #334155;
}
.calendar th,
.calendar td{
white-space:normal;
}
.calendar-wrapper::-webkit-scrollbar{
height:6px;
}
.calendar-wrapper::-webkit-scrollbar-thumb{
background:#cbd5e1;
border-radius:10px;
}
.dark-mode .calendar-wrapper::-webkit-scrollbar-thumb{
background:#475569;
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

<div class="card">
<div class="card-header d-flex justify-content-between align-items-center">
<h5 class="card-title mb-0">
<i class="fa fa-calendar-alt"></i>
Jadwal Ujian <?= $namaBulan[(int)$bulan]." ".$tahun ?>
</h5>
<button onclick="window.print()" class="btn btn-primary btn-print">
<i class="fa fa-print"></i> Print
</button>
</div>

<div class="card-body">

<form method="GET" class="form-filter mb-3 d-flex gap-2">
<select name="bulan" class="form-control" style="width:150px">
<?php for($i=1;$i<=12;$i++){ ?>
<option value="<?=str_pad($i,2,'0',STR_PAD_LEFT)?>" <?=($bulan==$i?'selected':'')?>>
<?=$namaBulan[$i]?>
</option>
<?php } ?>
</select>

<select name="tahun" class="form-control" style="width:120px">
<?php for($t=date('Y')-1;$t<=date('Y')+1;$t++){ ?>
<option value="<?=$t?>" <?=($tahun==$t?'selected':'')?>>
<?=$t?>
</option>
<?php } ?>
</select>

<button class="btn btn-success">Tampilkan</button>
</form>
<div class="calendar-wrapper">
<table class="calendar">
<tr>
<th>Sen</th><th>Sel</th><th>Rab</th>
<th>Kam</th><th>Jum</th><th>Sab</th><th>Min</th>
</tr>
<tr>

<?php
for($i=1;$i<$startDay;$i++){
echo "<td></td>";
}

for($day=1;$day<=$totalDays;$day++){

$tgl="$tahun-$bulan-".str_pad($day,2,'0',STR_PAD_LEFT);

echo "<td><div class='cell'>";
echo "<div class='date'>$day</div>";
echo "<div class='events'>";
if(isset($data[$tgl])){
foreach($data[$tgl] as $e){

list($bg,$border)=getEventColor($e['mapel']);
echo "
<div class='event btn-detail'
style='background:$bg;border-left:4px solid $border'
data-mapel='$e[mapel]'
data-nama='$e[nama_soal]'
data-kelas='$e[kelas]'
data-durasi='$e[waktu_ujian]'
data-tanggal='$tgl'>

<b>$e[mapel]</b><br>
$e[nama_soal]<br>
$e[kelas]<br>
⏱ $e[waktu_ujian] mnt
</div>";
}
}
echo "</div>";
echo "</div></td>";

if(($day+$startDay-1)%7==0){
echo "</tr><tr>";
}
}
?>

</tr>
</table>
</div>
</div>
</div>

</div>
</div>
</div>
</main>
</div>
</div>

<!-- MODAL -->
<div class="modal fade" id="detailUjianModal" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title"><i class="fa fa-info-circle"></i> Detail Jadwal Ujian</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<table class="table table-sm">
<tr><td>Mapel</td><td id="m_mapel"></td></tr>
<tr><td>Nama Ujian</td><td id="m_nama"></td></tr>
<tr><td>Kelas</td><td id="m_kelas"></td></tr>
<tr><td>Durasi</td><td id="m_durasi"></td></tr>
<tr><td>Tanggal</td><td id="m_tanggal"></td></tr>
</table>
</div>
</div>
</div>
</div>

<?php include '../inc/js.php'; ?>

<script>
document.querySelectorAll('.btn-detail').forEach(el=>{
el.addEventListener('click',function(){
document.getElementById('m_mapel').innerText=this.dataset.mapel;
document.getElementById('m_nama').innerText=this.dataset.nama;
document.getElementById('m_kelas').innerText=this.dataset.kelas;
document.getElementById('m_durasi').innerText=this.dataset.durasi+' menit';
document.getElementById('m_tanggal').innerText=this.dataset.tanggal;
new bootstrap.Modal(document.getElementById('detailUjianModal')).show();
});
});
</script>

</body>
</html>