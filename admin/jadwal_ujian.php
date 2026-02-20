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

$today = date('Y-m-d');

if (!function_exists('getEventColor')) {
    function getEventColor($text){
        $hash = md5($text);
        $h = hexdec(substr($hash,0,2)) % 360;
        $bg = "hsl($h, 80%, 90%)";
        $border = "hsl($h, 70%, 50%)";
        return [$bg,$border];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Ujian</title>
    <?php include '../inc/css.php'; ?>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz@14..32&display=swap" rel="stylesheet">
    <style>
    /* === VARIABEL === */
    :root {
        --font-sans: 'Inter', system-ui, -apple-system, sans-serif;
        --border-light: #e2e8f0;
        --border-dark: #334155;
        --bg-cell-light: #ffffff;
        --bg-cell-dark: #0f172a;
        --text-light: #1e293b;
        --text-dark: #f1f5f9;
        --empty-cell-light: #f1f5f9;
        --empty-cell-dark: #1e293b;
        /* Highlight hari ini lebih lembut */
        --today-bg-light: #bcbcbc;
        --today-border-light: #cacaca;
        --today-bg-dark: #454545;
        --today-border-dark: #7e7e7e;
    }

    /* === CALENDAR BASE === */
    .calendar-wrapper {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border-radius: 12px;
    }

    .calendar {
        width: 100%;
        border-collapse: collapse;
        min-width: 1000px;
        font-family: var(--font-sans);
    }

    .calendar th {
        background: #f8fafc;
        font-weight: 600;
        padding: 12px 8px;
        border: 1px solid var(--border-light);
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #475569;
    }

    .calendar td {
        border: 1px solid var(--border-light);
        padding: 6px;
        height: 150px;
        vertical-align: top;
        background: var(--bg-cell-light);
        transition: background 0.2s;
        width: 14.28%;
    }

    .calendar td:hover {
        background: #f1f5f9;
    }

    /* Sel kosong */
    .calendar td.empty-cell {
        background-color: var(--empty-cell-light);
    }
    .calendar td.empty-cell:hover {
        background-color: #e2e8f0;
    }

    /* Highlight hari ini (soft) */
    .calendar td.today {
        background-color: var(--today-bg-light);
        border: 2px solid var(--today-border-light);
    }
    .calendar td.today:hover {
        background-color: #a4a4a4;
    }

    .date {
        font-weight: 700;
        font-size: 0.9rem;
        color: #1e293b;
        margin-bottom: 6px;
        display: inline-block;
        background: rgba(99, 102, 241, 0.1);
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: 0.2s;
    }

    td:hover .date {
        background: #6366f1;
        color: white;
    }

    .cell {
        height: 150px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .events {
        flex: 1 1 auto;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    /* === EVENT CARD WARNA-WARNI === */
    .event {
        padding: 6px 8px;
        font-size: 11px;
        border-radius: 8px;
        color: #0f172a;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        line-height: 1.4;
        transition: all 0.2s ease;
        cursor: pointer;
        border-left: 4px solid transparent;
        font-family: var(--font-sans);
        background: linear-gradient(145deg, rgba(255,255,255,0.9), rgba(255,255,255,0.5));
        background-blend-mode: overlay;
    }

    .event:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 12px rgba(0,0,0,0.1);
    }

    .event b {
        font-size: 12px;
        display: block;
        margin-bottom: 2px;
    }

    /* === DARK MODE === */
    .dark-mode .calendar th {
        background: #1e293b;
        border-color: var(--border-dark);
        color: #cbd5e1;
    }

    .dark-mode .calendar td {
        background: var(--bg-cell-dark);
        border-color: var(--border-dark);
    }

    .dark-mode .calendar td:hover {
        background: #1e293b;
    }

    .dark-mode .calendar td.empty-cell {
        background-color: var(--empty-cell-dark);
    }
    .dark-mode .calendar td.empty-cell:hover {
        background-color: #2d3a4f;
    }

    .dark-mode .calendar td.today {
        background-color: var(--today-bg-dark);
        border-color: var(--today-border-dark);
    }
    .dark-mode .calendar td.today:hover {
        background-color: #595959;
    }

    .dark-mode .date {
        color: #e2e8f0;
        background: rgba(255,255,255,0.1);
    }

    .dark-mode td:hover .date {
        background: #6366f1;
        color: white;
    }

    .dark-mode .event {
        background: #2d3a4f !important;
        border-left: 4px solid #94a3b8 !important;
        color: white !important;
        text-shadow: 0 1px 2px black;
        box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    }

    .dark-mode .event b {
        color: white !important;
    }

    /* === PRINT STYLES - RAPI DAN SERAGAM === */
    @media print {
        * {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .sidebar,
        .navbar,
        .footer,
        .btn-print,
        .form-filter,
        .card-header .btn {
            display: none !important;
        }

        .content,
        .card,
        .card-body {
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            background: white !important;
        }

        .calendar-wrapper {
            overflow: visible !important;
            width: 100% !important;
        }

        .calendar {
            min-width: 100% !important;
            width: 100% !important;
            border-collapse: collapse !important;
            border: 1px solid black !important;
            table-layout: fixed !important;
        }

        .calendar th,
        .calendar td {
            border: 1px solid black !important;
            background: white !important;
            color: black !important;
            height: auto !important;
            min-height: 120px !important;
            vertical-align: top !important;
            padding: 4px !important;
            font-size: 10pt !important;
            line-height: 1.2 !important;
            overflow: visible !important;
        }

        .calendar td.empty-cell {
            background-color: #f0f0f0 !important;
        }

        .calendar td.today {
            background-color: #c0c0c0 !important;
            border: 1px solid black !important;
        }

        .date {
            font-weight: bold !important;
            font-size: 10pt !important;
            margin-bottom: 2px !important;
            background: transparent !important;
            color: black !important;
            width: auto !important;
            height: auto !important;
            border-radius: 0 !important;
            display: block !important;
            text-align: left !important;
            padding: 0 !important;
        }

        .cell {
            display: block !important;
            height: auto !important;
            min-height: inherit;
            overflow: visible !important;
        }

        .events {
            display: block !important;
            height: auto !important;
            overflow: visible !important;
        }

        .event {
            background: white !important;
            color: black !important;
            border: 1px solid black !important;
            border-left: 4px solid black !important;
            padding: 2px 4px !important;
            margin-bottom: 2px !important;
            font-size: 8pt !important;
            line-height: 1.3 !important;
            box-shadow: none !important;
            text-shadow: none !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            white-space: normal !important;
            word-wrap: break-word !important;
        }

        .event b {
            font-weight: bold !important;
            color: black !important;
        }

        .event:hover {
            transform: none !important;
            box-shadow: none !important;
        }
    }

    /* === RESPONSIVE UNTUK MOBILE === */
    @media (max-width: 768px) {
        .calendar td {
            font-size: 10px;
        }
        .event {
            font-size: 9px;
            padding: 4px 6px;
        }
        .event b {
            font-size: 10px;
        }
    }

    /* === SCROLLBAR HALUS === */
    .events::-webkit-scrollbar {
        width: 4px;
    }
    .events::-webkit-scrollbar-track {
        background: transparent;
    }
    .events::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 20px;
    }
    .dark-mode .events::-webkit-scrollbar-thumb {
        background: #475569;
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
                                                <option value="<?=str_pad($i,2,'0',STR_PAD_LEFT)?>" <?=($bulan==$i? 'selected': '')?>>
                                                    <?=$namaBulan[$i]?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <select name="tahun" class="form-control" style="width:120px">
                                            <?php for($t=date('Y')-1;$t<=date('Y')+1;$t++){ ?>
                                                <option value="<?=$t?>" <?=($tahun==$t? 'selected': '')?>>
                                                    <?=$t?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <button class="btn btn-success">Tampilkan</button>
                                    </form>

                                    <div class="calendar-wrapper">
                                        <table class="calendar">
                                            <thead>
                                                <tr>
                                                    <th>Sen</th>
                                                    <th>Sel</th>
                                                    <th>Rab</th>
                                                    <th>Kam</th>
                                                    <th>Jum</th>
                                                    <th>Sab</th>
                                                    <th>Min</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                <?php
                                                for($i=1;$i<$startDay;$i++){
                                                    echo "<td class='empty-cell'></td>";
                                                }

                                                for($day=1;$day<=$totalDays;$day++){
                                                    $tgl = "$tahun-$bulan-".str_pad($day,2,'0',STR_PAD_LEFT);
                                                    $class = ($tgl == $today) ? 'today' : '';
                                                    echo "<td class='$class'><div class='cell'>";
                                                    echo "<div class='date'>$day</div>";
                                                    echo "<div class='events'>";
                                                    if(isset($data[$tgl])){
                                                        foreach($data[$tgl] as $e){
                                                            list($bg,$border)=getEventColor($e['mapel']);
                                                            echo "
                                                            <div class='event btn-detail'
                                                                style='background:$bg; border-left:4px solid $border;'
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

                                                $remaining = 7 - (($totalDays + $startDay - 1) % 7);
                                                if($remaining < 7){
                                                    for($i=0;$i<$remaining;$i++){
                                                        echo "<td class='empty-cell'></td>";
                                                    }
                                                }
                                                ?>
                                                </tr>
                                            </tbody>
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

    <!-- MODAL DETAIL -->
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
    document.querySelectorAll('.btn-detail').forEach(el => {
        el.addEventListener('click', function() {
            document.getElementById('m_mapel').innerText = this.dataset.mapel;
            document.getElementById('m_nama').innerText = this.dataset.nama;
            document.getElementById('m_kelas').innerText = this.dataset.kelas;
            document.getElementById('m_durasi').innerText = this.dataset.durasi + ' menit';
            document.getElementById('m_tanggal').innerText = this.dataset.tanggal;
            new bootstrap.Modal(document.getElementById('detailUjianModal')).show();
        });
    });
    </script>
</body>
</html>