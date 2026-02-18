<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
only_admin();
include '../inc/dataadmin.php';

/* =========================
   FILTER ANGKATAN
========================= */
$angkatan_filter = $_GET['angkatan'] ?? '';
$where = [];

if (!empty($angkatan_filter)) {
    $angkatan_filter = mysqli_real_escape_string($koneksi, $angkatan_filter);
    $where[] = "siswa.kelas = '$angkatan_filter'";
}

$where_sql = '';
if (!empty($where)) {
    $where_sql = 'WHERE ' . implode(' AND ', $where);
}

/* =========================
   QUERY RANKING
========================= */
$query_sql = "
    SELECT siswa.id_siswa,
           siswa.nama_siswa,
           CONCAT(siswa.kelas,siswa.rombel) as kelas_full,
           siswa.kelas,
           COUNT(nilai.id_siswa) AS jumlah_ujian,
           ROUND(AVG(nilai.nilai + IFNULL(nilai.nilai_uraian,0)),2) AS rata_rata
    FROM siswa
    LEFT JOIN nilai ON siswa.id_siswa = nilai.id_siswa
    $where_sql
    GROUP BY siswa.id_siswa
    ORDER BY rata_rata DESC
";
$query = mysqli_query($koneksi, $query_sql);

/* =========================
   DATA GRAFIK ANGKATAN
========================= */
$chart_kelas = [];
$chart_avg = [];

