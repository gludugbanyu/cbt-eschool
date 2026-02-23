<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
include '../inc/dataadmin.php';
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Analisa Per Kelas</title>
    <?php include '../inc/css.php'; ?>
</head>

<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="main">
            <?php include 'navbar.php'; ?>

            <main class="content">
                <div class="container-fluid">

                    <div class="card shadow">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fa fa-layer-group"></i> Analisa Butir Per Kelas
                            </h5>
                        </div>

                        <div class="card-body">

                            <div class="row g-3">

                                <!-- SOAL -->
                                <div class="col-md-6">
                                    <label>Kode Soal</label>
                                    <select id="kode_soal" class="form-select">
                                        <option value="">-- Pilih Soal --</option>
                                        <?php
                                    $q=mysqli_query($koneksi,"
                                    SELECT kode_soal,nama_soal
                                    FROM soal
                                    ORDER BY kode_soal ASC
                                    ");
                                    while($d=mysqli_fetch_assoc($q)){
                                    echo "<option value='{$d['kode_soal']}'>
                                    {$d['kode_soal']} - {$d['nama_soal']}
                                    </option>";
                                    }
                                    ?>
                                    </select>
                                </div>

                                <!-- ROMBEL -->
                                <div class="col-md-6">
                                    <label>Kelas</label>
                                    <select id="rombel" class="form-select">
                                        <option value="">-- Pilih Soal Dulu --</option>
                                    </select>
                                </div>

                            </div>

                            <div id="infoPeserta" class="mt-3"></div>

                            <div class="card mt-3" id="kelasBox" style="display:none;">
                                <div class="card-body">

                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>No Soal</th>
                                                <th>P Global</th>
                                                <th>P Kelas</th>
                                                <th>ΔP</th>
                                                <th>Status</th>
                                                <th>Preview Soal</th>
                                            </tr>
                                        </thead>
                                        <tbody id="isi"></tbody>
                                    </table>
                                    <div class="mt-3 small text-muted">

                                        <b>Keterangan:</b><br>

                                        <b>P Global</b> = Proporsi siswa seluruh kelas yang menjawab benar pada butir
                                        soal tersebut.<br>
                                        Rumus: <code>Jumlah Benar Seluruh Peserta / Total Peserta</code>
                                        <br><br>

                                        <b>P Kelas</b> = Proporsi siswa pada kelas terpilih yang menjawab benar pada
                                        butir soal tersebut.<br>
                                        Rumus: <code>Jumlah Benar Kelas / Total Peserta Kelas</code>
                                        <br><br>

                                        <b>Selisih (ΔP)</b> = Perbedaan tingkat kesukaran antara kelas dengan populasi
                                        global.<br>
                                        Rumus: <code>P Kelas − P Global</code>
                                        <br><br>

                                        Interpretasi:
                                        <ul class="mb-0">
                                            <li>ΔP &lt; -0.20 → Soal lebih sulit bagi kelas ini (indikasi miskonsepsi)
                                            </li>
                                            <li>-0.20 s/d 0.20 → Normal (sesuai populasi umum)</li>
                                            <li>ΔP &gt; 0.20 → Soal lebih mudah bagi kelas ini</li>
                                        </ul>

                                    </div>


                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </main>

        </div>
    </div>
    <!-- MODAL PREVIEW -->
    <div class="modal fade" id="modalSoal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Preview Butir Soal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" id="isiModalSoal" style="max-height:90vh;overflow:auto;">
                    Loading...
                </div>

            </div>
        </div>
    </div>
    <?php include '../inc/js.php'; ?>
<script>
function lihatSoal(kode, nomor){

document.getElementById('isiModalSoal').innerHTML='Loading...';

fetch('modal_lihat_soal.php?kode_soal='+kode+'&nomor='+nomor)
.then(res=>res.text())
.then(html=>{

document.getElementById('isiModalSoal').innerHTML=html;

new bootstrap.Modal(
document.getElementById('modalSoal')
).show();

});

}
</script>
    <script>
    // LOAD ROMBEL
    $('#kode_soal').on('change', function() {

        let kode = $(this).val();

        $.post('ajax_kelas_dari_soal.php', {
            kode: kode
        }, function(res) {
            $('#rombel').html(res);
        });

    });

    // ANALISA
// ANALISA
$('#rombel').on('change', function(){

let kode   = $('#kode_soal').val();
let rombel = $(this).val();

if(!kode || !rombel) return;

$.post('ajax_statistik_kelas.php',{
kode:kode,
rombel:rombel
},function(res){

let d = JSON.parse(res);

$('#infoPeserta').html(d.info);

let html="";

d.data.forEach(r=>{

let cls="";
if(r.st=="Perlu Perhatian") cls="row-bad";
if(r.st=="Lebih Dikuasai") cls="row-good";

html+=`
<tr class="${cls}">
<td>${r.no}</td>
<td>${r.Pg}</td>
<td>${r.Pk}</td>
<td>${r.d}</td>
<td>${r.st}</td>
<td>
<button class="btn btn-sm btn-outline-dark"
onclick="lihatSoal('${kode}', ${r.no})">
Lihat Soal
</button>
</td>
</tr>
`;

});

$('#isi').html(html);
$('#kelasBox').fadeIn();

});

});
    </script>
</body>

</html>