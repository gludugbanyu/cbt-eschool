<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
include '../inc/dataadmin.php';

/* ================= SERVER INFO ================= */
$php_version      = phpversion();
$mysql_version    = mysqli_get_server_info($koneksi);
$server_raw = $_SERVER['SERVER_SOFTWARE'];

if (preg_match('/Apache\/([0-9\.]+)/i', $server_raw, $match)) {
    $apache_version = "Apache " . $match[1];
} else {
    $apache_version = "Apache";
}
$upload_max       = ini_get('upload_max_filesize');
$post_max         = ini_get('post_max_size');
$memory_limit     = ini_get('memory_limit');
$max_execution    = ini_get('max_execution_time');

function badge($ext){
    return extension_loaded($ext)
        ? '<span class="status-online">Aktif</span>'
        : '<span class="status-offline">Tidak Aktif</span>';
}

/* ===== Disk Space ===== */
$total_space = disk_total_space("/");
$free_space  = disk_free_space("/");
$used_space  = $total_space - $free_space;
$disk_percent = round(($used_space / $total_space) * 100);

/* ===== Memory Usage ===== */
$memory_usage = memory_get_usage(true);
$memory_peak  = memory_get_peak_usage(true);
$memory_percent = round(($memory_usage / (intval($memory_limit)*1024*1024)) * 100);