$qChart = mysqli_query($koneksi,"
    SELECT kelas,
    ROUND(AVG(nilai.nilai + IFNULL(nilai.nilai_uraian,0)),2) as avg_nilai
    FROM siswa
    JOIN nilai ON siswa.id_siswa = nilai.id_siswa
    GROUP BY kelas
    ORDER BY kelas ASC
");

while($c=mysqli_fetch_assoc($qChart)){
    $chart_kelas[] = $c['kelas'];
    $chart_avg[]   = $c['avg_nilai'];
}

/* =========================
   TOP 10 DONUT
========================= */
$top10_label=[];
$top10_nilai=[];

$qTop = mysqli_query($koneksi,"
    SELECT siswa.nama_siswa,
    ROUND(AVG(nilai.nilai + IFNULL(nilai.nilai_uraian,0)),2) as rata
    FROM siswa
    JOIN nilai ON siswa.id_siswa = nilai.id_siswa
    GROUP BY siswa.id_siswa
    ORDER BY rata DESC
    LIMIT 10
");

while($t=mysqli_fetch_assoc($qTop)){
    $top10_label[]=$t['nama_siswa'];
    $top10_nilai[]=$t['rata'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<title>Ranking Siswa</title>
<?php include '../inc/css.php'; ?>

<link rel="stylesheet" href="../assets/datatables/datatables.min.css">
<link rel="stylesheet" href="../assets/datatables/buttons.dataTables.min.css">

<style>

.badge-modern{
    padding:5px 10px;
    border-radius:20px;
    font-size:11px;
    font-weight:600;
}

.badge-top1{ background:#ffd700; color:#000; }
.badge-top2{ background:#c0c0c0; color:#000; }
.badge-top3{ background:#cd7f32; color:#fff; }

.dark-mode .badge-top1{ color:#000 !important; }
.dark-mode .badge-top2{ color:#000 !important; }
.dark-mode .badge-top3{ color:#000 !important; }

/* Chart Grid */
.chart-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:20px;
}

.chart-card{
    height:360px;
}

.chart-container{
    position:relative;
    height:300px;
}
@media(max-width:992px){
    .chart-grid{
        grid-template-columns:1fr;
    }
}
.rank-badge{
    padding:6px 14px;
    border-radius:30px;
    font-size:12px;
    font-weight:700;
    letter-spacing:.5px;
    display:inline-block;
}

.badge-gold{
    background:linear-gradient(135deg,#FFD700,#FFC107);
    color:#000;
    box-shadow:0 3px 8px rgba(255,193,7,.4);
}

.badge-silver{
    background:linear-gradient(135deg,#C0C0C0,#E0E0E0);
    color:#000;
    box-shadow:0 3px 8px rgba(180,180,180,.4);
}

.badge-bronze{
    background:linear-gradient(135deg,#CD7F32,#E6A15C);
    color:#fff;
    box-shadow:0 3px 8px rgba(205,127,50,.4);
}

.dark-mode .badge-gold{ color:#000 !important; }
.dark-mode .badge-silver{ color:#000 !important; }
.dark-mode .badge-bronze{ color:#000 !important; }
</style>
</head>

<body>
<div class="wrapper">
<?php include 'sidebar.php'; ?>
<div class="main">
<?php include 'navbar.php'; ?>

<main class="content">
<div class="container-fluid">

<h3 class="mb-4">
<i class="fas fa-trophy text-warning"></i> Ranking Rata-rata Nilai Siswa
</h3>

<!-- FILTER -->
<form method="GET" class="mb-4">
<div class="row">
<div class="col-md-3">
<select name="angkatan" class="form-select">
<option value="">Semua Angkatan</option>
<?php
$angkatan_q = mysqli_query($koneksi,"SELECT DISTINCT kelas FROM siswa ORDER BY kelas ASC");
while($a=mysqli_fetch_assoc($angkatan_q)){
$sel = ($angkatan_filter == $a['kelas']) ? 'selected' : '';
echo "<option value='{$a['kelas']}' $sel>{$a['kelas']}</option>";
}
?>
</select>
</div>

<div class="col-md-2">
<button class="btn btn-primary">
<i class="fas fa-filter"></i> Filter
</button>
</div>
</div>
</form>

<!-- TABLE -->
<div class="card mb-4 shadow-sm">
<div class="card-body table-responsive">

<table id="rankingTable" class="table table-bordered table-striped align-middle">
<thead class="table-dark">
<tr>
<th style="width:80px;">Rank</th>
<th style="width:130px;">Peringkat</th>
<th>Nama</th>
<th>Kelas</th>
<th>Ujian</th>
<th>Rata-rata</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>

<?php
$row_number = 0;   // nomor semua siswa
$valid_rank = 0;   // ranking hanya untuk yang sudah ujian

while($row = mysqli_fetch_assoc($query)):

    $row_number++; // selalu naik

    $badge = '-';

    if($row['jumlah_ujian'] > 0 && $row['rata_rata'] > 0){
        $valid_rank++;

        if($valid_rank == 1){
            $badge = '<span class="rank-badge badge-gold">🥇 TOP1</span>';
        }elseif($valid_rank == 2){
            $badge = '<span class="rank-badge badge-silver">🥈 TOP2</span>';
        }elseif($valid_rank == 3){
            $badge = '<span class="rank-badge badge-bronze">🥉 TOP3</span>';
        }
    }
?>
<tr>
<td><strong><?= $row_number ?></strong></td>
<td><?= $badge ?></td>
<td><?= htmlspecialchars($row['nama_siswa']) ?></td>
<td><?= $row['kelas_full'] ?></td>
<td><?= $row['jumlah_ujian'] ?></td>
<td><strong><?= $row['rata_rata'] ?? 0 ?></strong></td>
<td>
<?php if($valid_rank > 0 && $valid_rank <= 3 && $row['rata_rata'] > 0): ?>
<a href="sertifikat.php?id=<?= $row['id_siswa'] ?>&rank=<?= $valid_rank ?>&kelas=<?= $row['kelas'] ?>"
target="_blank"
class="btn btn-sm btn-danger">
<i class="fas fa-file-pdf"></i> Sertifikat
</a>
<?php else: ?>
<span class="text-muted small">-</span>
<?php endif; ?>
</td>
</tr>
<?php endwhile; ?>

</tbody>
</table>

</div>
</div>

<!-- MODERN CHART GRID -->
<div class="chart-grid">

<div class="card chart-card shadow-sm">
<div class="card-header fw-semibold">
📊 Rata-rata Nilai per Angkatan
</div>
<div class="card-body">
<div class="chart-container">
<canvas id="chartAngkatan"></canvas>
</div>
</div>
</div>

<div class="card chart-card shadow-sm">
<div class="card-header fw-semibold">
🔥 Top 10 Siswa
</div>
<div class="card-body">
<div class="chart-container">
<canvas id="chartTop10"></canvas>
</div>
</div>
</div>

</div>

</div>
</main>
</div>
</div>

<?php include '../inc/js.php'; ?>
<script src="../assets/datatables/datatables.min.js"></script>
<script src="../assets/datatables/jszip.min.js"></script>
<script src="../assets/datatables/buttons.html5.min.js"></script>
<script src="../assets/js/chart.js"></script>
<script>
const angkatanFilter = "<?= $angkatan_filter ?>";
</script>
<script>
$(document).ready(function(){

$('#rankingTable').DataTable({
    dom:'Bfrtip',
    buttons:[{
        extend:'excelHtml5',
        title: function(){
            if(angkatanFilter){
                return 'Ranking Siswa Kelas ' + angkatanFilter;
            }else{
                return 'Ranking Siswa Semua Angkatan';
            }
        },
        exportOptions:{
            columns: ':not(:last-child)',
            format:{
                body:function(data){
                    return data.replace(/<.*?>/g,'');
                }
            }
        }
    }],
    pageLength:10
});

/* Smooth row animation */
$('#rankingTable tbody tr').each(function(i){
$(this).css({opacity:0,transform:'translateY(10px)'});
setTimeout(()=>{
$(this).animate({opacity:1},{step:function(now){
$(this).css('transform','translateY('+(10-(10*now))+'px)');
},duration:400});
},i*60);
});

/* BAR CHART */
new Chart(document.getElementById('chartAngkatan'),{
type:'bar',
data:{
labels:<?= json_encode($chart_kelas) ?>,
datasets:[{
data:<?= json_encode($chart_avg) ?>,
backgroundColor:['#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b','#858796']
}]
},
options:{
responsive:true,
maintainAspectRatio:false,
plugins:{legend:{display:false}}
}
});

/* DONUT */
new Chart(document.getElementById('chartTop10'),{
type:'doughnut',
data:{
labels:<?= json_encode($top10_label) ?>,
datasets:[{
data:<?= json_encode($top10_nilai) ?>,
backgroundColor:['#FFD700','#C0C0C0','#CD7F32','#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b','#858796','#20c9a6']
}]
},
options:{
responsive:true,
maintainAspectRatio:false,
plugins:{legend:{position:'bottom'}}
}
});

});
</script>

</body>
</html>