<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['role'] ?? 'editor';

$soalPages = [
'soal.php','edit_soal.php','tambah_soal.php',
'edit_butir_soal.php','tambah_butir_soal.php',
'preview_soal.php','daftar_butir_soal.php',
'upload-gambar.php','kartu_siswa.php',
'daftar_hadir.php','berita_acara.php',
'jadwal_ujian.php'
];

$hasilPages = ['hasil.php','ranking_siswa.php'];
$backupPages = ['backup.php','backup_gbr.php','reset_database.php'];

$isSoalOpen   = in_array($currentPage,$soalPages);
$isHasilOpen  = in_array($currentPage,$hasilPages);
$isBackupOpen = in_array($currentPage,$backupPages);
?>
<nav id="sidebar" class="sidebar js-sidebar">
    <div class="sidebar-content js-simplebar">
        <a class="sidebar-brand d-flex align-items-center" href="./">
            <img src="../assets/images/icon.png" alt="Logo" style="height:40px; width:auto; margin-right:10px;">

            <span class="align-middle">
                <?= htmlspecialchars($pengaturan['nama_aplikasi'] ?? 'CBT E-School') ?>
            </span>
        </a>

        <ul class="sidebar-nav">

            <li class="sidebar-item <?= ($currentPage == 'dashboard.php') ? 'active' : '' ?>">
                <a class="sidebar-link" href="dashboard.php">
                    <i class="align-middle fas fa-tachometer-alt fa-fw"></i> <span class="align-middle">Dashboard</span>
                </a>
            </li>

            <li
                class="sidebar-item <?= ($currentPage == 'soal.php') ? 'active' : '' ?> <?= ($currentPage == 'edit_soal.php') ? 'active' : '' ?>  
        <?= ($currentPage == 'tambah_soal.php') ? 'active' : '' ?> <?= ($currentPage == 'edit_butir_soal.php') ? 'active' : '' ?> 
        <?= ($currentPage == 'tambah_butir_soal.php') ? 'active' : '' ?> <?= ($currentPage == 'preview_soal.php') ? 'active' : '' ?> 
        <?= ($currentPage == 'daftar_butir_soal.php') ? 'active' : '' ?> <?= ($currentPage == 'upload-gambar.php') ? 'active' : '' ?> 
        <?= ($currentPage == 'kartu_siswa.php') ? 'active' : '' ?> <?= ($currentPage == 'daftar_hadir.php') ? 'active' : '' ?>
        <?= ($currentPage == 'berita_acara.php') ? 'active' : '' ?> <?= ($currentPage == 'jadwal_ujian.php') ? 'active' : '' ?>">
                <a data-bs-toggle="collapse" href="#soal" class="sidebar-link <?= $isSoalOpen?'':'collapsed'?>">
                    <i class="align-middle fa fa-file fa-fw"></i> <span class="align-middle">Manajemen Ujian </span>
                </a>
                <ul id="soal"
                    class="sidebar-dropdown list-unstyled collapse timeline-submenu <?= $isSoalOpen?'show':''?>"
                    data-bs-parent="#sidebar">
                    <li class="sidebar-item submenu <?= ($currentPage == 'soal.php') ? 'active' : '' ?>"><a
                            class="sidebar-link" href="soal.php"><i class="align-middle fas fa-book fa-fw"></i> Bank Soal</a>
                    </li>
                    <li class="sidebar-item submenu <?= ($currentPage == 'upload-gambar.php') ? 'active' : '' ?>"><a
                            class="sidebar-link" href="upload-gambar.php"><i class="align-middle fas fa-upload fa-fw"></i>
                            Upload Gambar</a></li>
                    <li class="sidebar-item submenu <?= ($currentPage == 'kartu_siswa.php') ? 'active' : '' ?>"><a
                            class="sidebar-link" href="kartu_siswa.php"><i class="align-middle fas fa-id-card fa-fw"></i> Cetak
                            Kartu Ujian</a></li>
                    <li class="sidebar-item submenu <?= ($currentPage == 'daftar_hadir.php') ? 'active' : '' ?>"><a
                            class="sidebar-link" href="daftar_hadir.php"><i class="align-middle fas fa-print fa-fw"></i> Cetak
                            Daftar Hadir</a></li>
                    <li class="sidebar-item submenu <?= ($currentPage == 'berita_acara.php') ? 'active' : '' ?>"><a
                            class="sidebar-link" href="berita_acara.php"><i class="align-middle fas fa-print fa-fw"></i> Cetak
                            Berita Acara</a></li>
                </ul>
            </li>

            <?php if($role=='admin'): ?>
            <li
                class="sidebar-item <?= ($currentPage == 'siswa.php') ? 'active' : '' ?> <?= ($currentPage == 'edit_siswa.php') ? 'active' : '' ?> <?= ($currentPage == 'tambah_siswa.php') ? 'active' : '' ?>">
                <a class="sidebar-link" href="siswa.php">
                    <i class="align-middle fas fa-user fa-fw"></i> <span class="align-middle">Manajemen Siswa</span>
                </a>
            </li>
            <?php endif; ?>
            <?php if($role=='admin'): ?>
            <li
                class="sidebar-item <?= ($currentPage == 'manajemen_user.php') ? 'active' : '' ?> <?= ($currentPage == 'edit_user.php') ? 'active' : '' ?> <?= ($currentPage == 'tambah_user.php') ? 'active' : '' ?>">
                <a class="sidebar-link" href="manajemen_user.php">
                    <i class="align-middle fas fa-users fa-fw"></i> <span class="align-middle">Manajemen Pengguna</span>
                </a>
            </li>
            <?php endif; ?>

            <li class="sidebar-item <?= ($currentPage == 'monitor.php') ? 'active' : '' ?>">
                <a class="sidebar-link" href="monitor.php">
                    <i class="align-middle fas fa-laptop fa-fw"></i> <span class="align-middle">Monitoring Ujian</span>
                </a>
            </li>

            <li class="sidebar-item <?= ($currentPage == 'reset_login.php') ? 'active' : '' ?>">
                <a class="sidebar-link" href="reset_login.php">
                    <i class="align-middle fas fa-redo fa-fw"></i> <span class="align-middle">Reset Login</span>
                </a>
            </li>

            <li class="sidebar-item  <?= ($currentPage == 'ranking_siswa.php') ? 'active' : '' ?> <?= ($currentPage == 'edit_butir_soal.php') ? 'active' : '' ?> 
        <?= ($currentPage == 'hasil.php') ? 'active' : '' ?>">
                <a data-bs-toggle="collapse" href="#hasil" class="sidebar-link collapsed">
                    <i class="align-middle fas fa-chart-pie fa-fw"></i> <span class="align-middle">Hasil Ujian </span>
                </a>
                <ul id="hasil"
                    class="sidebar-dropdown list-unstyled collapse timeline-submenu <?= $isHasilOpen?'show':''?>"
                    data-bs-parent="#sidebar">
                    <li class="sidebar-item submenu <?= ($currentPage == 'hasil.php') ? 'active' : '' ?>"><a
                            class="sidebar-link" href="hasil.php"><i class="align-middle fas fa-chart-line fa-fw"></i> Hasil
                            Ujian</a></li>
                    <li class="sidebar-item submenu <?= ($currentPage == 'ranking_siswa.php') ? 'active' : '' ?>"><a
                            class="sidebar-link" href="ranking_siswa.php"><i
                                class="align-middle fas fa-trophy fa-fw"></i>Ranking Siswa</a></li>
                </ul>
            </li>

            <li class="sidebar-item <?= ($currentPage == 'online.php') ? 'active' : '' ?>">
                <a class="sidebar-link" href="online.php">
                    <i class="align-middle fas fa-chalkboard-teacher fa-fw"></i> <span class="align-middle">Who's
                        Online</span>
                </a>
            </li>

            <?php if($role=='admin'): ?>
            <li class="sidebar-item <?= ($currentPage == 'faq.php') ? 'active' : '' ?>">
                <a class="sidebar-link" href="faq.php">
                    <i class="align-middle fas fa-robot fa-fw"></i> <span class="align-middle">FAQ</span>
                </a>
            </li>
            <?php endif; ?>


            <li class="sidebar-item <?= ($currentPage == 'chatbox_siswa.php') ? 'active' : '' ?>">
                <a class="sidebar-link" href="chatbox_siswa.php">
                    <i class="align-middle fas fa-comment fa-fw"></i> <span class="align-middle">ChatBox</span>
                </a>
            </li>

            <li class="sidebar-item <?= ($currentPage == 'leaderboard.php') ? 'active' : '' ?>">
                <a class="sidebar-link" href="leaderboard.php">
                    <i class="align-middle fas fa-gamepad fa-fw"></i> <span class="align-middle">Mini Games</span>
                </a>
            </li>

            <?php if($role=='admin'): ?>
            <li
                class="sidebar-item <?= ($currentPage == 'backup.php') ? 'active' : '' ?> <?= ($currentPage == 'reset_database.php') ? 'active' : '' ?> <?= ($currentPage == 'backup_gbr.php') ? 'active' : '' ?>">
                <a data-bs-toggle="collapse" href="#backup" class="sidebar-link collapsed">
                    <i class="align-middle fas fa-hdd fa-fw"></i> <span class="align-middle">Backup </span>
                </a>
                <ul id="backup"
                    class="sidebar-dropdown list-unstyled collapse timeline-submenu <?= $isBackupOpen?'show':''?>"
                    data-bs-parent="#sidebar">
                    <li class="sidebar-item submenu <?= ($currentPage == 'backup.php') ? 'active' : '' ?>"><a
                            class="sidebar-link" href="backup.php"><i class="align-middle fas fa-database fa-fw"></i> 
                            Backup Database</a></li>
                    <li class="sidebar-item submenu <?= ($currentPage == 'backup_gbr.php') ? 'active' : '' ?>"><a
                            class="sidebar-link" href="backup_gbr.php"><i class="align-middle fas fa-download fa-fw"></i>
                            Backup Gambar Soal</a></li>
                    <li class="sidebar-item submenu <?= ($currentPage == 'reset_database.php') ? 'active' : '' ?>"><a
                            class="sidebar-link" href="reset_database.php"><i class="align-middle fas fa-save fa-fw"></i> 
                            Reset Database</a></li>
                </ul>
            </li>

            <li class="sidebar-item <?= ($currentPage == 'log.php') ? 'active' : '' ?>">
                <a class="sidebar-link" href="log.php">
                    <i class="align-middle fas fa-history fa-fw"></i> <span class="align-middle">Log Activity</span>
                </a>
            </li>

            <li
                class="sidebar-item <?= ($currentPage == 'setting.php') ? 'active' : '' ?> <?= ($currentPage == 'pass.php') ? 'active' : '' ?>">
                <a class="sidebar-link" href="setting.php">
                    <i class="align-middle fas fa-cogs fa-fw"></i> <span class="align-middle">Pengaturan</span>
                </a>
            </li>
            <?php endif; ?>

            <li class="sidebar-item <?= ($currentPage == 'server.php') ? 'active' : '' ?>">
                <a class="sidebar-link" href="server.php">
                   <i class="align-middle fas fa-network-wired fa-fw"></i> <span class="align-middle">Server Status</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link btnLogout" href="logout.php">
                    <i class="align-middle fas fa-sign-out-alt fa-fw"></i> <span class="align-middle">Logout</span>
                </a>
            </li>
            <br><br>

        </ul>
    </div>
</nav>