/* ===== Apache Status ===== */
$apache_status = (strpos(strtolower($server_raw), 'apache') !== false)
    ? '<span class="status-online">Running</span>'
    : '<span class="status-offline">Unknown</span>';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Server Stats</title>
    <?php include '../inc/css.php'; ?>

    <style>
    .table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    table {
        border-collapse: collapse;
        width: 100%;
        min-width: 320px;
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 8px 10px;
        text-align: left;
    }

    th {
        background: #f4f4f4;
    }
    </style>
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
                            <!-- ================= SERVER STATUS PANEL ================= -->
                            <div class="card shadow-sm">
                                <div class="card-header bg-dark text-white">
                                    <h5 class="mb-0 text-white"><strong>Status webServer</strong></h5>
                                </div>
                                <div class="card-body">

                                    <div class="row g-3">

                                        <!-- PHP -->
                                        <div class="col-md-3 col-6">
                                            <div class="server-box text-center">
                                                <div class="server-icon">
                                                    <i class="fab fa-php text-primary"></i>
                                                </div>
                                                <div class="server-title">PHP Version</div>
                                                <div class="server-value"><?= $php_version ?></div>
                                            </div>
                                        </div>

                                        <!-- MYSQL -->
                                        <div class="col-md-3 col-6">
                                            <div class="server-box text-center">
                                                <div class="server-icon">
                                                    <i class="fas fa-database text-info"></i>
                                                </div>
                                                <div class="server-title">MySQL Version</div>
                                                <div class="server-value"><?= $mysql_version ?></div>
                                            </div>
                                        </div>

                                        <!-- APACHE ONLY -->
                                        <div class="col-md-3 col-6">
                                            <div class="server-box text-center">
                                                <div class="server-icon">
                                                    <i class="fas fa-server text-dark"></i>
                                                </div>
                                                <div class="server-title">Web Server</div>
                                                <div class="server-value"><?= $apache_version ?></div>
                                            </div>
                                        </div>

                                        <!-- UPLOAD -->
                                        <div class="col-md-3 col-6">
                                            <div class="server-box text-center">
                                                <div class="server-icon">
                                                    <i class="fas fa-upload text-warning"></i>
                                                </div>
                                                <div class="server-title">Upload Max</div>
                                                <div class="server-value"><?= $upload_max ?></div>
                                            </div>
                                        </div>

                                        <!-- POST -->
                                        <div class="col-md-3 col-6">
                                            <div class="server-box text-center">
                                                <div class="server-icon">
                                                    <i class="fas fa-envelope-open-text text-secondary"></i>
                                                </div>
                                                <div class="server-title">POST Max</div>
                                                <div class="server-value"><?= $post_max ?></div>
                                            </div>
                                        </div>

                                        <!-- MEMORY LIMIT -->
                                        <div class="col-md-3 col-6">
                                            <div class="server-box text-center">
                                                <div class="server-icon">
                                                    <i class="fas fa-memory text-danger"></i>
                                                </div>
                                                <div class="server-title">Memory Limit</div>
                                                <div class="server-value"><?= $memory_limit ?></div>
                                            </div>
                                        </div>

                                        <!-- EXECUTION -->
                                        <div class="col-md-3 col-6">
                                            <div class="server-box text-center">
                                                <div class="server-icon">
                                                    <i class="fas fa-clock text-muted"></i>
                                                </div>
                                                <div class="server-title">Execution Time</div>
                                                <div class="server-value"><?= $max_execution ?>s</div>
                                            </div>
                                        </div>

                                        <!-- APACHE STATUS -->
                                        <div class="col-md-3 col-6">
                                            <div class="server-box text-center">
                                                <div class="server-icon">
                                                    <i class="fas fa-network-wired text-success"></i>
                                                </div>
                                                <div class="server-title">Apache Status</div>
                                                <div class="server-value"><?= $apache_status ?></div>
                                            </div>
                                        </div>

                                        <!-- GD EXTENSION -->
                                        <div class="col-md-3 col-6">
                                            <div class="server-box text-center">
                                                <div class="server-icon">
                                                    <i class="fas fa-image text-success"></i>
                                                </div>
                                                <div class="server-title">GD Extension</div>
                                                <div class="server-value"><?= badge('gd') ?></div>
                                            </div>
                                        </div>

                                        <!-- MBSTRING EXTENSION -->
                                        <div class="col-md-3 col-6">
                                            <div class="server-box text-center">
                                                <div class="server-icon">
                                                    <i class="fas fa-file-archive text-warning"></i>
                                                </div>
                                                <div class="server-title">ZIP Extension</div>
                                                <div class="server-value"><?= badge('zip') ?></div>
                                            </div>
                                        </div>

                                        <!-- DISK -->
                                        <div class="col-md-3 col-6">
                                            <div class="server-box text-center">
                                                <div class="server-icon">
                                                    <i class="fas fa-hdd text-success"></i>
                                                </div>
                                                <div class="server-title">Disk Usage
                                                    (<?= round($total_space/1073741824) ?> GB)</div>
                                                <div class="progress-wrap">
                                                    <div class="progress-bar" style="width:<?= $disk_percent ?>%"></div>
                                                </div>
                                                <div class="server-value"><?= $disk_percent ?>% used</div>
                                            </div>
                                        </div>

                                        <!-- MEMORY GRAPH -->
                                        <div class="col-md-3 col-6">
                                            <div class="server-box text-center">
                                                <div class="server-icon">
                                                    <i class="fas fa-chart-line text-danger"></i>
                                                </div>
                                                <div class="server-title">Memory Usage</div>
                                                <div class="progress-wrap">
                                                    <div class="progress-bar" style="width:<?= $memory_percent ?>%">
                                                    </div>
                                                </div>
                                                <div class="server-value">
                                                    <?= round($memory_usage/1048576) ?>MB /
                                                    <?= intval($memory_limit) ?>MB
                                                </div>
                                            </div>
                                        </div>


                                    </div>

                                </div>
                            </div>
                            <!-- ================= END SERVER PANEL ================= -->
                            <!-- ================= DETAIL PERANGKAT ================= -->
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0 text-white"><strong>Detail Perangkat</strong></h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <th>Status Koneksi</th>
                                                    <td id="status">Memeriksa...</td>
                                                </tr>
                                                <tr>
                                                    <th>IP Address</th>
                                                    <td id="ip">Memuat...</td>
                                                </tr>
                                                <tr>
                                                    <th>OS & Browser</th>
                                                    <td id="userAgent">Memuat...</td>
                                                </tr>
                                                <tr>
                                                    <th>Resolusi Layar</th>
                                                    <td id="resolusi">Memuat...</td>
                                                </tr>
                                                <tr>
                                                    <th>Ukuran Viewport</th>
                                                    <td id="viewport">Memuat...</td>
                                                </tr>
                                                <tr>
                                                    <th>Bahasa Sistem</th>
                                                    <td id="bahasa">Memuat...</td>
                                                </tr>
                                                <tr>
                                                    <th>Waktu Lokal</th>
                                                    <td id="waktu">Memuat...</td>
                                                </tr>
                                                <tr>
                                                    <th>Zona Waktu</th>
                                                    <td id="zona">Memuat...</td>
                                                </tr>
                                                <tr>
                                                    <th>Platform</th>
                                                    <td id="platform">Memuat...</td>
                                                </tr>
                                                <tr>
                                                    <th>Cookie Diaktifkan</th>
                                                    <td id="cookie">Memuat...</td>
                                                </tr>
                                                <tr>
                                                    <th>Jenis Perangkat</th>
                                                    <td id="deviceType">Memuat...</td>
                                                </tr>
                                                <tr>
                                                    <th>Touch Support</th>
                                                    <td id="touch">Memuat...</td>
                                                </tr>
                                                <tr>
                                                    <th>Status Baterai</th>
                                                    <td id="baterai">Memuat...</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
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
    // JS Anda tetap sama (tidak saya ubah)
    document.addEventListener('DOMContentLoaded', function() {
        function $(id) {
            return document.getElementById(id);
        }

        function updateOnlineStatus() {
            if (navigator.onLine) {
                $('status').textContent = "Online";
                $('status').className = "status-online";
            } else {
                $('status').textContent = "Offline";
                $('status').className = "status-offline";
            }
        }
        updateOnlineStatus();
        window.addEventListener('online', updateOnlineStatus);
        window.addEventListener('offline', updateOnlineStatus);

        fetch("https://api.ipify.org?format=json")
            .then(res => res.json())
            .then(data => {
                $('ip').textContent = data.ip;
            })
            .catch(() => {
                $('ip').textContent = "Tidak tersedia";
            });

        $('userAgent').textContent = navigator.userAgent;
        $('resolusi').textContent = screen.width + " x " + screen.height;
        $('viewport').textContent = window.innerWidth + " x " + window.innerHeight;
        $('bahasa').textContent = navigator.language;
        $('waktu').textContent = new Date().toLocaleString();
        $('zona').textContent = Intl.DateTimeFormat().resolvedOptions().timeZone;
        $('platform').textContent = navigator.platform;
        $('cookie').textContent = navigator.cookieEnabled ? "Aktif" : "Nonaktif";

        let ua = navigator.userAgent.toLowerCase();
        $('deviceType').textContent = /mobile|android|iphone|ipad/.test(ua) ? "Mobile/Tablet" : "Desktop";
        $('touch').textContent = ('ontouchstart' in window || navigator.maxTouchPoints > 0) ? "Touchscreen" :
            "Non-Touch";

        if ('getBattery' in navigator) {
            navigator.getBattery().then(function(b) {
                $('baterai').textContent = Math.round(b.level * 100) + "%" + (b.charging ?
                    " (Charging)" : "");
            });
        } else {
            $('baterai').textContent = "Tidak didukung";
        }
    });
    </script>

</body>

</html>