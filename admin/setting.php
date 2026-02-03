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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan</title>
    <?php include '../inc/css.php'; ?>
    <style>
    .progress {
        background-color: #e9ecef;
        border-radius: 0.25rem;
        overflow: hidden;
    }

    .progress-bar {
        background-color: #0d6efd;
        height: 100%;
        transition: width 0.4s ease;
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
                        <div class="col-12 col-md-8">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Form Pengaturan</h5>
                                </div>
                                <div class="card-body">
                                    <?php
                                        $q = mysqli_query($koneksi, "SELECT * FROM pengaturan WHERE id = 1");
                                        $data = mysqli_fetch_assoc($q);
                                        ?>
                                    <form action="simpan_pengaturan.php" method="post" enctype="multipart/form-data">
                                        <div class="row g-3">
                                            <!-- Nama Aplikasi -->


                                            <!-- Logo Sekolah -->
                                            <div class="col-12 col-md-6">
                                                <label for="logo_sekolah" class="form-label">Logo</label>
                                                <?php if (!empty($data['logo_sekolah'])): ?>
                                                <div class="mb-2">
                                                    <img id="preview-logo"
                                                        src="../assets/images/<?= $data['logo_sekolah'] ?>" alt="Logo"
                                                        width="150" class="img-thumbnail">
                                                </div>
                                                <?php endif; ?>
                                                <input type="file" class="form-control" name="logo_sekolah"
                                                    id="logo_sekolah" accept="image/*">
                                            </div>

                                            <!-- Warna Tema Aplikasi -->
                                            <div class="col-12 col-md-6">
                                                <label for="warna_tema" class="form-label">Warna Tema Ujian
                                                    Siswa</label>

                                                <!-- Color Palette -->
                                                <div class="d-flex flex-wrap gap-2 mb-2" id="palette">
                                                    <?php
                                                    $warnaList = ['#0d6efd', '#198754', '#dc3545', '#ffc107', '#6f42c1', '#20c997', '#fd7e14', '#343a40'];
                                                    $warnaSekarang = $data['warna_tema'] ?? '#0d6efd';
                                                    foreach ($warnaList as $warna) {
                                                        $selected = ($warnaSekarang === $warna) ? 'border-3 border-dark' : 'border';
                                                        echo "<div class='color-box $selected rounded' data-warna='$warna' style='width: 36px; height: 36px; background: $warna; cursor: pointer;'></div>";
                                                    }
                                                    ?>
                                                </div>

                                                <!-- Color Picker -->
                                                <input type="color" class="form-control form-control-color"
                                                    id="colorPicker" value="<?= $warnaSekarang ?>"
                                                    title="Pilih Warna Bebas">

                                                <!-- Hidden Input -->
                                                <input type="hidden" name="warna_tema" id="warna_tema"
                                                    value="<?= $warnaSekarang ?>">

                                                <small class="form-text text-muted">Klik warna di atas atau pilih warna
                                                    bebas di bawah.</small>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label for="nama_aplikasi" class="form-label">Nama Aplikasi</label>
                                                <input type="text" class="form-control" name="nama_aplikasi"
                                                    id="nama_aplikasi" value="<?= $data['nama_aplikasi'] ?? '' ?>"
                                                    required>
                                            </div>

                                            <!-- Waktu Sinkronisasi -->
                                            <div class="col-12 col-md-6">
                                                <label for="waktu_sinkronisasi" class="form-label">Waktu Sinkronisasi Ujian
                                                    (detik)</label>
                                                <input type="number" class="form-control" name="waktu_sinkronisasi"
                                                    id="waktu_sinkronisasi"
                                                    value="<?= $data['waktu_sinkronisasi'] ?? 60 ?>" min="10" required>
                                            </div>

                                            

                                            <!-- Status Login Ganda -->
                                            <div class="col-12 col-md-6">
                                                <label for="login_ganda" class="form-label">Status Login Ganda</label>
                                                <select class="form-select" name="login_ganda" id="login_ganda"
                                                    required>
                                                    <option value="izinkan"
                                                        <?= $data['login_ganda'] === 'izinkan' ? 'selected' : '' ?>>
                                                        Izinkan</option>
                                                    <option value="blokir"
                                                        <?= $data['login_ganda'] === 'blokir' ? 'selected' : '' ?>>
                                                        Blokir</option>
                                                </select>
                                            </div>

                                            <div class="col-12 col-md-6">
    <label class="form-label">Izinkan Siswa Melanjutkan Ujian Tanpa Reset</label>
    <select class="form-select" name="izinkan_lanjut_ujian" required>
        <option value="tidak" <?= $data['izinkan_lanjut_ujian'] === 'tidak' ? 'selected' : '' ?>>
            Tidak (Harus Reset Admin)
        </option>
        <option value="ya" <?= $data['izinkan_lanjut_ujian'] === 'ya' ? 'selected' : '' ?>>
            Ya (Boleh Masuk Lagi Tanpa Reset)
        </option>
    </select>
</div>

                     

                                        <div class="col-12 col-md-6">
                                            <label for="chat" class="form-label">Fitur ChatBox Siswa</label>
                                            <select class="form-select" name="chat" id="chat" required>
                                                <option value="izinkan"
                                                    <?= $data['chat'] === 'izinkan' ? 'selected' : '' ?>>
                                                    Izinkan</option>
                                                <option value="blokir"
                                                    <?= $data['chat'] === 'blokir' ? 'selected' : '' ?>>
                                                    Blokir</option>
                                            </select>
                                        </div>
                                        <!-- Sembunyikan Nilai -->
                                            <div class="col-12 col-md-6 d-flex align-items-center">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input"
                                                        name="sembunyikan_nilai" id="sembunyikan_nilai" value="1"
                                                        <?= !empty($data['sembunyikan_nilai']) ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="sembunyikan_nilai">Sembunyikan
                                                        Nilai Siswa (Dashboard Siswa)</label>
                                                </div>
                                            </div>
                                </div>

                                <div class="d-flex justify-content-start gap-2 mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Simpan Pengaturan
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="btnCekUpdate">
                                        <i class="fas fa-sync-alt"></i> Cek Update
                                    </button>
                                     <!-- Tambahkan setelah tombol Cek Update -->
                                    <button type="button" class="btn btn-outline-info" id="btnLihatLog">
                                        <i class="fas fa-clipboard-list"></i> Lihat Log Update
                                    </button>
                                </div>
                                <div id="hasilUpdate" class="form-text text-muted mt-2"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

        </div>
        </main>
<!-- Modal untuk log update -->
<div class="modal fade" id="logModal" tabindex="-1" aria-labelledby="logModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title" id="logModalLabel" style="color:white;">Riwayat Update</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" style="font-family: monospace; white-space: pre-wrap;">
        Memuat log...
      </div>
    </div>
  </div>
</div>

    </div>
    </div>
    <?php include '../inc/js.php'; ?>
    <script>
    document.getElementById('logo_sekolah').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('preview-logo');
        const maxSize = 2 * 1024 * 1024; // 2MB
        const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (!file) return;

        if (!validTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Format Tidak Valid',
                text: 'Hanya gambar JPG, PNG, GIF, atau WEBP yang diperbolehkan.'
            });
            this.value = '';
            preview.src = '../assets/images/<?= $data['logo_sekolah'] ?>';
            return;
        }

        if (file.size > maxSize) {
            Swal.fire({
                icon: 'warning',
                title: 'Ukuran Terlalu Besar',
                text: 'Ukuran file maksimal 2MB.'
            });
            this.value = '';
            preview.src = '../assets/images/<?= $data['logo_sekolah'] ?>';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });
    </script>
    <?php if (isset($_SESSION['success'])): ?>
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '<?= $_SESSION['success']; ?>',
        confirmButtonColor: '#28a745'
    });
    </script>
    <?php unset($_SESSION['success']); endif; ?>

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
    <script>
    document.getElementById('btnCekUpdate').addEventListener('click', function() {
        const hasil = document.getElementById('hasilUpdate');
        hasil.innerHTML = 'Sedang memeriksa versi terbaru...';

        fetch('cek_update.php')
            .then(res => res.json())
            .then(data => {
                hasil.innerHTML = '';
                if (data.status === 'update') {
                    Swal.fire({
                        title: 'Versi Baru Tersedia!',
                        html: `
                        <p><b>Versi saat ini:</b> ${data.versi_saat_ini}</p>
                        <p><b>Versi terbaru:</b> ${data.versi_baru}</p>
                        <hr>
                        <div style="text-align:left; max-height:200px; overflow:auto;">
                            <h6>Changelog:</h6>
                            ${data.changelog}
                        </div>
                    `,
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonText: 'Download & Update',
                        cancelButtonText: 'Tutup',
                        preConfirm: async () => {
                            Swal.fire({
                                title: '',
                                html: `
            <div class="progress" style="height: 20px;">
                <div class="progress-bar" style="width:0%;" role="progressbar"></div>
            </div>
            <p class="mt-2">Sedang memproses, mohon tunggu...</p>
        `,
                                didOpen: async () => {
                                    const progressBar = Swal.getHtmlContainer()
                                        .querySelector('.progress-bar');
                                    let progress = 0;
                                    const interval = setInterval(() => {
                                        progress += Math.floor(Math
                                            .random() * 10) + 5;
                                        if (progress >= 95) progress = 95;
                                        progressBar.style.width = progress +
                                            '%';
                                    }, 300);

                                    fetch('proses_update.php', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json'
                                            },
                                            body: JSON.stringify({
                                                versi_baru: data
                                                    .versi_baru,
                                                url: data.download_url
                                            })
                                        })
                                        .then(res => res.json())
                                        .then(resp => {
                                            clearInterval(interval);
                                            progressBar.style.width = '100%';
                                            if (!resp.success) throw new Error(
                                                resp.message);
                                            Swal.update({
                                                title: 'Update Berhasil!',
                                                html: 'Aplikasi berhasil diperbarui.',
                                                icon: 'success',
                                                showConfirmButton: true
                                            });
                                        })
                                        .catch(err => {
                                            clearInterval(interval);
                                            Swal.update({
                                                icon: 'error',
                                                title: 'Gagal Update!',
                                                text: err.message ||
                                                    'Terjadi kesalahan.',
                                                showConfirmButton: true
                                            });
                                        });
                                },
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false
                            });
                            return false;
                        }

                    }).then(result => {
                        if (result.isConfirmed && result.value.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Update berhasil diunduh dan diterapkan.',
                            }).then(() => location.reload());
                        }
                    });
                } else if (data.status === 'uptodate') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sudah Versi Terbaru',
                        html: `<b>Versi saat ini:</b> ${data.versi_saat_ini}`,
                        confirmButtonColor: '#28a745'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Mengecek',
                        text: data.message || 'Terjadi kesalahan.'
                    });
                }
            })
            .catch(() => {
                hasil.innerHTML = '';
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Terhubung',
                    text: 'Tidak bisa menghubungi server update.'
                });
            });
    });
    </script>
    <script>
    document.querySelectorAll('.color-box').forEach(box => {
        box.addEventListener('click', function() {
            // Reset semua box
            document.querySelectorAll('.color-box').forEach(b => {
                b.classList.remove('border-3', 'border-dark');
                b.classList.add('border');
            });
            // Tandai yang terpilih
            this.classList.remove('border');
            this.classList.add('border-3', 'border-dark');

            // Set nilai input tersembunyi dan picker
            const warna = this.dataset.warna;
            document.getElementById('warna_tema').value = warna;
            document.getElementById('colorPicker').value = warna;
        });
    });

    // Jika user pilih warna bebas di color picker
    document.getElementById('colorPicker').addEventListener('input', function() {
        const warna = this.value;
        document.getElementById('warna_tema').value = warna;

        // Reset semua box, karena warna bebas
        document.querySelectorAll('.color-box').forEach(b => {
            b.classList.remove('border-3', 'border-dark');
            b.classList.add('border');
        });
    });
    
    document.getElementById('btnLihatLog').addEventListener('click', function() {
    fetch('lihat_update_log.php')
        .then(res => res.text())
        .then(data => {
            document.querySelector('#logModal .modal-body').textContent = data || 'Belum ada log update.';
            var modal = new bootstrap.Modal(document.getElementById('logModal'));
            modal.show();
        })
        .catch(() => {
            document.querySelector('#logModal .modal-body').textContent = 'Gagal memuat log.';
            var modal = new bootstrap.Modal(document.getElementById('logModal'));
            modal.show();
        });
});

    </script>
</body>

</html>