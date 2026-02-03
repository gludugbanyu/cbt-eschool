<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
only_admin();

include '../inc/dataadmin.php';

$success = '';
$error = '';

// PROSES RESTORE
if (isset($_POST['restore'])) {
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== 0) {
        $error = 'Upload file gagal atau tidak ada file.';
    } else {
        $maxFileSize = 20 * 1024 * 1024; // 20 MB
        if ($_FILES['file']['size'] > $maxFileSize) {
            $error = 'Ukuran file terlalu besar. Maksimal 20 MB.';
        } elseif (pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION) !== 'gdbk') {
            $error = 'File harus berekstensi .gdbk';
        } else {
            $encrypted = file_get_contents($_FILES['file']['tmp_name']);

            // Fungsi dekripsi
            function decrypt_data($data, $key) {
                $data = base64_decode($data);
                $iv = substr($data, 0, 16);
                $encrypted = substr($data, 16);
                return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
            }

            $zipData = decrypt_data($encrypted, $key);
            if ($zipData === false) {
                $error = 'Gagal dekripsi file. Pastikan kunci benar dan file valid.';
            } else {
                $tmpZip = tempnam(sys_get_temp_dir(), 'rest_gambar_');
                file_put_contents($tmpZip, $zipData);

                $zip = new ZipArchive();
                if ($zip->open($tmpZip) === TRUE) {
                    $extractPath = realpath('../gambar');
                    if (!$extractPath) {
                        $error = 'Folder ../gambar tidak ditemukan.';
                    } else {
                        $zip->extractTo($extractPath);
                        $zip->close();
                        unlink($tmpZip);
                        $success = 'Restore folder gambar berhasil.';
                    }
                } else {
                    unlink($tmpZip);
                    $error = 'Gagal membuka file zip hasil dekripsi.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Backup & Restore Folder Gambar</title>
<?php include '../inc/css.php'; ?>
<script src="../assets/js/sweetalert.js"></script>
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
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Backup & Restore Folder Gambar</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-secondary">
                                    <strong>Catatan:</strong>  
                                    Jika file di folder <code>/gambar/</code> sudah terlalu banyak dan besar,  
                                    disarankan melakukan backup dan restore folder <code>/gambar/</code> secara manual melalui FTP atau file manager hosting Anda.  
                                    Backup dan restore otomatis cocok untuk folder yang ukurannya tidak terlalu besar agar tidak memberatkan server.
                                </div>
                                <?php if ($success): ?>
                                    <script>
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Sukses',
                                        text: '<?= addslashes($success) ?>',
                                        confirmButtonText: 'OK'
                                    });
                                    </script>
                                <?php elseif ($error): ?>
                                    <script>
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: '<?= addslashes($error) ?>',
                                        confirmButtonText: 'Coba Lagi'
                                    });
                                    </script>
                                <?php endif; ?>

                                <!-- FORM BACKUP -->
                                <form method="post" action="backup_gbr_download.php" id="backupForm">
                                    <button type="submit" name="backup" class="btn btn-primary mb-3">
                                        <i class="fa fa-download"></i> Backup Folder Gambar
                                    </button>
                                </form>

                                <!-- FORM RESTORE -->
                                <form method="post" enctype="multipart/form-data" id="restoreForm">
                                    <input type="hidden" name="MAX_FILE_SIZE" value="20971520" />
                                    <div class="mb-3">
                                        <label for="file" class="form-label">Upload File Backup Folder Gambar (.gdbk)</label>
                                        <input type="file" name="file" id="file" accept=".gdbk" required class="form-control" />
                                    </div>
                                    <button type="submit" name="restore" class="btn btn-success">
                                        <i class="fa fa-upload"></i> Restore Folder Gambar
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
document.getElementById('backupForm').addEventListener('submit', function(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Yakin ingin melakukan backup folder gambar?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, backup!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            e.target.submit();
        }
    });
});

document.getElementById('restoreForm').addEventListener('submit', function(e) {
    e.preventDefault();
    Swal.fire({
        title: 'Yakin ingin merestore folder gambar? Data lama akan tertimpa!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, restore!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            e.target.submit();
        }
    });
});
</script>
</body>
</html>
