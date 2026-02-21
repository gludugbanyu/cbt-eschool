<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('siswa'); // Pastikan siswa sudah login
include '../inc/datasiswa.php';
require_once '../assets/phpqrcode/qrlib.php';

// Dapatkan protokol dan host dinamis
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$scriptPath = dirname($_SERVER['SCRIPT_NAME']);
$basePath = rtrim(dirname($scriptPath, 1), '/');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ujian Siswa</title>
    <?php include '../inc/css.php'; ?>
    <style>
    /* FLEX */
    .ujian-flex {
        display: flex;
        gap: 16px;
        align-items: center;
    }

    /* QR */
    .icon-wrapper {
        width: 70px;
        height: 70px;
        flex-shrink: 0;
    }

    /* RIGHT */
    .ujian-right {
        display: flex;
        flex-direction: column;
        width: 100%;
    }

    /* HEADER */
    .top {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* KODE */
    .kode {
        font-size: 15px;
        font-weight: 600;
        margin: 0;
    }

    /* INFO */
    .info {
        font-size: 13px;
        opacity: .8;
    }

    /* META */
    .meta {
        font-size: 12px;
        opacity: .6;
    }

    /* STATUS CLEAN */
    .status {
        font-size: 12px;
        font-weight: 500;
    }

    /* LOCKED */
    .status.text-danger {
        color: #d32f2f !important;
    }

    /* READY */
    .status.text-success {
        color: #2e7d32 !important;
    }

    /* BUTTON */
    .btn-masuk {
        margin-top: 8px;
        align-self: flex-end;
        font-size: 12px;
        padding: 4px 14px;
        border-radius: 8px;
    }

    /* READY CARD */
    .ujian-card:has(.text-success) {
        border-left: 4px solid #2e7d32 !important;
    }

    /* LOCKED */
    .ujian-card:has(.text-danger) {
        border-left: 4px solid #d32f2f !important;
    }

    /* FLEX */
    .ujian-flex {
        display: flex;
        gap: 18px;
        align-items: flex-start;
    }

    /* LEFT SIDE */
    .ujian-left {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 90px;
    }

    /* QR BESAR */
    .icon-wrapper {
        width: 80px;
        height: 80px;
    }

    /* STATUS DI BAWAH QR */
    .status {
        font-size: 10px;
        margin-top: 4px;
        text-align: center;
        line-height: 1;
    }

    /* RIGHT */
    .ujian-right {
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    /* KODE */
    .kode {
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 2px;
    }

    /* MAPEL */
    .info {
        font-size: 13px;
        opacity: .8;
    }

    /* META */
    .meta {
        font-size: 12px;
        opacity: .6;
        margin-bottom: 4px;
    }

    /* BUTTON */
    .btn-masuk {
        align-self: flex-start;
        font-size: 12px;
        padding: 4px 14px;
        border-radius: 8px;
    }

    /* READY */
    .countdown.text-success {
        color: #2e7d32 !important;
    }

    /* LOCKED */
    .countdown.text-danger {
        color: #d32f2f !important;
    }

    .ujian-card {
        position: relative;
        border-radius: 14px !important;
        background: #ffffff !important;
        border: 1px solid rgba(0, 0, 0, 0.04) !important;

        box-shadow:
            0 1px 2px rgba(0, 0, 0, 0.04),
            0 2px 6px rgba(0, 0, 0, 0.06);

        transition: .2s ease;
        overflow: hidden;
    }

    /* INNER BORDER (CRISP EDGE) */
    .ujian-card::after {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: 14px;
        pointer-events: none;
        box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.03);
    }

    .dark-mode .ujian-card {
        background: #1e1e1e !important;
        border: 1px solid rgba(255, 255, 255, 0.04) !important;

        box-shadow:
            0 1px 2px rgba(0, 0, 0, 0.6),
            0 3px 8px rgba(0, 0, 0, 0.4);
    }

    .dark-mode .ujian-card::after {
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.05);
    }

    .ujian-card:hover {
        transform: translateY(-2px);
        box-shadow:
            0 4px 10px rgba(0, 0, 0, 0.08),
            0 2px 4px rgba(0, 0, 0, 0.04);
    }

    .dark-mode .ujian-card:hover {
        box-shadow:
            0 4px 12px rgba(0, 0, 0, 0.7),
            0 2px 6px rgba(0, 0, 0, 0.5);
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
                            <div class="card">
                                <div
                                    class="card-header d-flex bg-secondary text-white justify-content-between align-items-center flex-wrap gap-2">
                                    <h5 class="card-title mb-0 text-white">Ujian Aktif</h5>
                                    <small id="last-updated" class="text-white"></small>
                                </div>
                                <div class="card-body">
                                    <input type="text" id="searchInput" class="form-control mb-3"
                                        placeholder="Cari ujian...">
                                    <div id="ujian-container" class="row g-3">
                                        <!-- Kartu ujian akan dimuat di sini -->
                                    </div>
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
    <script src="../assets/js/qrcode.min.js"></script>

    <script>
    let semuaUjian = [];

    function tampilkanUjian(data) {
        const container = document.getElementById('ujian-container');
        container.innerHTML = '';

        if (data.length === 0) {
            container.innerHTML =
                '<div class="col-12 text-center py-5"><i class="fa fa-user-slash fa-3x text-muted mb-3"></i><br>Tidak ada ujian ditemukan.</div>';
            return;
        }

        const pathNameParts = window.location.pathname.split('/').filter(Boolean);
        const appFolder = pathNameParts.length > 0 ? '/' + pathNameParts[0] : '';

        let allCardsHTML = '';
        const qrList = []; // Simpan data QR yang perlu digenerate

        data.forEach(ujian => {
            const cardId = 'qr-' + ujian.kode_soal;
            const qrLink =
                `${window.location.origin}${appFolder}/siswa/konfirmasi_ujian.php?kode_soal=${encodeURIComponent(ujian.kode_soal)}`;

            qrList.push({
                id: cardId,
                link: qrLink
            }); // Simpan data QR-nya

            allCardsHTML += `
        <div class="col-12 col-lg-4 col-xl-3 col-sm-6 col-md-4">
            <div class="card ujian-card h-100 border-0">
<div class="card-body ujian-flex">

<div class="ujian-left">
<div id="${cardId}" class="icon-wrapper"></div>

<div class="status countdown text-danger"
data-tanggal="${ujian.tanggal}">
Belum dimulai
</div>
</div>

<div class="ujian-right">

<h5 class="kode">${ujian.kode_soal}</h5>

<div class="info">${ujian.mapel}</div>

<div class="meta">
${ujian.waktu_ujian} menit • ${ujian.tanggal}
</div>

<a href="konfirmasi_ujian.php?kode_soal=${ujian.kode_soal}"
class="btn btn-outline-secondary btn-masuk">
Masuk Ujian
</a>

</div>

</div>
</div>
</div>
        </div>`;
        });

        container.innerHTML = allCardsHTML;

        // Setelah semua elemen ada di DOM, generate semua QR code
        qrList.forEach(({
            id,
            link
        }) => {
            const el = document.getElementById(id);
            if (el) {
                new QRCode(el, {
                    text: link,
                    width: 80,
                    height: 80,
                    colorDark: "#000000",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.L
                });
            }
        });
    }

    function loadUjian() {
        fetch('get_ujian.php')
            .then(res => res.json())
            .then(data => {
                semuaUjian = data;
                tampilkanUjian(data);
                updateCountdown();

                // Update waktu terakhir
                let now = new Date();
                $('#last-updated').html(
                    `<i class="fa fa-refresh fa-spin text-success me-1"></i> Terakhir diperbarui: ${now.toLocaleTimeString('id-ID')}`
                );
            });
    }

    // Live search
    document.getElementById('searchInput').addEventListener('input', function() {
        const keyword = this.value.toLowerCase();
        const hasil = semuaUjian.filter(ujian =>
            ujian.mapel.toLowerCase().includes(keyword) ||
            ujian.kode_soal.toLowerCase().includes(keyword)
        );
        tampilkanUjian(hasil);
    });

    // Jalankan saat load dan per 1 menit
    loadUjian();
    setInterval(loadUjian, 60000);

    function updateCountdown() {

        const now = new Date();

        // ambil tanggal hari ini jam 00:00
        const today = new Date(
            now.getFullYear(),
            now.getMonth(),
            now.getDate()
        );

        document.querySelectorAll('.countdown').forEach(el => {

            let tgl = el.dataset.tanggal; // format: 2026-02-25
            let p = tgl.split('-');

            let mulai = new Date(
                parseInt(p[0]),
                parseInt(p[1]) - 1,
                parseInt(p[2])
            );

            let diff = mulai - today;
            let btn = el.closest('.card-body').querySelector('.btn-masuk');

            if (diff <= 0) {

                el.innerHTML = "Sudah bisa dikerjakan";
                el.classList.remove('text-danger');
                el.classList.add('text-success');
                btn.disabled = false;

            } else {

                btn.disabled = true;

                let hari = Math.ceil(diff / (1000 * 60 * 60 * 24));

                if (hari === 1) {
                    el.innerHTML = "Dimulai besok";
                } else if (hari <= 7) {
                    el.innerHTML = "Mulai " + hari + " hari lagi";
                } else if (hari <= 30) {
                    let minggu = Math.ceil(hari / 7);
                    el.innerHTML = "Mulai sekitar " + minggu + " minggu lagi";
                } else {
                    el.innerHTML = "Belum waktunya dikerjakan";
                }

            }

        });

    }

    setInterval(updateCountdown, 60000);
    </script>
    <?php if (!empty($_SESSION['warning_message'])): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: <?= json_encode($_SESSION['warning_message']); ?>,
            showConfirmButton: false,
            timer: 2000
        });
    });
    </script>
    <?php unset($_SESSION['warning_message']); ?>
    <?php endif; ?>
</body>

</html>