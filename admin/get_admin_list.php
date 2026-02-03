<?php
include '../koneksi/koneksi.php';

$current = $_GET['current'] ?? '';
$current_ids = explode(',', $current);

$q = mysqli_query($koneksi,"SELECT id,nama_admin FROM admins ORDER BY nama_admin");

while($d=mysqli_fetch_assoc($q)){
    $checked = in_array($d['id'], $current_ids) ? 'checked' : '';

    echo "
    <div style='text-align:left'>
        <label>
            <input type='checkbox' class='chk-admin' value='{$d['id']}' $checked>
            {$d['nama_admin']}
        </label>
    </div>";
}
