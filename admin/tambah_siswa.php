<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
include '../inc/dataadmin.php';
check_login('admin');
only_admin();

if (isset($_POST['submit'])) {
    $nama = htmlspecialchars($_POST['nama_siswa']);
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];
    $kelas = htmlspecialchars($_POST['kelas']);
    $rombel = htmlspecialchars($_POST['rombel']);

    // Cek apakah username sudah ada
    $cek = mysqli_query($koneksi, "SELECT * FROM siswa WHERE username = '$username'");
    if (mysqli_num_rows($cek) > 0) {
        $_SESSION['error'] = 'Username sudah digunakan.';
        header('Location: siswa.php');
        exit;
    }

    // Enkripsi password
    include '../inc/encrypt.php';
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
    $encrypted = openssl_encrypt($password, $method, $rahasia, 0, $iv);
    $final = base64_encode($iv . $encrypted);

    // Simpan ke database
    $query = "INSERT INTO siswa (nama_siswa, username, password, kelas, rombel)
              VALUES ('$nama', '$username', '$final', '$kelas', '$rombel')";
    mysqli_query($koneksi, $query);

    $_SESSION['success'] = 'Berhasil menambahkan siswa.';
        header('Location: siswa.php');
        exit;
}
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
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Tambah Siswa</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label class="form-label">Nama Siswa</label>
                                            <input type="text" name="nama_siswa" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Username</label>
                                            <input type="text" name="username" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Password</label>
                                            <input type="text" name="password" class="form-control" required>
                                        </div>
                                        <div class="row">
                                            <div class="mb-3 col-sm-6">
                                                <label class="form-label">Kelas</label>
                                                <input type="text" name="kelas" class="form-control" required>
                                            </div>
                                            <div class="mb-3 col-sm-6">
                                                <label class="form-label">Rombel</label>
                                                <input type="text" name="rombel" class="form-control" required>
                                            </div>
                                        </div>
                                        <button type="submit" name="submit" class="btn btn-success">
                                            <i class="fas fa-save"></i> Simpan
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
