<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
only_admin();
include '../inc/dataadmin.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Database</title>
    <?php include '../inc/css.php'; ?>
</head>

<body>
    <div class="wrapper">
        <?php include 'sidebar.php'; ?>
        <div class="main">
            <?php include 'navbar.php'; ?>

            <main class="content">
                <div class="container-fluid p-0">
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Reset Database</h5>
                                </div>
                                <div class="card-body">
                                <form id="resetForm" class="mt-4">
                                    <div class="mb-4">
                                        <label class="form-label fw-bold fs-5 mb-3">🗂️ Pilih Data yang Ingin Direset:</label>

                                        <div class="p-3 mb-3 border rounded bg-light d-flex align-items-start gap-2">
                                            <i class="fa fa-chalkboard-teacher text-primary fs-3"></i>
                                            <div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="reset[]" value="siswa" id="resetSiswa">
                                                    <label class="form-check-label fw-semibold" for="resetSiswa">
                                                        Reset <strong>Siswa</strong>
                                                    </label>
                                                </div>
                                                <small class="text-muted">Menghapus seluruh data siswa dari database.</small>
                                            </div>
                                        </div>

                                        <div class="p-3 mb-3 border rounded bg-light d-flex align-items-start gap-2">
                                            <i class="fas fa fa-person-chalkboard text-danger fs-3"></i>
                                            <div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="reset[]" value="soal" id="resetSoal">
                                                    <label class="form-check-label fw-semibold" for="resetSoal">
                                                        Reset <strong>Soal</strong>
                                                    </label>
                                                </div>
                                                <small class="text-muted">Menghapus soal, jawaban siswa, butir soal, dan file gambar (kecuali .htaccess).</small>
                                            </div>
                                        </div>

                                        <div class="p-3 mb-3 border rounded bg-light d-flex align-items-start gap-2">
                                            <i class="fas fa-chalkboard text-success fs-3"></i>
                                            <div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="reset[]" value="nilai" id="resetNilai">
                                                    <label class="form-check-label fw-semibold" for="resetNilai">
                                                        Reset <strong>Nilai</strong>
                                                    </label>
                                                </div>
                                                <small class="text-muted">Menghapus semua nilai ujian siswa.</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-warning mt-4">
                                        <strong><i class="bi bi-exclamation-triangle-fill"></i> Perhatian:</strong> Tindakan ini akan <u>menghapus data secara permanen</u>. Pastikan Anda sudah melakukan backup jika diperlukan.
                                    </div>

                                    <button type="button" class="btn btn-lg btn-danger" id="btnReset">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Reset Sekarang
                                    </button>
                                </form>


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
document.getElementById("btnReset").addEventListener("click", function () {
    const form = document.getElementById("resetForm");
    const formData = new FormData(form);
    const btn = document.getElementById("btnReset");

    if (!formData.getAll('reset[]').length) {
        Swal.fire('Oops!', 'Pilih setidaknya satu data yang ingin di-reset.', 'warning');
        return;
    }

    Swal.fire({
        title: 'Konfirmasi Reset',
        input: 'text',
        inputLabel: 'Ketik CONFIRM untuk melanjutkan',
        inputPlaceholder: 'CONFIRM',
        inputAttributes: {
            autocapitalize: 'off'
        },
        showCancelButton: true,
        confirmButtonText: 'Lanjutkan Reset',
        showLoaderOnConfirm: true,
        preConfirm: (inputValue) => {
            if (inputValue !== 'CONFIRM') {
                Swal.showValidationMessage('Anda harus mengetik "CONFIRM" untuk melanjutkan');
            }
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            // Ganti tampilan tombol menjadi loading
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';
            btn.disabled = true;

            fetch('reset_proses.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                Swal.fire('Sukses!', data.message, 'success').then(() => {
                    location.reload();
                });
            })
            .catch(error => {
                Swal.fire('Error', 'Terjadi kesalahan saat mereset.', 'error');
            })
            .finally(() => {
                btn.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> Reset Sekarang';
                btn.disabled = false;
            });
        }
    });
});
</script>

</body>
</html>
