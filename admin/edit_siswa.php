<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
include '../inc/dataadmin.php';

$id = $_GET['id'];
$data = mysqli_query($koneksi, "SELECT * FROM siswa WHERE id_siswa = '$id'");
$siswa = mysqli_fetch_assoc($data);

if (!$siswa) {
    echo "
    <script src='../assets/js/sweetalert.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Error!',
                text: 'Data siswa tidak ditemukan.',
                icon: 'error',
                confirmButtonText: 'Kembali'
            }).then(() => {
                window.location.href = 'siswa.php';
            });
        });
    </script>";
    exit;
}

if (isset($_POST['submit'])) {
    $nama = htmlspecialchars($_POST['nama_siswa']);
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password']; // Tidak disanitasi karena akan dienkripsi
    $kelas = htmlspecialchars($_POST['kelas']);
    $rombel = htmlspecialchars($_POST['rombel']);

    // Cek jika username berubah dan sudah dipakai user lain
    if ($username != $siswa['username']) {
        $cek = mysqli_query($koneksi, "SELECT * FROM siswa WHERE username = '$username'");
        if (mysqli_num_rows($cek) > 0) {
            echo "
            <script src='../assets/js/sweetalert.js'></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        title: 'Gagal!',
                        text: 'Username sudah digunakan!',
                        icon: 'error',
                        confirmButtonText: 'Kembali'
                    }).then(() => {
                        window.location.href = 'edit_siswa.php?id=$id';
                    });
                });
            </script>";
            exit;
        }
    }

    // Enkripsi password jika diisi, jika tidak tetap pakai password lama
    if (!empty($password)) {
        include '../inc/encrypt.php';
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
        $encrypted = openssl_encrypt($password, $method, $rahasia, 0, $iv);
        $final = base64_encode($iv . $encrypted);
    } else {
        $final = $siswa['password']; // Tetap gunakan password lama
    }

    // Update data siswa
    $update = "UPDATE siswa SET 
        nama_siswa = '$nama',
        username = '$username',
        password = '$final',
        kelas = '$kelas',
        rombel = '$rombel'
        WHERE id_siswa = '$id'";

    mysqli_query($koneksi, $update);

    echo "
    <script src='../assets/js/sweetalert.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Data siswa berhasil diupdate!',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'siswa.php';
            });
        });
    </script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Siswa</title>
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
                        <div class="col-12">
                            <div class="card">
                            <div class="card-header">
                                    <h5 class="card-title mb-0">Edit Siswa</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label class="form-label">Nama Siswa</label>
                                            <input type="text" name="nama_siswa" class="form-control" value="<?= $siswa['nama_siswa'] ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Username</label>
                                            <input type="text" name="username" class="form-control" value="<?= $siswa['username'] ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Password <small>(Kosongkan jika tidak diubah)</small></label>
                                            <input type="text" name="password" class="form-control">
                                        </div>
                                        <div class="row">
                                            <div class="mb-3 col-sm-6">
                                                <label class="form-label">Kelas</label>
                                                <input type="text" name="kelas" class="form-control" value="<?= $siswa['kelas'] ?>" required>
                                            </div>
                                            <div class="mb-3 col-sm-6">
                                                <label class="form-label">Rombel</label>
                                                <input type="text" name="rombel" class="form-control" value="<?= $siswa['rombel'] ?>" required>
                                            </div>
                                        </div>
                                        <button type="submit" name="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Update
                                        </button>
                                        <a href="siswa.php" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left"></i> Kembali
                                        </a>
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
</body>
</html>