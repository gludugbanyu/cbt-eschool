<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<nav id="sidebar" class="sidebar js-sidebar">
            <div class="sidebar-content js-simplebar">
                <a class="sidebar-brand d-flex align-items-center" href="./">
                    <img src="../assets/images/icon.png" 
                        alt="Logo" 
                        style="height:40px; width:auto; margin-right:10px;">
                    
                    <span class="align-middle">
                        <?= htmlspecialchars($pengaturan['nama_aplikasi'] ?? 'CBT E-School') ?>
                    </span>
                </a>

                <ul class="sidebar-nav">
                    <li class="sidebar-header">Menu Utama</li>

                    <li class="sidebar-item <?= ($currentPage == 'dashboard.php') ? 'active' : '' ?>">
                        <a class="sidebar-link" href="dashboard.php">
                            <i class="align-middle fas fa-home"></i> <span class="align-middle">Dashboard</span>
                        </a>
                    </li>

                    <li class="sidebar-item <?= ($currentPage == 'ujian.php') ? 'active' : '' ?>">
                        <a class="sidebar-link" href="ujian.php">
                            <i class="align-middle fas fa-edit"></i> <span class="align-middle">Ujian</span>
                        </a>
                    </li>

                    <li class="sidebar-item <?= ($currentPage == 'hasil.php') ? 'active' : '' ?> <?= ($currentPage == 'preview_hasil.php') ? 'active' : '' ?>">
                        <a class="sidebar-link" href="hasil.php">
                            <i class="align-middle fas fa-chart-line"></i> <span class="align-middle">Hasil Ujian</span>
                        </a>
                    </li>

                    <li class="sidebar-item <?= ($currentPage == 'chat.php') ? 'active' : '' ?>">
                        <a class="sidebar-link" href="chat.php">
                        <i class="fas fa-comments" aria-hidden="true"></i> <span class="align-middle">ChatBox</span>
                        </a>
                    </li>

                    <li class="sidebar-item <?= ($currentPage == 'game.php') ? 'active' : '' ?>">
                        <a class="sidebar-link" href="game.php">
                        <i class="fa fa-gamepad" aria-hidden="true"></i> <span class="align-middle">Mini Games</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link btnLogout" href="logout.php">
                            <i class="align-middle fas fa-sign-out-alt"></i> <span class="align-middle">Logout</span>
                        </a>
                    </li>
                </ul>

            </div>
        </nav>