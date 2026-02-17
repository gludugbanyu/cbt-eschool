<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
include '../inc/dataadmin.php';

$kode_soal = $_GET['kode_soal'] ?? '';
if(empty($kode_soal)) die('Kode soal kosong');

only_preview_soal_by_kode($kode_soal);

/* ================= HELPER ================= */
function parseJS($str){
    preg_match_all('/\[(\d+):([^\]]*)\]/',$str,$m,PREG_SET_ORDER);
    $r=[];
    foreach($m as $x){
        $r[(int)$x[1]] = trim($x[2]);
    }
    return $r;
}
function bersihkanKunci($str){
    $out=''; $in=false;
    for($i=0;$i<strlen($str);$i++){
        if($str[$i]=='[') $in=true;
        if($str[$i]==']') $in=false;
        if($str[$i]==',' && !$in) continue;
        $out.=$str[$i];
    }
    return $out;
}

/* ================= AMBIL KUNCI ================= */
$qk = mysqli_query($koneksi,"SELECT kunci FROM soal WHERE kode_soal='$kode_soal'");
$dk = mysqli_fetch_assoc($qk);
$kunci_fix = bersihkanKunci($dk['kunci']);
preg_match_all('/\[(.*?)\]/',$kunci_fix,$km);
$kunci_list = $km[1];

$total_kunci = count($kunci_list);
$nilai_per_soal = $total_kunci > 0 ? 100 / $total_kunci : 0;

$bobot_per_soal=[];
foreach($kunci_list as $item){
    list($no,) = explode(':',$item,2);
    $bobot_per_soal[(int)$no] = $nilai_per_soal;
}

/* ================= TIPE MAP ================= */
$tipe_map=[];
$qtipe=mysqli_query($koneksi,"SELECT nomer_soal, tipe_soal FROM butir_soal WHERE kode_soal='$kode_soal'");
while($t=mysqli_fetch_assoc($qtipe)){
    $tipe_map[(int)$t['nomer_soal']] = strtolower($t['tipe_soal']);
}

