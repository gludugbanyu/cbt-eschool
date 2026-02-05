<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
only_admin();

$id=$_GET['id'];
$d=mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT * FROM admins WHERE id='$id'"));

if(isset($_POST['update'])){
    // CEK JANGAN SAMPAI ADMIN TERAKHIR JADI EDITOR
$role_lama = $d['role'];
$role_baru = $_POST['role'];

if ($role_lama == 'admin' && $role_baru != 'admin') {

    $cekAdmin = mysqli_query($koneksi, "
        SELECT COUNT(*) as total 
        FROM admins 
        WHERE role='admin' AND id!='$id'
    ");

    $totalAdminLain = mysqli_fetch_assoc($cekAdmin)['total'];

    if ($totalAdminLain <= 0) {
        $_SESSION['swal'] = [
            'icon' => 'error',
            'title' => 'Ditolak!',
            'text' => 'Minimal harus ada 1 admin di sistem.'
        ];
        header("Location: manajemen_user.php");
        exit;
    }
}

$nama=$_POST['nama'];
$user=$_POST['username'];
$role=$_POST['role'];

// CEK DUPLIKAT USERNAME
$cek=mysqli_query($koneksi,"SELECT id FROM admins WHERE username='$user' AND id!='$id'");
if(mysqli_num_rows($cek)>0){
    $_SESSION['swal']=['icon'=>'error','title'=>'Gagal!','text'=>'Username sudah digunakan!'];
    header("Location: manajemen_user.php"); exit;
}

if(!empty($_POST['password'])){
    $newpass=$_POST['password'];

    if(!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[\W_]).{8,}$/',$newpass)){
        $_SESSION['swal']=['icon'=>'error','title'=>'Password Lemah!','text'=>'Minimal 8 karakter, huruf, angka & simbol.'];
        header("Location: manajemen_user.php"); exit;
    }

    $hash=password_hash($newpass,PASSWORD_DEFAULT);
    mysqli_query($koneksi,"UPDATE admins SET
    username='$user',
    nama_admin='$nama',
    role='$role',
    password='$hash'
    WHERE id='$id'");
}else{
    mysqli_query($koneksi,"UPDATE admins SET
    username='$user',
    nama_admin='$nama',
    role='$role'
    WHERE id='$id'");
}

$_SESSION['swal']=['icon'=>'success','title'=>'Berhasil!','text'=>'User berhasil diupdate.'];
header("Location: manajemen_user.php"); exit;
}

include '../inc/dataadmin.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit User</title>
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
<div class="card-header"><h5>Edit User</h5></div>
<div class="card-body">

<form method="POST">
<div class="mb-3">
<label>Nama</label>
<input name="nama" value="<?=$d['nama_admin']?>" class="form-control" required>
</div>

<div class="mb-3">
<label>Username</label>
<input name="username" value="<?=$d['username']?>" class="form-control" required>
</div>

<div class="mb-3">
<label>Password Baru (opsional)</label>
<input type="password" name="password" class="form-control">
</div>

<div class="mb-3">
<label>Role</label>
<select name="role" class="form-control">
<option value="admin" <?=$d['role']=='admin'?'selected':''?>>Admin</option>
<option value="editor" <?=$d['role']=='editor'?'selected':''?>>Editor</option>
</select>
</div>

<button name="update" class="btn btn-primary">Update</button>
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
