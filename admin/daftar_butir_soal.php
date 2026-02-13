<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
include '../inc/dataadmin.php';

if (!isset($_GET['kode_soal'])) {
    header('Location: soal.php');
    exit();
}

$kode_soal_raw = $_GET['kode_soal'];
$kode_soal = mysqli_real_escape_string($koneksi, $kode_soal_raw);

only_pemilik_soal_by_kode($kode_soal_raw);
// Ambil data soal
$query_soal = mysqli_query($koneksi, "SELECT * FROM soal WHERE kode_soal='$kode_soal'");
$data_soal = mysqli_fetch_assoc($query_soal);
if ($data_soal['status'] == 'Aktif') {
        $_SESSION['warning_message'] = "Soal ini sudah aktif dan tidak bisa diedit!.";
    header('Location: soal.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Butir Soal</title>
    <?php include '../inc/css.php'; ?>
    <link href="../assets/summernote/summernote-bs5.css" rel="stylesheet">
    <style>
    .table-wrapper {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .dataTables_paginate {
        display: block;
        text-align: center;
        margin-top: 10px;
    }

    .dataTables_paginate .paginate_button {
        padding: 5px 10px;
        margin: 0 5px;
        background-color: #f4f4f4;
        border: 1px solid #ddd;
        cursor: pointer;
    }

    .dataTables_paginate .paginate_button:hover {
        background-color: #007bff;
        color: white;
    }

    table img {
        max-width: 150px;
        max-height: 150px;
        height: auto;
        object-fit: contain;
    }

    table th,
    table td {
        text-align: left !important;
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
                    <!-- Tampilkan data soal -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Daftar Butir Soal</h5>
                                    <br>
                                    <h2 class="">
                                    <strong>Kode Soal: <?= htmlspecialchars($data_soal['kode_soal']) ?></strong>
                                </h2>

                                <?php if ($data_soal['jumlah_opsi'] == 5): ?>
                                    <span class="badge bg-info">Jumlah Opsi: 5 (A–E)</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Jumlah Opsi: 4 (A–D)</span>
                                <?php endif; ?>
                                <br><br>

                                    <?php
                                $query_butir = mysqli_query($koneksi, "SELECT * FROM butir_soal WHERE kode_soal='$kode_soal' ORDER BY id_soal ASC");
                                $jumlah_pg = 0;
                                $jumlah_pg_kompleks = 0;
                                $jumlah_benar_salah = 0;
                                $jumlah_menjodohkan = 0;
                                $jumlah_uraian = 0;
                                while ($data = mysqli_fetch_assoc($query_butir)) {
                                    switch ($data['tipe_soal']) {
                                        case 'Pilihan Ganda':
                                            $jumlah_pg++;
                                            break;
                                        case 'Pilihan Ganda Kompleks':
                                            $jumlah_pg_kompleks++;
                                            break;
                                        case 'Benar/Salah':
                                            $jumlah_benar_salah++;
                                            break;
                                        case 'Menjodohkan':
                                            $jumlah_menjodohkan++;
                                            break;
                                        case 'Uraian':
                                            $jumlah_uraian++;
                                            break;
                                    }
                                }
                                echo "<p>PG: " . $jumlah_pg . " | PGX: " . $jumlah_pg_kompleks . " | BS: " . $jumlah_benar_salah . " | MJD: " . $jumlah_menjodohkan . " | U: " . $jumlah_uraian . " <p>";
                                ?>
                                    <div class="table-wrapper">
                                        <?php

                                // Cari nomor yang hilang dulu
                                $query_gap = mysqli_query($koneksi, "
                                    SELECT MIN(t1.nomer_soal + 1) AS nomor_lompatan
                                    FROM butir_soal t1
                                    LEFT JOIN butir_soal t2 
                                        ON t2.nomer_soal = t1.nomer_soal + 1 AND t2.kode_soal = '$kode_soal'
                                    WHERE t1.kode_soal = '$kode_soal' AND t2.nomer_soal IS NULL
                                ");
                                
                                $data_gap = mysqli_fetch_assoc($query_gap);
                                $nomor_baru = $data_gap['nomor_lompatan'];
                                
                                // Jika tidak ada gap (misalnya hasilnya NULL), ambil MAX + 1
                                if (!$nomor_baru) {
                                    $query_last = mysqli_query($koneksi, "SELECT MAX(nomer_soal) AS nomor_terakhir FROM butir_soal WHERE kode_soal = '$kode_soal'");
                                    $data = mysqli_fetch_assoc($query_last);
                                    $nomor_terakhir = $data['nomor_terakhir'] ?? 0;
                                    $nomor_baru = $nomor_terakhir + 1;
                                }
                                
                                ?>
                                        <a href="soal.php" class="btn btn btn-outline-secondary">
                                            <i class="fas fa-arrow-left"></i> Bank Soal
                                        </a>
                                        <a href="tambah_butir_soal.php?kode_soal=<?= htmlspecialchars($data_soal['kode_soal']) ?>&nomer_baru=<?= $nomor_baru ?>&opsi=<?= $data_soal['jumlah_opsi'] ?>"
                                            class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Tambah Soal
                                        </a>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-outline-secondary dropdown-toggle"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-cogs"></i> Aksi
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item"
                                                    href="preview_soal.php?kode_soal=<?= $kode_soal; ?>&opsi=<?= $data_soal['jumlah_opsi'] ?>">
                                                        <i class="fas fa-eye"></i> Preview
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                    href="export_excel.php?kode_soal=<?= urlencode($kode_soal) ?>&opsi=<?= $data_soal['jumlah_opsi'] ?>">
                                                        <i class="fas fa-file-excel"></i> Export Soal Excel
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                        data-bs-target="#modalImportExcel">
                                                        <i class="fas fa-file-excel text-success"></i> Import Soal Excel
                                                    </a>
                                                </li>
                                                <li>
                                                <a class="dropdown-item" href="#" 
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalImportDocx">
                                                    <i class="fas fa-file-word text-primary"></i>
                                                    Import Soal DOCX
                                                </a>
                                            </li>
                                            </ul>
                                        </div>


                                        <table id="butirsoal" class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>No Soal</th>
                                                    <th>Pertanyaan</th>
                                                    <th>Tipe Soal</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
    <?php
    $query_butir = mysqli_query($koneksi, "SELECT * FROM butir_soal WHERE kode_soal='$kode_soal' ORDER BY nomer_soal ASC");
    while ($butir = mysqli_fetch_assoc($query_butir)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($butir['nomer_soal']) . "</td>";
        echo "<td>" . $butir['pertanyaan'] . "</td>";
        echo "<td>" . htmlspecialchars($butir['tipe_soal']) . "</td>";
        echo "<td>" . htmlspecialchars($butir['status_soal']) . "</td>";
        
        echo "<td>
        <button 
            class='btn btn-sm btn-outline-dark'
            onclick=\"lihatSoal('".$kode_soal."', ".$butir['nomer_soal'].")\">
            <i class='fa fa-eye'></i> Lihat
        </button>
        
        <a href='edit_butir_soal.php?id_soal=" . $butir['id_soal'] . "&kode_soal=" . urlencode($kode_soal) . "&opsi=" . $data_soal['jumlah_opsi'] . "' class='btn btn-sm btn-primary'>
            <i class='fas fa-edit'></i> Edit
        </a>
        
        <button class='btn btn-sm btn-danger btn-hapus' data-id='" . $butir['id_soal'] . "'>
            <i class='fa fa-close'></i> Hapus
        </button>
        </td>";
        

        echo "</tr>";
    }
    ?>
</tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
<div class="modal fade" id="modalImportExcel" tabindex="-1" aria-labelledby="modalImportExcelLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> 
        <form action="import_soal.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="kode_soal" value="<?= $kode_soal; ?>">
            <input type="hidden" name="opsi" value="<?= $data_soal['jumlah_opsi'] ?>">
            
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalImportExcelLabel" style="color:white;">
                        <i class="fas fa-file-excel me-2"></i> Import Soal dari Excel
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                
                <div class="modal-body">
                    <div class="card mb-3 border-success">
                        <div class="card-header bg-success-subtle py-2">
                            <strong><i class="fas fa-info-circle"></i> Panduan Format Excel:</strong>
                        </div>
                        <div class="card-body py-2 px-3 small">
                            <ul class="mb-2">
                                <li><strong>Kode Soal:</strong> Kolom B <b>WAJIB</b> berisi: <span class="badge bg-dark"><?= $kode_soal ?></span></li>
                                <li><strong>Tipe Soal:</strong> Isi dengan <code>Pilihan Ganda</code>, <code>Pilihan Ganda Kompleks</code>, <code>Menjodohkan</code>, atau <code>Benar/Salah</code>.</li>
                            </ul>

                            <hr class="my-2">

                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong>Format Benar/Salah:</strong></p>
                                    <ul class="mb-2">
                                        <li>Isi pernyataan di Kolom Pilihan 1, 2, dst.</li>
                                        <li>Jawaban Benar isi dengan: <code>Benar|Salah|Benar</code> (pisahkan dengan <code>|</code>).</li>
                                    </ul>
                                </div>
                                <div class="col-md-6 border-start">
                                    <p class="mb-1"><strong>Format Menjodohkan:</strong></p>
                                    <ul class="mb-2">
                                        <li>Isi item kiri di Kolom Pilihan 1, 2, dst.</li>
                                        <li>Jawaban Benar isi dengan format <code>Kiri:Kanan</code> dipisah <code>|</code>.</li>
                                        <li>Contoh: <code>Emas:Logam|Bayam:Sayur</code></li>
                                    </ul>
                                </div>
                            </div>

                            <hr class="my-2">
                            
                            <ul class="mb-0">
                                <li><strong>Pilihan Ganda:</strong> Jawaban benar isi dengan <code>pilihan_1</code> atau <code>pilihan_1,pilihan_2</code> (untuk Kompleks).</li>
                                <li><strong>Gambar:</strong> <b>Tidak didukung di Excel</b>. Gunakan fitur <b>Import DOCX</b> jika ada gambar.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="file_excel" class="form-label fw-bold">Pilih File Excel (.xlsx)</label>
                        <input type="file" class="form-control" name="file_excel" id="file_excel" accept=".xlsx" required>
                    </div>

                    <div class="alert alert-secondary d-flex align-items-center justify-content-between py-2">
                        <div class="small">
                            <i class="fas fa-download me-2"></i> Belum punya template?
                        </div>
                        <a href="../assets/template_import_soal.xlsx" class="btn btn-sm btn-success">
                            Download Template Excel
                        </a>
                    </div>
                </div>
                
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-upload me-1"></i> Mulai Import
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="modalImportDocx" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="import_docx_preview.php" method="post" enctype="multipart/form-data">

            <input type="hidden" name="kode_soal" value="<?= $kode_soal; ?>">
            <input type="hidden" name="opsi" value="<?= $data_soal['jumlah_opsi']; ?>">

            <div class="modal-content shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" style="color:white;">
                        <i class="fas fa-file-word me-2"></i> Import Soal DOCX (Word)
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="card mb-3 border-primary">
                        <div class="card-header bg-primary-subtle py-2 fw-bold small">
                            <i class="fas fa-microchip me-1"></i> LOGIKA PENULISAN (PARSING ENGINE):
                        </div>
                        <div class="card-body py-2 px-3 small">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1 fw-bold text-primary">1. Format Dasar</p>
                                    <ul class="ps-3 mb-2">
                                        <li>Nomor: <code>1. </code> (Angka, Titik, Spasi)</li>
                                        <li>Opsi: <code>A. </code> sampai <code>E. </code></li>
                                        <li>Tipe: <code>Tipe: PG</code>, <code>PGX</code>, <code>BS</code>, atau <code>MJD</code></li>
                                        <li>Pemisah: Garis <code>-----</code> di akhir tiap soal.</li>
                                    </ul>
                                </div>
                                <div class="col-md-6 border-start">
                                    <p class="mb-1 fw-bold text-danger">2. Format Kunci & Jawaban</p>
                                    <ul class="ps-3 mb-0">
                                        <li><strong>PG/PGX:</strong> <code>Kunci: A</code> atau <code>Kunci: A,C</code></li>
                                        <li><strong>BS (Benar/Salah):</strong> Tulis pernyataan di opsi A, B, dst. Kunci: <code>Kunci: Benar|Salah|Benar</code></li>
                                        <li><strong>MJD (Menjodohkan):</strong> Kunci: <code>Kunci: Kiri:Kanan|Kiri:Kanan</code></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white py-4 px-4 border-top">

                                        <h6 class="fw-bold text-dark mb-2">Panduan & Catatan Teknis:</h6>
                                        <ul class="mb-0 text-muted small" style="line-height: 1.6;">
                                            <li>
                                                <strong class="text-dark">Tipe BS (Benar/Salah):</strong> 
                                                Maksimal pernyataan adalah <span class="badge bg-white text-dark border">5 baris</span>. 
                                                Pastikan jumlah baris di pertanyaan sama dengan jumlah status pada <code>Kunci:</code>.
                                            </li>
                                            <li class="mt-2">
                                                <strong class="text-dark">Konfigurasi Server:</strong> 
                                                Pastikan <code>post_max_size</code> di <span class="badge bg-white text-danger border">php.ini</span> sudah dinaikkan (misal: 64M). 
                                                Jika terlalu kecil, data JSON akan terpotong dan proses simpan akan <strong>gagal (null)</strong>.
                                            </li>
                                            <li class="mt-2">
                                                <strong class="text-dark">Input Gambar:</strong> 
                                                Insert → Picture → This Device... pada Microsoft Word.(jangan copy paste gambar)<br>
                                                Gambar akan otomatis di-upload dan diubah menjadi link HTML saat Anda menekan <b>"Proses Import"</b> di halaman preview nanti.
                                            </li>
                                        </ul>

                        </div>
                    </div>


                    <div class="mb-3">
                        <label class="form-label fw-bold">Pilih File (.docx)</label>
                        <input type="file" name="file_docx" class="form-control" 
                               accept=".docx,application/vnd.openxmlformats-officedocument.wordprocessingml.document" 
                               required>
                        <div class="form-text text-muted">Pastikan file tidak dalam keadaan terproteksi (Read-Only).</div>
                    </div>

                    <div class="alert alert-secondary d-flex align-items-center justify-content-between py-2 mb-0">
                        <div class="small">
                            <i class="fas fa-file-download me-1"></i> Rekomendasi: Gunakan template standar.
                        </div>
                        <a href="../assets/template_import_soal.docx" class="btn btn-sm btn-primary">
                            <i class="fas fa-download me-1"></i> Download Template Docx
                        </a>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> Preview & Validasi Data
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

            </main>
        </div>
    </div>
    <?php include '../inc/js.php'; ?>
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
document.addEventListener("DOMContentLoaded", function() {
    const formDocx = document.querySelector('#modalImportDocx form');

    if (formDocx) {
        formDocx.addEventListener('submit', function() {
            Swal.fire({
                title: 'Memproses DOCX...',
                html: 'Sedang membaca file...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        });
    }
});
</script>
    <script>
    function lihatSoal(kode, nomor){
        const modalEl = document.getElementById('modalSoal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

        // tampilkan loading dulu
        document.getElementById('isiModalSoal').innerHTML = 'Loading...';

        // ambil isi soal via AJAX
        fetch('modal_lihat_soal.php?kode_soal='+kode+'&nomor='+nomor)
            .then(res => res.text())
            .then(html => {
                document.getElementById('isiModalSoal').innerHTML = html;
            });

        modal.show();
    }
    </script>
<script>
    $(document).ready(function() {
        $('#butirsoal').DataTable({
            "paging": true,
            "searching": true,
            order: [
                [0, 'asc']
            ],
            "info": true,
            "lengthChange": true,
            "autoWidth": false,
        });
    });


    window.onload = function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success') && urlParams.get('success') === '1') {
            Swal.fire({
                icon: 'success',
                title: 'Data berhasil diperbarui!',
                showConfirmButton: true,
            });

            urlParams.delete('success');
            window.history.replaceState(null, null, window.location.pathname + '?' + urlParams.toString());
        }
    }

$(document).on('click', '.btn-hapus', function () {
    const id = $(this).data('id');

    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data soal akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'hapus_butir_soal.php?id_soal=' + id;
        }
    });
});
</script>

    <script>
document.addEventListener("DOMContentLoaded", function() {
    const formImport = document.querySelector('#modalImportExcel form');

    if (formImport) {
        formImport.addEventListener('submit', function(e) {
            Swal.fire({
                title: 'Mengimpor...',
                html: 'Harap tunggu, sistem sedang memproses file.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        });
    }
});
</script>
    <?php
if (isset($_SESSION['import_result'])) {
    $res = $_SESSION['import_result'];
    unset($_SESSION['import_result']);

    $successCount = $res['successCount'];
    $failCount = $res['failCount'];
    $duplicates = $res['duplicates'];

    $pesan = "Import selesai!<br>Berhasil: $successCount<br>Duplikat: $failCount";
    if ($failCount > 0) {
        $pesan .= "<br>Soal duplikat:<br>" . implode(", ", $duplicates);
    }
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'info',
            title: 'Hasil Import',
            html: `<?= $pesan ?>`,
            confirmButtonText: 'OK'
        });
    });
    </script>
    <?php
}

if (isset($_SESSION['import_error'])) {
    $error = $_SESSION['import_error'];
    unset($_SESSION['import_error']);
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: 'Gagal Import',
            text: '<?= addslashes($error) ?>',
            confirmButtonText: 'OK'
        });
    });
    </script>
    <?php
}
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Alert untuk Error (Kode Tidak Sesuai)
    <?php if (isset($_SESSION['alert_error'])): ?>
    Swal.fire({
        icon: 'error',
        title: 'Gagal Import',
        text: '<?= addslashes($_SESSION['alert_error']) ?>',
        confirmButtonColor: '#d33'
    });
    <?php unset($_SESSION['alert_error']); endif; ?>

    // Alert untuk Sukses (Berhasil Simpan)
    <?php if (isset($_SESSION['alert_success'])): ?>
    Swal.fire({
        icon: 'success',
        title: 'Sukses',
        text: '<?= addslashes($_SESSION['alert_success']) ?>',
        confirmButtonColor: '#3085d6'
    });
    <?php unset($_SESSION['alert_success']); endif; ?>
});
</script>
</body>

</html>