/* ================= KOMPOSISI JENIS ================= */
$jenis = [
    'pilihan ganda'=>0,
    'pilihan ganda kompleks'=>0,
    'benar/salah'=>0,
    'menjodohkan'=>0,
    'uraian'=>0
];
$qJenis = mysqli_query($koneksi,"
    SELECT LOWER(tipe_soal) as tipe, COUNT(*) as jml
    FROM butir_soal
    WHERE kode_soal='$kode_soal'
    GROUP BY tipe_soal
");
while($j=mysqli_fetch_assoc($qJenis)){
    if(isset($jenis[$j['tipe']])){
        $jenis[$j['tipe']]=$j['jml'];
    }
}

/* ================= STATISTIK PAKAI SKOR ASLI ================= */
$stat=[];

$qnilai=mysqli_query($koneksi,"
    SELECT jawaban_siswa, detail_uraian
    FROM nilai 
    WHERE kode_soal='$kode_soal'
");

while($row=mysqli_fetch_assoc($qnilai)){

    $jawab=parseJS($row['jawaban_siswa']);

    foreach($kunci_list as $item){

        list($no,$isi_kunci)=explode(':',$item,2);
        $no=(int)$no;

        if(!isset($stat[$no])){
            $stat[$no]=['total'=>0,'skor'=>0];
        }

        $stat[$no]['total']++;

        $js = strtolower(trim($jawab[$no]??''));
        $kj = strtolower(trim($isi_kunci));
        $tipe = $tipe_map[$no] ?? '';

        if($tipe=='uraian'){

            // ambil skor dari kolom yang BENAR
            $du = parseJS($row['detail_uraian'] ?? '');
        
            $skorU = floatval($du[$no] ?? 0);
        
            $stat[$no]['skor'] += $skorU;
        
            continue;
        }
        
        

        $skor = 0;

        if($tipe=='benar/salah' || $tipe=='menjodohkan'){
            $kArr = array_map('trim', explode('|',$kj));
            $jArr = array_map('trim', explode('|',$js));
            $jumlah = count($kArr);
            $benar = 0;
            for($i=0;$i<$jumlah;$i++){
                if(isset($jArr[$i]) && $jArr[$i]==$kArr[$i]) $benar++;
            }
            $skor = ($benar/$jumlah)*$nilai_per_soal;
        }
        elseif($tipe=='pilihan ganda kompleks'){
            $kArr = array_map('trim', explode(',', str_replace('|',',',$kj)));
            $jArr = array_map('trim', explode(',', str_replace('|',',',$js)));
            $jumlah = count($kArr);
            $benar = 0;
            foreach($jArr as $j){
                if(in_array($j,$kArr)) $benar++;
            }
            $skor = ($benar/$jumlah)*$nilai_per_soal;
        }
        else{
            if($js==$kj) $skor=$nilai_per_soal;
        }

        $stat[$no]['skor'] += $skor;
    }
}

ksort($stat);

/* ================= HEADER INFO ================= */
$qInfo = mysqli_query($koneksi,"
    SELECT COUNT(DISTINCT id_siswa) as jumlah_siswa
    FROM nilai
    WHERE kode_soal='$kode_soal'
");
$jumlah_siswa = mysqli_fetch_assoc($qInfo)['jumlah_siswa'] ?? 0;

/* ================= KELAS & PARTISIPASI ================= */
$qKelas = mysqli_query($koneksi,"
    SELECT DISTINCT s.kelas
    FROM nilai n
    JOIN siswa s ON n.id_siswa = s.id_siswa
    WHERE n.kode_soal = '$kode_soal'
");
$kelas_list = [];
while($k = mysqli_fetch_assoc($qKelas)){
    $kelas_list[] = $k['kelas'];
}
$kelas_text = implode(', ', $kelas_list);

$total_siswa_kelas = 0;
foreach($kelas_list as $kls){
    $q = mysqli_query($koneksi,"SELECT COUNT(*) as jml FROM siswa WHERE kelas='$kls'");
    $total_siswa_kelas += mysqli_fetch_assoc($q)['jml'];
}

$partisipasi = $total_siswa_kelas > 0
    ? ($jumlah_siswa / $total_siswa_kelas) * 100
    : 0;

/* ================= RATA KEBERHASILAN ================= */
$total_semua=0;
foreach($stat as $s){
    $total_semua += $s['skor'];
}
$rata_keberhasilan = $total_semua>0
    ? ($total_semua / ($jumlah_siswa*100)) * 100
    : 0;

/* ================= SOAL BERMASALAH ================= */
$bad=[];
foreach($stat as $no=>$s){
    $p = ($s['skor']/($s['total']*$nilai_per_soal))*100;
    if($p<40) $bad[]=$no;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisa Perbutir</title>
    <?php include '../inc/css.php'; ?>
    <style>
    @media print {

        body {
            margin: 0;
        }

        .wrapper,
        .main,
        .content,
        .container-fluid,
        .card,
        .card-body {
            overflow: visible !important;
            height: auto !important;
        }

        #area-cetak {
            position: static !important;
            width: 100% !important;
        }

        .table-responsive {
            overflow: visible !important;
        }

        table {
            page-break-inside: auto;
            font-size: 12px;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .progress {
            height: 14px;
        }

        button {
            display: none !important;
        }
    }

    #tabelAnalisa {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    #tabelAnalisa th,
    #tabelAnalisa td {
        border: 1px solid #333;
        padding: 6px;
    }

    .progress {
        background: #eee;
    }

    .progress-bar {
        color: #fff;
        text-align: center;
        font-size: 11px;
    }

    .table-responsive {
        overflow-x: auto;
    }

    @media print {
        .table-responsive {
            overflow: visible !important;
        }
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
                    <div class="card shadow">
                        <div class="card-body" id="area-cetak">

                            <!-- HEADER PROFESIONAL -->
                            <div class="card mb-4 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">

                                        <div>
                                            <h4 class="mb-3">Analisa Perbutir Soal</h4>
                                            <table class="table table-sm table-borderless mb-0">
                                                <tr>
                                                    <td width="220">Kode Soal</td>
                                                    <td>: <strong><?= $kode_soal ?></strong></td>
                                                </tr>
                                                <tr>
                                                    <td>Kelas Peserta</td>
                                                    <td>: <strong><?= htmlspecialchars($kelas_text) ?></strong></td>
                                                </tr>
                                                <tr>
                                                    <td>Total Siswa Kelas</td>
                                                    <td>: <strong><?= $total_siswa_kelas ?></strong> siswa</td>
                                                </tr>
                                                <tr>
                                                    <td>Peserta Mengerjakan</td>
                                                    <td>: <strong><?= $jumlah_siswa ?></strong> siswa</td>
                                                </tr>
                                                <tr>
                                                    <td>Tingkat Partisipasi</td>
                                                    <td>: <strong><?= number_format($partisipasi,1) ?>%</strong></td>
                                                </tr>
                                                <tr>
                                                    <td>Rata-rata Ketepatan</td>
                                                    <td>: <strong><?= number_format($rata_keberhasilan,1) ?>%</strong>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>

                                        <div class="text-end">
                                            <button onclick="exportPDF()" class="btn btn-danger mb-2">
                                                <i class="fa-solid fa-file-pdf"></i> Export PDF
                                            </button>
                                            <button onclick="printAnalisa()" class="btn btn-secondary mb-2">
                                                <i class="fa fa-print"></i> Print
                                            </button>
                                            <br>
                                            <small class="text-muted">
                                                Statistik dihitung dari skor asli sistem penilaian.
                                            </small>
                                        </div>


                                    </div>
                                </div>
                            </div>


                            <?php if(count($bad)>0): ?>
                            <div class="alert alert-danger">
                                <strong>Perhatian!</strong> Soal nomor <?= implode(', ',$bad) ?> perlu evaluasi.
                            </div>
                            <?php endif; ?>
                            <div class="table-responsive">
                                <table id="tabelAnalisa" class="table table-bordered table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>No</th>
                                            <th>Bobot</th>
                                            <th>Tipe Soal</th>
                                            <th>Skor Terkumpul</th>
                                            <th>% Benar</th>
                                            <th>Kualitas</th>
                                            <th>Rekomendasi</th>
                                            <th>Lihat</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php foreach($stat as $no=>$s):
    $p = ($s['skor']/($s['total']*$nilai_per_soal))*100;

    if($p>=85){ $badge="success"; $ket="Sangat Mudah"; $saran="Tingkatkan kesulitan atau perbaiki distraktor"; }
    elseif($p>=60){ $badge="primary"; $ket="Sesuai Harapan"; $saran="Soal sudah baik, pertahankan"; }
    elseif($p>=40){ $badge="warning"; $ket="Cukup Menantang"; $saran="Perjelas redaksi soal"; }
    else{ $badge="danger"; $ket="Perlu Evaluasi"; $saran="Periksa kunci atau kemungkinan soal ambigu"; }
    $tipeAsli = $tipe_map[$no] ?? '';

switch($tipeAsli){
    case 'pilihan ganda': $tipeLabel='PG'; break;
    case 'pilihan ganda kompleks': $tipeLabel='PGX'; break;
    case 'benar/salah': $tipeLabel='BS'; break;
    case 'menjodohkan': $tipeLabel='MJD'; break;
    case 'uraian': $tipeLabel='U'; break;
    default: $tipeLabel='-';
}
?>
                                        <tr>
                                            <td><?= $no ?></td>
                                            <td class="text-primary fw-bold">
                                                <?= number_format($bobot_per_soal[$no],2) ?></td>
                                            <td class="fw-bold text-center"><?= $tipeLabel ?></td>

                                            <td class="text-success fw-bold"><?= number_format($s['skor'],2) ?></td>
                                            <td>
                                                <div class="progress" style="height:18px;">
                                                    <div class="progress-bar bg-<?= $badge ?>" style="width:<?= $p ?>%">
                                                        <?= number_format($p,1) ?>%
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="badge bg-<?= $badge ?>"><?= $ket ?></span></td>
                                            <td><?= $saran ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-dark"
                                                    onclick="lihatSoal('<?= $kode_soal ?>', <?= $no ?>)">
                                                    Lihat
                                                </button>
                                            </td>



                                        </tr>
                                        <?php endforeach; ?>
                                        <script src="../assets/html2pdf.js/dist/html2pdf.bundle.min.js"></script>
                                        <script>
                                        function exportPDF() {
                                            const element = document.getElementById('area-cetak');

                                            html2pdf().set({
                                                margin: 0.2,
                                                filename: 'Analisa_<?= $kode_soal ?>.pdf',
                                                image: {
                                                    type: 'jpeg',
                                                    quality: 1
                                                },
                                                html2canvas: {
                                                    scale: 3,
                                                    useCORS: true
                                                },
                                                jsPDF: {
                                                    unit: 'in',
                                                    format: 'a4',
                                                    orientation: 'portrait'
                                                },
                                                pagebreak: {
                                                    mode: ['avoid-all', 'css', 'legacy']
                                                }
                                            }).from(element).save();
                                        }
                                        </script>
                                        <script>
                                        function printAnalisa() {
                                            window.print();
                                        }
                                        </script>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

        </div>
    </div>
 <!-- MODAL PREVIEW SOAL -->
 <div class="modal fade" id="modalSoal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header">
                <h5 class="modal-title">Preview Butir Soal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body" id="isiModalSoal" style="max-height:90vh;overflow:auto;">
                Loading...
            </div>

        </div>
    </div>
</div>

    <script>
    function lihatSoal(kode, nomor){

    const modalEl = document.getElementById('modalSoal');

    document.getElementById('isiModalSoal').innerHTML = 'Loading...';

    fetch('modal_lihat_soal.php?kode_soal='+kode+'&nomor='+nomor)
        .then(res => res.text())
        .then(html => {

            document.getElementById('isiModalSoal').innerHTML = html;

            const modal = new bootstrap.Modal(modalEl);
            modal.show();

        });

}
    </script>
    <?php include '../inc/js.php'; ?>
</body>

</html>