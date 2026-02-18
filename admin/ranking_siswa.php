<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
only_admin();
include '../inc/dataadmin.php';

/* =========================
   FILTER ANGKATAN (AMAN ANGKA & ROMAWI)
========================= */
$angkatan_filter = $_GET['angkatan'] ?? '';

$where_sql = '';
if (!empty($angkatan_filter)) {
    $angkatan_filter = mysqli_real_escape_string($koneksi, $angkatan_filter);
    $where_sql = "WHERE siswa.kelas = '$angkatan_filter'";
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
    HAVING rata_rata IS NOT NULL
    ORDER BY rata_rata DESC
";

$query = mysqli_query($koneksi, $query_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ranking Siswa</title>

<?php include '../inc/css.php'; ?>

<link rel="stylesheet" href="../assets/datatables/datatables.min.css">
<link rel="stylesheet" href="../assets/datatables/buttons.dataTables.min.css">
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

<!-- ================= FILTER ================= -->
<form method="GET" class="mb-4">
<div class="row">

<div class="col-md-3">
<select name="angkatan" class="form-select">
<option value="">Semua Angkatan</option>
<?php
$angkatan_q = mysqli_query($koneksi,"
    SELECT DISTINCT kelas
    FROM siswa
    ORDER BY kelas ASC
");
while($a=mysqli_fetch_assoc($angkatan_q)){
    $sel = ($angkatan_filter == $a['kelas']) ? 'selected' : '';
    echo "<option value='{$a['kelas']}' $sel>Kelas {$a['kelas']}</option>";
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

<!-- ================= TABLE ================= -->
<div class="card">
<div class="card-body table-responsive">

<table id="rankingTable" class="table table-bordered table-striped">
<thead class="table-dark">
<tr>
<th>Ranking</th>
<th>Nama</th>
<th>Kelas</th>
<th>Jumlah Ujian</th>
<th>Rata-rata</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>

<?php
$no = 1;
while($row = mysqli_fetch_assoc($query)):

$row_class = '';
$medal = '';

if($no == 1){
    $row_class = 'rank-1';
    $medal = '<i class="fas fa-medal text-warning"></i>';
}elseif($no == 2){
    $row_class = 'rank-2';
    $medal = '<i class="fas fa-medal text-secondary"></i>';
}elseif($no == 3){
    $row_class = 'rank-3';
    $medal = '<i class="fas fa-medal text-danger"></i>';
}
?>

<tr class="<?= $row_class ?>">
<td><?= $no ?></td>
<td><?= $medal ?> <?= htmlspecialchars($row['nama_siswa']) ?></td>
<td><?= $row['kelas_full'] ?></td>
<td><?= $row['jumlah_ujian'] ?></td>
<td><?= $row['rata_rata'] ?? 0 ?></td>
<td>
<?php if($no <= 3 && $row['rata_rata'] > 0 && $row['jumlah_ujian'] > 0): ?>
<a href="sertifikat.php?id=<?= $row['id_siswa'] ?>&rank=<?= $no ?>"
   target="_blank"
   class="btn btn-sm btn-primary">
   <i class="fas fa-file-pdf"></i> Sertifikat
</a>
<?php else: ?>
<span class="text-muted small">-</span>
<?php endif; ?>
</td>
</tr>

<?php $no++; endwhile; ?>

</tbody>
</table>

</div>
</div>

</div>
</main>
</div>
</div>

<?php include '../inc/js.php'; ?>

<script src="../assets/datatables/datatables.min.js"></script>
<script src="../assets/datatables/jszip.min.js"></script>
<script src="../assets/datatables/dataTables.buttons.min.js"></script>
<script src="../assets/datatables/buttons.html5.min.js"></script>

<script>
$(document).ready(function () {

    $('#rankingTable').DataTable({
        dom:
        '<"row mb-3"' +
            '<"col-md-6 d-flex align-items-center"B>' +
            '<"col-md-6 d-flex justify-content-end"f>' +
        '>' +
        '<"row mb-3"' +
            '<"col-md-6 d-flex align-items-center"l>' +
            '<"col-md-6 d-flex justify-content-end"p>' +
        '>' +
        't' +
        '<"row mt-3"' +
            '<"col-md-6 d-flex align-items-center"i>' +
            '<"col-md-6 d-flex justify-content-end"p>' +
        '>',
        paging: true,
        lengthChange: true,
        searching: true,
        ordering: true,
        info: true,
        responsive: true,
        order: [[0, 'asc']],
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Ranking Siswa',
                exportOptions: {
                    columns: [0,1,2,3,4]
                }
            }
        ]
    });

});
</script>

</body>
</html>