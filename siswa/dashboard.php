<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('siswa');
include '../inc/datasiswa.php';

$query = "SELECT * FROM faq";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard Siswa</title>
    <?php include '../inc/css.php'; ?>

    <style>
    /* ===============================
   DASHBOARD LAUNCHER TILE (CBT UI)
=============================== */

    .dashboard-header {
        padding: 30px 0;
        text-align: center;
    }

    .tile-link {
        text-decoration: none;
        color: inherit;
        display: block;
        height: 100%;
    }

    .tile-link:hover {
        color: inherit;
    }

    .tile-card {
        position: relative;
        border-radius: 14px !important;
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.04);

        box-shadow:
            0 1px 2px rgba(0, 0, 0, 0.04),
            0 2px 6px rgba(0, 0, 0, 0.06);

        transition: .2s ease;
        overflow: hidden;
        cursor: pointer;
    }

    .tile-card::after {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: 14px;
        pointer-events: none;
        box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.03);
    }

    .tile-card:hover {
        transform: translateY(-2px);
        box-shadow:
            0 4px 10px rgba(0, 0, 0, 0.08),
            0 2px 4px rgba(0, 0, 0, 0.04);
    }

    .dark-mode .tile-card {
        background: #1e1e1e;
        border: 1px solid rgba(255, 255, 255, 0.04);

        box-shadow:
            0 1px 2px rgba(0, 0, 0, 0.6),
            0 3px 8px rgba(0, 0, 0, 0.4);
    }

    .dark-mode .tile-card::after {
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.05);
    }

    .tile-body {
        position: relative;
        padding: 24px 18px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: center;
        height: 100%;
    }

    .tile-title {
        font-size: 15px;
        font-weight: 600;
        margin-bottom: 2px;
        color: #111;
        z-index: 2;
    }

    .dark-mode .tile-title {
        color: #fff;
    }

    .tile-desc {
        font-size: 12px;
        opacity: .7;
        z-index: 2;
    }

    /* ICON BACKGROUND */
    .tile-bg-icon {
        position: absolute;
        right: -10px;
        bottom: -10px;
        font-size: 70px;
        opacity: .08;
        pointer-events: none;
        transform: rotate(-10deg);
        transition: .2s;
    }

    .dark-mode .tile-bg-icon {
        opacity: .06;
    }

    .tile-card:hover .tile-bg-icon {
        transform: rotate(-6deg) scale(1.05);
        opacity: .12;
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

                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0 text-white"><strong>Dashboard Siswa</strong></h5>
                        </div>

                        <div class="card-body">

                            <div class="dashboard-header">
                                <h4>Halo, <?= htmlspecialchars($nama_siswa); ?> 👋</h4>
                                <p class="text-muted">Selamat datang di dashboard ujian kamu</p>
                            </div>

                            <div class="row g-4 pb-5">

                                <!-- SCAN QR -->
                                <div class="col-md-6 col-lg-4">
                                    <div class="tile-link" id="scanCard">
                                        <div class="card tile-card h-100 border-0">
                                            <div class="card-body tile-body">

                                                <div class="tile-title">Scan QR Ujian</div>
                                                <div class="tile-desc">Akses ujian menggunakan QR code</div>

                                                <div class="tile-bg-icon">
                                                    <i class="fas fa-camera"></i>
                                                </div>

                                                <div id="qr-reader" style="width:250px; display:none;" class="mt-2">
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- UJIAN -->
                                <div class="col-md-6 col-lg-4">
                                    <a href="ujian.php" class="tile-link">
                                        <div class="card tile-card h-100 border-0">
                                            <div class="card-body tile-body">

                                                <div class="tile-title">Kerjakan Ujian</div>
                                                <div class="tile-desc">Mulai ujian aktif sekarang</div>

                                                <div class="tile-bg-icon">
                                                    <i class="fas fa-pen"></i>
                                                </div>

                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <!-- HASIL -->
                                <div class="col-md-6 col-lg-4">
                                    <a href="hasil.php" class="tile-link">
                                        <div class="card tile-card h-100 border-0">
                                            <div class="card-body tile-body">

                                                <div class="tile-title">Hasil Ujian</div>
                                                <div class="tile-desc">Lihat nilai ujian kamu</div>

                                                <div class="tile-bg-icon">
                                                    <i class="fas fa-chart-line"></i>
                                                </div>

                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <!-- PERANGKAT -->
                                <div class="col-md-6 col-lg-4">
                                    <a href="perangkat.php" class="tile-link">
                                        <div class="card tile-card h-100 border-0">
                                            <div class="card-body tile-body">

                                                <div class="tile-title">Status Perangkat</div>
                                                <div class="tile-desc">Cek perangkat ujian</div>

                                                <div class="tile-bg-icon">
                                                    <i class="fas fa-laptop"></i>
                                                </div>

                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <!-- GAME -->
                                <div class="col-md-6 col-lg-4">
                                    <a href="game.php" class="tile-link">
                                        <div class="card tile-card h-100 border-0">
                                            <div class="card-body tile-body">

                                                <div class="tile-title">Mini Games</div>
                                                <div class="tile-desc">Belajar sambil bermain</div>

                                                <div class="tile-bg-icon">
                                                    <i class="fas fa-gamepad"></i>
                                                </div>

                                            </div>
                                        </div>
                                    </a>
                                </div>

                                <!-- CHAT -->
                                <div class="col-md-6 col-lg-4">
                                    <a href="chat.php" class="tile-link">
                                        <div class="card tile-card h-100 border-0">
                                            <div class="card-body tile-body">

                                                <div class="tile-title">ChatBox</div>
                                                <div class="tile-desc">Diskusi dengan teman</div>

                                                <div class="tile-bg-icon">
                                                    <i class="fas fa-comments"></i>
                                                </div>

                                            </div>
                                        </div>
                                    </a>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <?php include 'chatbot.php'; ?>
    <?php include '../inc/js.php'; ?>
    <?php include '../inc/check_activity.php'; ?>

    <script src="../assets/js/html5-qrcode.min.js"></script>

    <script>
    document.getElementById('scanCard').addEventListener('click', function() {

        const qrReader = document.getElementById('qr-reader');
        qrReader.style.display = 'block';

        const html5QrCode = new Html5Qrcode("qr-reader");

        html5QrCode.start({
                facingMode: "environment"
            }, {
                fps: 10,
                qrbox: 250
            },
            qrCodeMessage => {
                html5QrCode.stop().then(() => {
                    qrReader.style.display = 'none';
                    window.location.href = qrCodeMessage;
                });
            });
    });
    </script>

    <?php if (isset($_SESSION['error'])): ?>
    <script>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '<?= $_SESSION['error']; ?>',
        confirmButtonColor: '#dc3545'
    });
    </script>
    <?php unset($_SESSION['error']); endif; ?>

</body>

</html>