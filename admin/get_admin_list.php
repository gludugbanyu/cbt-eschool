<?php
include '../koneksi/koneksi.php';

$current = $_GET['current'] ?? '';
$current_ids = explode(',', $current);

$q = mysqli_query($koneksi, "SELECT id, nama_admin FROM admins ORDER BY nama_admin ASC");
?>

<div style="text-align:left;">

    <div style="margin-bottom:10px;">
        <input type="text" id="searchAdmin" class="form-control"
               placeholder="Cari nama admin...">
    </div>

    <div style="max-height:350px;overflow-y:auto;border:1px solid #ddd;padding:10px;border-radius:6px;">
    <?php while($a = mysqli_fetch_assoc($q)): ?>
        <div class="admin-item" style="text-align:left;">
            <label style="display:block;margin-bottom:6px;text-align:left;">
                <input type="checkbox"
                       class="chk-admin"
                       value="<?= $a['id']; ?>"
                       <?= in_array($a['id'], $current_ids) ? 'checked' : '' ?>>
                <?= $a['nama_admin']; ?>
            </label>
        </div>
    <?php endwhile; ?>
    </div>

</div>