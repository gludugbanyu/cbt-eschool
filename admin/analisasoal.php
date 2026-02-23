<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
include '../inc/dataadmin.php';

$id_admin = $_SESSION['admin_id'] ?? 0;
$role     = $_SESSION['role'] ?? '';

$filter_owner = "";
if ($role != 'admin') {
    $filter_owner = "WHERE FIND_IN_SET('$id_admin', id_pembuat)";
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Analisa Soal</title>
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
                                <i class="fa fa-chart-bar"></i> Analisa Kualitas Soal
                            </h5>
                        </div>

                        <div class="card-body">

                            <div class="row g-3">

                                <!-- MAPEL -->
                                <div class="col-md-6">
                                    <label class="form-label">Pilih Mapel</label>
                                    <select id="mapel" class="form-select">
                                        <option value="">-- Pilih Mapel --</option>
                                        <?php
$qMapel = mysqli_query($koneksi,"
SELECT DISTINCT mapel 
FROM soal
$filter_owner
ORDER BY mapel ASC
");

while($m=mysqli_fetch_assoc($qMapel)){
echo "<option value='{$m['mapel']}'>{$m['mapel']}</option>";
}
?>
                                    </select>
                                </div>

                                <!-- KODE -->
                                <div class="col-md-6">
                                    <label class="form-label">Kode Soal</label>
                                    <select id="kode_soal" class="form-select">
                                        <option value="">-- Pilih Mapel Dulu --</option>
                                    </select>
                                </div>

                            </div>

                            <!-- STATISTIK -->
                            <div class="row mt-4" id="boxStatistik" style="display:none;">

                                <div class="col-md-12">

                                    <div class="card border-0 shadow-sm">

                                        <div class="card-body">

                                            <div class="row gx-3 gy-3 text-center stat-row">

                                                <!-- PESERTA -->
                                                <div class="col-md-3">
                                                    <div class="stat-card">
                                                        <div class="stat-title">Jumlah Peserta</div>
                                                        <div class="stat-value" id="jmlPeserta">0</div>
                                                    </div>
                                                </div>

                                                <!-- RATA -->
                                                <div class="col-md-3">
                                                    <div class="stat-card">
                                                        <div class="stat-title">Rata Nilai</div>
                                                        <div class="stat-value" id="rataNilai">0</div>
                                                    </div>
                                                </div>

                                                <!-- P -->
                                                <div class="col-md-3">
                                                    <div class="stat-card" id="boxP">
                                                        <div class="indicator" id="indP"></div>
                                                        <div class="stat-title">Indeks Kesukaran</div>
                                                        <div class="stat-value" id="indeksP">0</div>
                                                        <small class="stat-meta" id="ketP"></small>
                                                    </div>
                                                </div>

                                                <!-- D -->
                                                <div class="col-md-3">
                                                    <div class="stat-card" id="boxD">
                                                        <div class="indicator" id="indD"></div>
                                                        <div class="stat-title">Daya Beda</div>
                                                        <div class="stat-value" id="dayaD">0</div>
                                                        <small class="stat-meta" id="ketD"></small>
                                                    </div>
                                                </div>

                                                <!-- SD -->
                                                <div class="col-md-3">
                                                    <div class="stat-card">
                                                        <div class="stat-title">Standar Deviasi</div>
                                                        <div class="stat-value" id="sd">0</div>
                                                    </div>
                                                </div>

                                                <!-- CV -->
                                                <div class="col-md-3">
                                                    <div class="stat-card">
                                                        <div class="stat-title">Koef Variasi</div>
                                                        <div class="stat-value" id="cv">0</div>
                                                    </div>
                                                </div>

                                                <!-- PL -->
                                                <div class="col-md-3">
                                                    <div class="stat-card">
                                                        <div class="stat-title">Proporsi Lulus</div>
                                                        <div class="stat-value" id="pl">0</div>
                                                    </div>
                                                </div>

                                                <!-- EF -->
                                                <div class="col-md-3">
                                                    <div class="stat-card">
                                                        <div class="stat-title">Efektivitas</div>
                                                        <div class="stat-value" id="ef">0</div>
                                                    </div>
                                                </div>

                                            </div>

                                            <hr>

                                            <!-- REKOMENDASI -->
                                            <div id="rekomendasiBox" class="rekom-box mt-3" style="display:none;">
                                                <b>🔴 Rekomendasi:</b>
                                                <span id="rekomendasiText"></span>
                                            </div>

                                            <div class="text-end mt-3">
                                                <a id="btnMasukButir" class="btn btn-primary">
                                                    <i class="fa fa-chart-line"></i> Analisa Per Butir
                                                </a>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="metode-wrap mt-3">

                                <div class="metode-title">
                                    Dasar Perhitungan Statistik
                                </div>

                                <div class="metode-grid">

                                    <div class="metode-item">
                                        <div class="metode-head">Indeks Kesukaran</div>
                                        <div class="metode-formula">Mean / 100</div>
                                        <div class="metode-note">
                                            &lt;0.30 Sukar<br>
                                            0.30–0.70 Sedang<br>
                                            &gt;0.70 Mudah
                                        </div>
                                    </div>

                                    <div class="metode-item">
                                        <div class="metode-head">Daya Beda</div>
                                        <div class="metode-formula">(Atas−Bawah)/100</div>
                                        <div class="metode-note">
                                            &lt;0.20 Jelek<br>
                                            0.20–0.39 Cukup<br>
                                            ≥0.40 Baik
                                        </div>
                                    </div>

                                    <div class="metode-item">
                                        <div class="metode-head">Standar Deviasi</div>
                                        <div class="metode-formula">√Σ(x−x̄)²/n</div>
                                        <div class="metode-note">
                                            &lt;10 Homogen<br>
                                            &gt;20 Tidak stabil
                                        </div>
                                    </div>

                                    <div class="metode-item">
                                        <div class="metode-head">Koef Variasi</div>
                                        <div class="metode-formula">SD / Mean</div>
                                        <div class="metode-note">
                                            &lt;0.20 Baik<br>
                                            &gt;0.30 Kurang stabil
                                        </div>
                                    </div>

                                    <div class="metode-item">
                                        <div class="metode-head">Proporsi Lulus</div>
                                        <div class="metode-formula">Lulus / N</div>
                                        <div class="metode-note">
                                            ≥75 = Tuntas
                                        </div>
                                    </div>

                                    <div class="metode-item">
                                        <div class="metode-head">Efektivitas</div>
                                        <div class="metode-formula">D(1−|0.5−P|)</div>
                                        <div class="metode-note">
                                            &lt;0.15 Jelek<br>
                                            &gt;0.30 Efektif
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

    <?php include '../inc/js.php'; ?>

    <script>
    // LOAD KODE SOAL BERDASAR MAPEL
    $('#mapel').on('change', function() {

        let mapel = $(this).val();

        if (!mapel) {
            $('#kode_soal').html('<option>-- Pilih Mapel Dulu --</option>');
            $('#boxStatistik').hide();
            return;
        }

        $('#kode_soal').html('<option>Loading...</option>');

        $.post('ajax_kodesoal_mapel.php', {
            mapel: mapel
        }, function(res) {
            $('#kode_soal').html(res);
        });

    });


    $('#kode_soal').on('change', function() {

        let kode = $(this).val();

        if (!kode) {
            $('#boxStatistik').hide();
            return;
        }

        $.post('ajax_statistik_soal.php', {
            kode: kode
        }, function(res) {

            let d = JSON.parse(res);

            // =====================
            // SET DATA
            // =====================

            $('#jmlPeserta').text(d.jml);
            $('#rataNilai').text(d.rata);
            $('#indeksP').text(d.p);
            $('#ketP').text(d.ket_p);
            $('#dayaD').text(d.d);
            $('#ketD').text(d.ket_d);
            $('#sd').text(d.sd);
            $('#cv').text(d.cv);
            $('#pl').text((d.pl * 100).toFixed(0) + '%');
            $('#ef').text(d.ef);

            // =====================
            // STYLE META
            // =====================

            $('#ketP').removeClass('meta-sukar meta-sedang meta-mudah');
            $('#ketD').removeClass('meta-jelek meta-cukup meta-baik');

            if (d.p < 0.3) $('#ketP').addClass('meta-sukar');
            else if (d.p < 0.7) $('#ketP').addClass('meta-sedang');
            else $('#ketP').addClass('meta-mudah');

            if (d.d < 0.2) $('#ketD').addClass('meta-jelek');
            else if (d.d < 0.4) $('#ketD').addClass('meta-cukup');
            else $('#ketD').addClass('meta-baik');

            // =====================
            // RESET BORDER
            // =====================

            $('#indP').removeClass('p-sukar p-sedang p-mudah');
            $('#indD').removeClass('d-jelek d-cukup d-baik');
            $('#rekomendasiBox').hide();

            // =====================
            // REKOMENDASI + VALIDASI N
            // =====================

            let rekom = "";
            let tipe = "rekom-bad";
            let warnN = "";
            let n = d.jml;

            // WARNING JUMLAH PESERTA
            if (n < 10) {
                warnN = "Jumlah peserta (" + n +
                    ") terlalu sedikit untuk analisa statistik yang representatif.<br>";
                tipe = "rekom-info";
            } else if (n < 20) {
                warnN = "Jumlah peserta (" + n +
                    ") masih kurang stabil, interpretasi daya beda perlu kehati-hatian.<br>";
                tipe = "rekom-warn";
            }

            // ANALISA NORMAL
            if (d.p > 0.7 && d.d < 0.2) {
                rekom =
                    "Soal terlalu mudah dan tidak mampu membedakan kemampuan siswa ➜ Disarankan REVISI.";
                tipe = "rekom-bad";
            } else if (d.p < 0.3 && d.d < 0.2) {
                rekom = "Soal terlalu sukar dan daya beda rendah ➜ Disarankan DIGANTI.";
                tipe = "rekom-bad";
            } else if (d.d < 0.2) {
                rekom = "Daya beda sangat rendah ➜ Butir soal perlu evaluasi.";
                tipe = "rekom-bad";
            } else if (d.p > 0.85) {
                rekom = "Soal sangat mudah ➜ Kurangi distraktor.";
                tipe = "rekom-warn";
            }

            // =====================
            // PRIORITAS N KECIL
            // =====================

            if (n < 20) {

                $('#rekomendasiText').html(
                    warnN +
                    (rekom ?
                        "<br><i>*Catatan: Interpretasi kualitas soal belum stabil karena ukuran sampel kecil.</i>" :
                        "")
                );

                $('#rekomendasiBox')
                    .removeClass('rekom-info rekom-warn rekom-bad')
                    .addClass(tipe)
                    .fadeIn();

            } else if (rekom) {

                $('#rekomendasiText').html(rekom);
                $('#rekomendasiBox')
                    .removeClass('rekom-info rekom-warn rekom-bad')
                    .addClass(tipe)
                    .fadeIn();

            }

            // =====================
            // LINK ANALISA
            // =====================

            $('#btnMasukButir').attr(
                'href', 'analisa_perbutir.php?kode_soal=' + kode
            );

            // =====================
            // SHOW
            // =====================

            $('#boxStatistik').fadeIn();

        });

    });
    </script>
</body>

</html>