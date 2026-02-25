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

// fungsi decrypt backup
function decrypt_data($data, $key) {
    $data = base64_decode($data);
    $iv = substr($data, 0, 16);
    $encrypted = substr($data, 16);
    return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
}

if (isset($_POST['import'])) {

    $maxFileSize = 10 * 1024 * 1024; // 10MB

    if ($_FILES['file']['error'] === 0) {

        if ($_FILES['file']['size'] > $maxFileSize) {
            $error = "Ukuran file terlalu besar. Maksimal 10MB.";
        }

        elseif (pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION) === 'dbk') {

            $encrypted = file_get_contents($_FILES['file']['tmp_name']);
            $sql = decrypt_data($encrypted, $key);

            if (!$sql) {
                $error = "Gagal decrypt file backup.";
            }

            else {

                mysqli_begin_transaction($koneksi);

                try {

                    mysqli_query($koneksi,"SET FOREIGN_KEY_CHECKS=0");
                    mysqli_query($koneksi,"SET UNIQUE_CHECKS=0");

                    $queries = explode(";\n", $sql);

                    foreach ($queries as $query) {

                        $query = trim($query);

                        if (!empty($query)) {

                            // MODE RESTORE AMAN
                            if (stripos($query, 'INSERT INTO') === 0) {
    $query = preg_replace('/^INSERT INTO/i', 'INSERT IGNORE INTO', $query);
}

                            if (!mysqli_query($koneksi, $query)) {
                                throw new Exception(mysqli_error($koneksi));
                            }

                        }
                    }

                    mysqli_query($koneksi,"SET FOREIGN_KEY_CHECKS=1");
                    mysqli_query($koneksi,"SET UNIQUE_CHECKS=1");

                    mysqli_commit($koneksi);
                    $success = "Restore Database Berhasil";

                } catch (Exception $e) {

                    mysqli_rollback($koneksi);
                    $error = "Restore gagal: " . $e->getMessage();
                }
            }

        } else {
            $error = "File harus berekstensi .dbk";
        }

    } else {
        $error = "Upload file error.";
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
<div class="card-header">
<h5 class="card-title mb-0">Backup Restore</h5>
</div>

<div class="card-body">

<?php if ($success): ?>
<script>
Swal.fire({
icon: 'success',
title: 'Sukses',
text: '<?= addslashes($success) ?>'
});
</script>
<?php endif; ?>

<?php if ($error): ?>
<script>
Swal.fire({
icon: 'error',
title: 'Gagal',
text: '<?= addslashes($error) ?>'
});
</script>
<?php endif; ?>

<button id="backupBtn" class="btn btn-primary mb-3">
<i class="fa fa-download"></i> Backup Database
</button>

<form method="post" enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
<div class="mb-3">
<label class="form-label">Upload File Backup (.dbk)</label>
<input type="file" name="file" accept=".dbk" required class="form-control" />
</div>

<button type="submit" name="import" class="btn btn-success">
<i class="fa fa-upload"></i> Restore Database
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
title: 'Yakin ingin backup?',
icon: 'warning',
showCancelButton: true,
confirmButtonText: 'Ya!',
cancelButtonText: 'Batal'
}).then((result) => {
if (result.isConfirmed) {
window.location.href = 'backup_download.php';
}
});
});
</script>

</body>
</html>