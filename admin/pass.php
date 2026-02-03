<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
include '../inc/dataadmin.php';

$id_admin = $_SESSION['admin_id'] ?? null;

if (!$id_admin) {
    $_SESSION['error'] = 'Session tidak valid.';
    header('Location: login.php');
    exit;
}

$stmt = $koneksi->prepare("SELECT * FROM admins WHERE id = ?");
$stmt->bind_param("i", $id_admin);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_admin = trim($_POST['nama_admin'] ?? '');
    $password_lama = $_POST['password_lama'] ?? '';
    $password_baru = $_POST['password_baru'] ?? '';
    $konfirmasi = $_POST['konfirmasi_password'] ?? '';

    // Validasi nama_admin
    if (empty($nama_admin)) {
        $_SESSION['error'] = 'Nama admin tidak boleh kosong.';
        header('Location: pass.php');
        exit;
    }

    $berhasil = false;

    // Update nama jika berbeda
    if ($nama_admin !== $admin['nama_admin']) {
        $updateNama = $koneksi->prepare("UPDATE admins SET nama_admin = ? WHERE id = ?");
        $updateNama->bind_param("si", $nama_admin, $id_admin);
        if ($updateNama->execute()) {
            $_SESSION['success'] = 'Nama berhasil diperbarui.';
            $berhasil = true;
        } else {
            $_SESSION['error'] = 'Gagal memperbarui nama.';
            header('Location: pass.php');
            exit;
        }
    }

    // Jika password lama diisi, lakukan validasi dan update password
    if (!empty($password_lama) || !empty($password_baru) || !empty($konfirmasi)) {
        if (empty($password_lama) || empty($password_baru) || empty($konfirmasi)) {
            $_SESSION['error'] = 'Semua kolom password harus diisi.';
            header('Location: pass.php');
            exit;
        }

        if (!password_verify($password_lama, $admin['password'])) {
            $_SESSION['error'] = 'Password lama salah.';
            header('Location: pass.php');
            exit;
        }

        if ($password_baru !== $konfirmasi) {
            $_SESSION['error'] = 'Konfirmasi password tidak cocok.';
            header('Location: pass.php');
            exit;
        }

        $password_baru_hash = password_hash($password_baru, PASSWORD_DEFAULT);
        $update = $koneksi->prepare("UPDATE admins SET password = ? WHERE id = ?");
        $update->bind_param("si", $password_baru_hash, $id_admin);

        if ($update->execute()) {
            $_SESSION['success'] = $berhasil ? 'Nama dan Password berhasil diperbarui.' : 'Password berhasil diperbarui.';
        } else {
            $_SESSION['error'] = 'Gagal memperbarui password.';
        }
    }

    header('Location: pass.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profil</title>
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
                        <div class="col-12 col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Ubah Profil</h5>
                                </div>
                                <div class="card-body">
                                    <form method="post">
                                        <div class="mb-3">
                                            <label>Username</label>
                                            <input type="text" class="form-control bg-light" 
                                                value="<?= htmlspecialchars($admin['username']) ?>" 
                                                readonly style="cursor:not-allowed;">
                                            <small class="text-muted">Username tidak dapat diubah.</small>
                                        </div>
                                        <hr>
                                        <div class="mb-3">
                                            <label>Nama</label>
                                            <input type="text" name="nama_admin" class="form-control" value="<?= htmlspecialchars($admin['nama_admin']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label>Password Lama</label>
                                            <input type="password" name="password_lama" class="form-control">
                                        </div>
                                        <div class="mb-3">
                                            <label>Password Baru</label>
                                            <input type="password" name="password_baru" class="form-control">
                                        </div>
                                        <div class="mb-3">
                                            <label>Konfirmasi Password Baru</label>
                                            <input type="password" name="konfirmasi_password" class="form-control">
                                        </div>
                                        <p class="text-muted"><small>Biarkan kolom password kosong jika tidak ingin mengubahnya.</small></p>
                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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
    <?php if (isset($_SESSION['success'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '<?= $_SESSION['success']; ?>',
            confirmButtonColor: '#28a745'
        });
    <?php unset($_SESSION['success']); endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '<?= $_SESSION['error']; ?>',
            confirmButtonColor: '#dc3545'
        });
    <?php unset($_SESSION['error']); endif; ?>
    </script>
</body>
</html>
