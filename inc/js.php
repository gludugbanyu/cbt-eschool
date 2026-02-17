<?php
$ujian_terdekat = mysqli_query($koneksi, "
    SELECT * FROM soal 
    WHERE tanggal > NOW() 
    ORDER BY tanggal ASC 
    LIMIT 5
");

$signature = '';

if (mysqli_num_rows($ujian_terdekat) > 0) {
    mysqli_data_seek($ujian_terdekat, 0);
    while ($row = mysqli_fetch_assoc($ujian_terdekat)) {
        $signature .= $row['kode_soal'] . $row['tanggal'];
    }
    mysqli_data_seek($ujian_terdekat, 0);
}

$signature = md5($signature);
?>
<div id="toast-container" data-signature="<?= $signature ?>">
    <?php 
if (mysqli_num_rows($ujian_terdekat) > 0):
    mysqli_data_seek($ujian_terdekat, 0); 
    while ($ujian = mysqli_fetch_assoc($ujian_terdekat)): 
?>
    <div class="toast align-items-center text-white bg-primary border-0 mb-2 opacity-0" role="alert">

        <div class="d-flex">
            <div class="toast-body">
                <i class="far fa-calendar-check me-2"></i>
                Ujian <?= $ujian['kode_soal']; ?> dimulai pada
                <?= date('d M Y', strtotime($ujian['tanggal'])); ?>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>

    </div>
    <?php 
    endwhile;
endif;
?>
</div>
<footer class="footer mt-auto py-3 bg-dark">
    <div class="container-fluid">
        <div class="row text-grey">
            <div class="col-6 text-start">
                <p class="mb-0">
                    <a href="#" id="enc" style="color:grey;text-decoration:none;"></a>
                </p>
            </div>
        </div>
    </div>
</footer>
<?php
                                                $q = mysqli_query($koneksi, "SELECT * FROM pengaturan WHERE id = 1");
                                                $data = mysqli_fetch_assoc($q);
                                                ?>
<script src="../assets/adminkit/static/js/app.js"></script>
<script src="../assets/js/jquery-3.6.0.min.js"></script>
<script src="../assets/js/sweetalert.js"></script>
<script src="../assets/datatables/datatables.js"></script>
<audio id="notif-sound" src="../assets/notif.mp3" preload="auto"></audio>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Ambil semua elemen dengan data-bs-toggle="collapse"
    document.querySelectorAll('[data-bs-toggle="collapse"]')
        .forEach(item => {
            item.addEventListener('click', function() {
                // Ambil elemen target collapse berdasarkan href
                var target = document.querySelector(item.getAttribute('href'));
                var chevronIcon = item.querySelector('.fa-chevron-down');

                // Menambahkan event listener untuk ketika submenu dibuka
                target.addEventListener('shown.bs.collapse', function() {
                    if (chevronIcon) {
                        chevronIcon.classList.remove('fa-chevron-down');
                        chevronIcon.classList.add('fa-chevron-up');
                    }
                });

                // Menambahkan event listener untuk ketika submenu ditutup
                target.addEventListener('hidden.bs.collapse', function() {
                    if (chevronIcon) {
                        chevronIcon.classList.remove('fa-chevron-up');
                        chevronIcon.classList.add('fa-chevron-down');
                    }
                });
            });
        });
});

document.addEventListener("DOMContentLoaded", function() {
    var base64Text = "<?php echo $encryptedText; ?>";
    var versiSaya = "<?= $data['versi_aplikasi'] ?? '1.0.0' ?>"; // ambil dari database

    if (base64Text) {
        var decodedText = atob(base64Text);
        document.getElementById("enc").innerHTML = decodedText + " v." + versiSaya;
    } else {
        document.getElementById("enc").innerHTML = "v." + versiSaya;
    }
});

document.addEventListener("DOMContentLoaded", function() {
    var base64Text = "<?php echo $encryptedText; ?>";
    var versiSaya = "<?= $data['versi_aplikasi'] ?? '1.0.0' ?>"; // ambil dari database

    if (base64Text) {
        var decodedText = atob(base64Text);
        document.getElementById("encr").innerHTML = decodedText + " v." + versiSaya;
    } else {
        document.getElementById("encr").innerHTML = "v." + versiSaya;
    }
});

function checkIfEncDeleted() {
    var encElement = document.getElementById("enc");

    if (!encElement) {
        window.location.href = "../error_page.php";
    }
}
setInterval(checkIfEncDeleted, 500);

document.addEventListener('DOMContentLoaded', function() {

    const sound = document.getElementById('notif-sound');
    const container = document.getElementById('toast-container');
    const toasts = document.querySelectorAll('#toast-container .toast');

    if (!toasts.length) return;

    const currentSignature = container.dataset.signature;
    const savedSignature = sessionStorage.getItem('toastSignature');

    // 🔁 Kalau sama, jangan tampil lagi
    if (savedSignature === currentSignature) return;

    // Simpan signature baru
    sessionStorage.setItem('toastSignature', currentSignature);

    // 🎨 Random warna bootstrap
    const bgClasses = [
        'bg-primary', 'bg-success', 'bg-danger',
        'bg-warning', 'bg-info', 'bg-dark', 'bg-secondary'
    ];

    function getRandomBg() {
        return bgClasses[Math.floor(Math.random() * bgClasses.length)];
    }

    toasts.forEach((toastEl, index) => {

        // Ganti warna random
        toastEl.classList.remove(...bgClasses);
        toastEl.classList.add(getRandomBg());

        setTimeout(() => {

            toastEl.classList.remove('opacity-0');

            const toast = new bootstrap.Toast(toastEl, {
                delay: 5000
            });

            toast.show();

        }, index * 1500);

    });

    // 🔊 Sound
    if (sound) {
        sound.currentTime = 0;
        sound.play().catch(() => {});
    }

});
</script>
<script>
document.querySelectorAll('.btnLogout').forEach(function(el) {
    el.addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Yakin ingin logout?',
            text: "Anda akan keluar dari sesi ini.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Logout',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'logout.php';
            }
        });
    });
});
</script>