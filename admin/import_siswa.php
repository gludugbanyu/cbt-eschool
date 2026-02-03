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
    <title>Tambah Siswa</title>
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
                        <div class="col-12 col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Import Data Siswa</h5>
                                </div>
                                <div class="card-body">
                                    <!-- Progress bar -->
                                    <div id="progressContainer" style="display:none; margin-top:10px;">
                                        <progress id="progressBar" value="0" max="100" style="width:100%;"></progress>
                                        <span id="status" class="d-block mt-1 text-muted"></span>
                                    </div>
                                    
                                    <form id="uploadForm" method="POST" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label for="file" class="form-label">Upload File Excel (.xlsx):</label>
                                            <input type="file" name="file" id="file" class="form-control" accept=".xls,.xlsx" required>
                                        </div>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-upload"></i> Upload
                                        </button>
                                        <a href="../assets/format_import_siswa.xlsx" class="btn btn-info" download>
                                            <i class="fas fa-file-excel"></i> Download Format Excel
                                        </a>
                                        <a href="siswa.php" class="btn btn-secondary">Kembali</a>
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
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        e.preventDefault();

        var file = document.getElementById('file').files[0];
        if (!file) return;

        var formData = new FormData();
        formData.append('file', file);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'proses_import_siswa.php', true);

        // Tampilkan progress bar
        document.getElementById('progressContainer').style.display = 'block';

        // Update progress bar
        xhr.upload.onprogress = function(e) {
            if (e.lengthComputable) {
                var percent = Math.round((e.loaded / e.total) * 100);
                document.getElementById('progressBar').value = percent;
                document.getElementById('status').innerText = percent + '% diunggah';
            }
        };

        // Respon dari server
        xhr.onload = function () {
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);

                    let icon = (response.status === 'success') ? 'info' : 'error';

                    Swal.fire({
                        title: 'Import Selesai',
                        html: `
                            <strong>Berhasil:</strong> ${response.berhasil}<br>
                            <strong>Gagal:</strong> ${response.gagal}<br>
                            <strong>Duplikat:</strong> ${response.duplikat}
                        `,
                        icon: icon,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = 'siswa.php';
                    });

                } catch (e) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Respon Tidak Valid',
                        text: 'Gagal membaca respon dari server.'
                    });
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Upload Gagal',
                    text: 'Terjadi kesalahan pada server.'
                });
            }
        };

        xhr.onerror = function () {
            Swal.fire({
                icon: 'error',
                title: 'Upload Error',
                text: 'Tidak dapat terhubung ke server.'
            });
        };

        xhr.send(formData);
    });
    </script>
</body>
</html>
