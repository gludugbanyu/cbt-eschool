<?php
header('Content-Type: application/json');
include '../koneksi/koneksi.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$start = isset($_GET['start']) ? $_GET['start'] : 0;
$length = isset($_GET['length']) ? $_GET['length'] : 10;
$orderColumn = isset($_GET['order'][0]['column']) ? $_GET['order'][0]['column'] : 0;
$orderDirection = isset($_GET['order'][0]['dir']) ? $_GET['order'][0]['dir'] : 'asc';
$searchValue = isset($_GET['search']['value']) ? $_GET['search']['value'] : '';

$columns = [
    's.nama_siswa',
    'CONCAT(s.kelas, s.rombel)',   // ← TAMBAHAN
    'js.kode_soal',
    'js.waktu_sisa',
    'js.waktu_dijawab',
    'js.status_ujian'
];
$orderBy = $columns[$orderColumn];

$where = "";
if (!empty($searchValue)) {
$where = "WHERE s.nama_siswa LIKE '%$searchValue%' 
          OR js.kode_soal LIKE '%$searchValue%'
          OR CONCAT(s.kelas, s.rombel) LIKE '%$searchValue%'";} else {
    $where = "WHERE js.status_ujian = 'Aktif'";
}

$query = "SELECT js.*, 
                 s.nama_siswa,
                 CONCAT(s.kelas, s.rombel) AS kelas
          FROM jawaban_siswa js
          JOIN siswa s ON js.id_siswa = s.id_siswa
          $where
          ORDER BY $orderBy $orderDirection
          LIMIT $start, $length";

$result = mysqli_query($koneksi, $query);

if (!$result) {
    echo json_encode(['error' => 'Query failed: ' . mysqli_error($koneksi)]);
    exit;
}

$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $badge = ($row['status_ujian'] === 'Aktif')
        ? '<span class="badge bg-success">Aktif</span>'
        : '<span class="badge bg-danger">Non-Aktif</span>';

    $kodeSoal = $row['kode_soal'];
    $queryTotalSoal = mysqli_query($koneksi, "SELECT COUNT(*) AS total_soal FROM butir_soal WHERE kode_soal = '$kodeSoal'");
    $totalSoal = 0;
    if ($queryTotalSoal) {
        $totalSoal = mysqli_fetch_assoc($queryTotalSoal)['total_soal'];
    }

    $jawaban = $row['jawaban_siswa'];
    preg_match_all('/\[(\d+):(.*?)\]/', $jawaban, $matches);
    $jumlahDijawab = 0;
    foreach ($matches[2] as $isiJawaban) {
        if (trim($isiJawaban) !== '') {
            $jumlahDijawab++;
        }
    }

    $persentase = ($totalSoal > 0) ? round(($jumlahDijawab / $totalSoal) * 100) : 0;

    $barColor = 'bg-danger';
    if ($persentase >= 80) {
        $barColor = 'bg-success';
    } elseif ($persentase >= 50) {
        $barColor = 'bg-warning text-dark';
    }

    $progres = '
    <div style="font-size: 0.9rem; margin-bottom: 4px; font-weight: 600;">' . $jumlahDijawab . '/' . $totalSoal . '</div>
    <div class="progress" style="height: 6px; background-color: #e9ecef; border-radius: 3px; overflow: hidden;">
      <div class="progress-bar ' . $barColor . '" 
           role="progressbar" 
           style="width: ' . $persentase . '%; transition: width 0.6s ease;"
           aria-valuenow="' . $persentase . '" 
           aria-valuemin="0" 
           aria-valuemax="100">
      </div>
    </div>';

    $aksi = '<button class="btn btn-sm btn-danger simpan-paksa-btn" 
                data-kode="' . $row['kode_soal'] . '" 
                data-nama="' . $row['nama_siswa'] . '" 
                data-siswa="' . $row['id_siswa'] . '">
                Selesai Paksa <i class="fas fa-save"></i>
         </button>';

    $data[] = [
        'nama_siswa' => $row['nama_siswa'],
        'kelas' => $row['kelas'],
        'kode_soal' => $kodeSoal,
        'waktu_sisa' => $row['waktu_sisa'],
        'waktu_dijawab' => $row['waktu_dijawab'],
        'status_badge' => $badge,
        'progres' => $progres,
        'aksi' => $aksi
    ];
}

$queryCount = "SELECT COUNT(*) AS total FROM jawaban_siswa js JOIN siswa s ON js.id_siswa = s.id_siswa $where";
$countResult = mysqli_query($koneksi, $queryCount);
$total = mysqli_fetch_assoc($countResult)['total'];

echo json_encode([
    'draw' => isset($_GET['draw']) ? $_GET['draw'] : 1,
    'recordsTotal' => $total,
    'recordsFiltered' => $total,
    'data' => $data
]);
