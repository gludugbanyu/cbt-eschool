<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
only_admin();

if(isset($_POST['simpan'])){
$nama=$_POST['nama'];
$user=$_POST['username'];
$pass=$_POST['password'];
$role=$_POST['role'];

// CEK USERNAME
$cek=mysqli_query($koneksi,"SELECT id FROM admins WHERE username='$user'");
if(mysqli_num_rows($cek)>0){
    $_SESSION['swal']=['icon'=>'error','title'=>'Gagal!','text'=>'Username sudah digunakan!'];
    header("Location: manajemen_user.php"); exit;
}

// VALIDASI PASSWORD KUAT
if(!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[\W_]).{8,}$/',$pass)){
    $_SESSION['swal']=['icon'=>'error','title'=>'Password Lemah!','text'=>'Minimal 8 karakter, huruf, angka & simbol.'];
    header("Location: manajemen_user.php"); exit;
}

$hash=password_hash($pass,PASSWORD_DEFAULT);

mysqli_query($koneksi,"INSERT INTO admins (username,nama_admin,password,role)
VALUES ('$user','$nama','$hash','$role')");

$_SESSION['swal']=['icon'=>'success','title'=>'Berhasil!','text'=>'User berhasil ditambahkan.'];
header("Location: manajemen_user.php"); exit;
}

include '../inc/dataadmin.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah User</title>
<?php include '../inc/css.php'; ?>
</head>
<body>
<div class="wrapper">
<?php include 'sidebar.php'; ?>
<div class="main">
<?php include 'navbar.php'; ?>

<main class="content">
<div class="container-fluid p-0">
<div class="card">
<div class="card-header"><h5>Tambah User</h5></div>
<div class="card-body">

<form method="POST">
<div class="mb-3">
<label>Nama</label>
<input name="nama" class="form-control" required>
</div>

<div class="mb-3">
<label>Username</label>
<input name="username" class="form-control" required>
</div>

<div class="mb-3">
<label>Password</label>
<input type="password" name="password" class="form-control" required>
</div>

<div class="mb-3">
<label>Role</label>
<select name="role" class="form-control">
<option value="admin">Admin</option>
<option value="editor">Editor</option>
</select>
</div>

<button name="simpan" class="btn btn-primary">Simpan</button>
<a href="manajemen_user.php" class="btn btn-secondary">Kembali</a>
</form>

</div>
</div>
</div>
</main>
</div>
</div>
</body>
</html>
