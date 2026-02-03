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
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Manajemen User</title>
<?php include '../inc/css.php'; ?>
<style>
.table-wrapper{overflow-x:auto!important;}
table th,table td{text-align:left!important;}
</style>
</head>
<body>
<div class="wrapper">
<?php include 'sidebar.php'; ?>
<div class="main">
<?php include 'navbar.php'; ?>

<main class="content">
<div class="container-fluid p-0">
<div class="card">
<div class="card-header">
<h5 class="card-title mb-0">Daftar User Admin</h5>
</div>

<div class="card-body">

<a href="tambah_user.php" class="btn btn-primary mb-3">
<i class="fas fa-plus"></i> Tambah User
</a>

<div class="table-wrapper">
<table id="userTable" class="table table-striped nowrap">
<thead>
<tr>
<th style="display:none;">ID</th>
<th>No</th>
<th>Nama</th>
<th>Username</th>
<th>Role</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>
<?php
$no=1;
$q=mysqli_query($koneksi,"
SELECT * FROM admins 
ORDER BY id DESC
");
while($d=mysqli_fetch_assoc($q)){
?>
<tr>
    <td style="display:none;"><?= $d['id']; ?></td>
    <td><?= $no; ?></td>
    <td><?= $d['nama_admin']; ?></td>
    <td><?= $d['username']; ?></td>
    <td><span class="badge bg-info"><?= $d['role']; ?></span></td>
    <td>
        <a href="edit_user.php?id=<?= $d['id']; ?>" class="btn btn-success btn-sm">
            <i class="fas fa-edit"></i> Edit
        </a>

        <?php if($d['id'] != ($_SESSION['admin_id'] ?? 0)): ?>
            <form method="POST" action="hapus_user.php" class="d-inline delete-form">
                <input type="hidden" name="id" value="<?= $d['id']; ?>">
                <button class="btn btn-danger btn-sm">
                    <i class="fa fa-close"></i> Hapus
                </button>
            </form>
        <?php else: ?>
            <button class="btn btn-secondary btn-sm" disabled>
                <i class="fa fa-lock"></i> Hapus
            </button>
        <?php endif; ?>

    </td>
</tr>
<?php
$no++;
}

?>
</tbody>
</table>
</div>

</div>
</div>
</div>
</main>
</div>
</div>

<?php include '../inc/js.php'; ?>

<!-- Swal pesan dari tambah/edit/hapus -->
<?php if(isset($_SESSION['swal'])): ?>
<script>
Swal.fire({
    icon: '<?= $_SESSION['swal']['icon']; ?>',
    title: '<?= $_SESSION['swal']['title']; ?>',
    text: '<?= $_SESSION['swal']['text']; ?>',
    confirmButtonColor: '#3085d6'
});
</script>
<?php unset($_SESSION['swal']); endif; ?>

<script>
$('#userTable').DataTable({
order:[[0,'desc']],
columnDefs:[{targets:0,visible:false}]
});

document.querySelectorAll('.delete-form').forEach(f=>{
f.addEventListener('submit',e=>{
e.preventDefault();
Swal.fire({
title:'Yakin hapus user?',
text:'User yang dihapus tidak bisa dikembalikan!',
icon:'warning',
showCancelButton:true,
confirmButtonColor:'#d33',
cancelButtonColor:'#6c757d'
}).then(r=>{
if(r.isConfirmed) f.submit();
});
});
});
</script>

</body>
</html>
