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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisa Per Kelas</title>
    <?php include '../inc/css.php'; ?>
    <style>
    @media print {

        #area-cetak-kelas table {
            table-layout: fixed;
            width: 100%;
            font-size: 12px;
        }

        #area-cetak-kelas th,
        #area-cetak-kelas td {
            word-break: break-word;
            padding: 5px;
        }

        #area-cetak-kelas button {
            display: none !important;
        }
    }

    /* ===============================
   MODE LAPORAN RESMI (PDF ONLY)
================================ */

    .print-resmi {
        font-family: "Times New Roman", serif !important;
        font-size: 12pt !important;
        background: #fff !important;
        color: #000 !important;
    }

    .print-resmi .card,
    .print-resmi .card-body,
    .print-resmi .table-responsive {
        box-shadow: none !important;
        border: none !important;
        background: #fff !important;
    }

    .print-resmi .card-header,
    .print-resmi .btn,
    .print-resmi button,
    .print-resmi #infoPeserta,
    .print-resmi #kode_soal,
    .print-resmi #rombel,
    .print-resmi label,
    .print-resmi .row.g-3 {
        display: none !important;
    }

    .print-resmi table {
        border-collapse: collapse !important;
        width: 100% !important;
    }

    .print-resmi th,
    .print-resmi td {
        border: 1px solid #000 !important;
        padding: 6px !important;
        text-align: center !important;
        vertical-align: middle !important;
    }

    .print-resmi th {
        background: #eee !important;
    }

    .print-resmi .table-responsive {
        overflow: visible !important;
    }

    .print-resmi .laporan-header {
        text-align: center;
        margin-bottom: 15px;
    }

    .print-resmi .laporan-garis {
        border-bottom: 2px solid #000;
        margin: 10px 0 20px 0;
    }
    </style>
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

                            <div class="mt-3">


                                <div id="infoPeserta" class="mt-3"></div>
                                <div class="card mt-3" id="kelasBox" style="display:none;">
                                    <div class="mt-3">
                                        <button onclick="exportPDF()" class="btn btn-danger">
                                            <i class="fa-solid fa-file-pdf"></i> Export PDF
                                        </button>
                                    </div>
                                    <div id="area-cetak-kelas">
                                        <div class="card-body">

                                            <div class="table-responsive">

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
                                            </div>
                                            <div class="mt-3 small text-muted">

                                                <b>Keterangan:</b><br>

                                                <b>P Global</b> = Proporsi siswa seluruh kelas yang menjawab benar pada
                                                butir
                                                soal tersebut.<br>
                                                Rumus: <code>Jumlah Benar Seluruh Peserta / Total Peserta</code>
                                                <br><br>

                                                <b>P Kelas</b> = Proporsi siswa pada kelas terpilih yang menjawab benar
                                                pada
                                                butir soal tersebut.<br>
                                                Rumus: <code>Jumlah Benar Kelas / Total Peserta Kelas</code>
                                                <br><br>

                                                <b>Selisih (ΔP)</b> = Perbedaan tingkat kesukaran antara kelas dengan
                                                populasi
                                                global.<br>
                                                Rumus: <code>P Kelas − P Global</code>
                                                <br><br>

                                                Interpretasi:
                                                <ul class="mb-0">
                                                    <li>ΔP &lt; -0.20 → Soal lebih sulit bagi kelas ini (indikasi
                                                        miskonsepsi)
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
    function lihatSoal(kode, nomor) {

        document.getElementById('isiModalSoal').innerHTML = 'Loading...';

        fetch('modal_lihat_soal.php?kode_soal=' + kode + '&nomor=' + nomor)
            .then(res => res.text())
            .then(html => {

                document.getElementById('isiModalSoal').innerHTML = html;

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
    $('#rombel').on('change', function() {

        let kode = $('#kode_soal').val();
        let rombel = $(this).val();

        if (!kode || !rombel) return;

        $.post('ajax_statistik_kelas.php', {
            kode: kode,
            rombel: rombel
        }, function(res) {

            let d = JSON.parse(res);

            $('#infoPeserta').html(d.info);

            let html = "";

            d.data.forEach(r => {

                let cls = "";
                if (r.st == "Perlu Perhatian") cls = "row-bad";
                if (r.st == "Lebih Dikuasai") cls = "row-good";

                html += `
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
    <script src="../assets/html2pdf.js/dist/html2pdf.bundle.min.js"></script>

    <script>
    function exportPDF() {

        const element = document.getElementById('area-cetak-kelas');

        const hadDarkHtml = document.documentElement.classList.contains('dark-mode');
        const hadDarkBody = document.body.classList.contains('dark-mode');

        document.documentElement.classList.remove('dark-mode');
        document.body.classList.remove('dark-mode');

        // =============================
        // TAMBAHKAN HEADER RESMI VIA JS
        // =============================

        const header = document.createElement("div");
        header.className = "laporan-header";
        header.innerHTML = `
        <h3>LAPORAN ANALISA BUTIR SOAL PER KELAS</h3>
        <div>Kode Soal: <b>${$('#kode_soal').val()}</b></div>
        <div>Kelas: <b>${$('#rombel').val()}</b></div>
        <div class="laporan-garis"></div>
    `;

        element.prepend(header);

        element.classList.add("print-resmi");

        html2pdf().set({
            margin: 0.5,
            filename: 'Laporan_Analisa_Kelas_' +
                $('#kode_soal').val() + '_' +
                $('#rombel').val() + '.pdf',
            image: {
                type: 'jpeg',
                quality: 1
            },
            html2canvas: {
                scale: 2,
                backgroundColor: '#ffffff'
            },
            jsPDF: {
                unit: 'in',
                format: 'a4',
                orientation: 'landscape'
            }
        }).from(element).save().then(() => {

            element.classList.remove("print-resmi");
            header.remove();

            if (hadDarkHtml) document.documentElement.classList.add('dark-mode');
            if (hadDarkBody) document.body.classList.add('dark-mode');

        });
    }
    </script>
</body>

</html>