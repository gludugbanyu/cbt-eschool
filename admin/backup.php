<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
only_admin();

include '../inc/dataadmin.php';

$success = '';
$error = '';
global $key;
function decrypt_data($data, $key) {
    $data = base64_decode($data);
    $iv = substr($data, 0, 16);
    $encrypted = substr($data, 16);
    return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
}

if (isset($_POST['import'])) {
    $maxFileSize = 10 * 1024 * 1024; // 10 MB

    if ($_FILES['file']['error'] === 0) {
        if ($_FILES['file']['size'] > $maxFileSize) {
            $error = "Ukuran file terlalu besar. Maksimal 10 MB.";
        } elseif (pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION) === 'dbk') {
            $encrypted = file_get_contents($_FILES['file']['tmp_name']);
            $sql = decrypt_data($encrypted, $key);

            if ($sql === false) {
                $error = "Gagal dekripsi file. Pastikan kunci benar dan file valid.";
            } else {
                mysqli_begin_transaction($koneksi);
                if (mysqli_multi_query($koneksi, $sql)) {
                    do {
                        if ($result = mysqli_store_result($koneksi)) {
                            mysqli_free_result($result);
                        }
                    } while (mysqli_more_results($koneksi) && mysqli_next_result($koneksi));
                    mysqli_commit($koneksi);
                    $success = 'Database berhasil di-import dari file backup.';
                } else {
                    mysqli_rollback($koneksi);
                    $error = "Gagal menjalankan query restore: " . mysqli_error($koneksi);
                }
            }
        } else {
            $error = 'File tidak valid. Harus file berekstensi .dbk dan tidak rusak.';
        }
    } else {
        $error = 'Terjadi kesalahan upload file.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Backup Restore</title>
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
                                <h5 class="card-title mb-0">Backup Restore</h5>
                            </div>
                            <div class="card-body">

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

                                <!-- Tombol Backup -->
                                <button id="backupBtn" class="btn btn-primary mb-3">
                                    <i class="fa fa-download"></i> Backup Database
                                </button>

                                <!-- Form Import -->
                                <form method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
                                    <div class="mb-3">
                                        <label for="file" class="form-label">Upload File Backup (.dbk)</label>
                                        <input type="file" name="file" id="file" accept=".dbk" required class="form-control" />
                                    </div>
                                    <button type="submit" name="import" class="btn btn-success">
                                        <i class="fa fa-upload"></i> Import Database
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
document.getElementById('backupBtn').addEventListener('click', function () {
    Swal.fire({
        title: 'Yakin ingin melakukan backup database?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, backup!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirect ke file backup_download.php untuk proses backup dan download
            window.location.href = 'backup_download.php';
        }
    });
});
</script>

</body>
</html>
