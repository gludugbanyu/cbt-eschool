<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
// Periksa apakah id_soal dan aksi ada di parameter GET
if (!isset($_GET['id_soal']) ) {
    $_SESSION['error'] = 'ID Soal atau Aksi tidak ditemukan.';
    header('Location: soal.php');
    exit;
}


$id_soal = $_GET['id_soal'];

// Ambil data soal utama
$query_soal = mysqli_query($koneksi, "SELECT * FROM soal WHERE id_soal='$id_soal'");
$data_soal = mysqli_fetch_assoc($query_soal);

// Cek jika soal tidak ditemukan
if (!$data_soal) {
    $_SESSION['error'] = 'Soal dengan ID tersebut tidak ditemukan.';
    header('Location: soal.php');
    exit;
}
if ($data_soal['status'] == 'Nonaktif') {
    die('
    <!DOCTYPE html>
    <html>
    <head>
        <title>Peringatan</title>
        <script src="../assets/js/sweetalert.js"></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: "warning",
                title: "Tidak Bisa Perbarui Token!",
                text: "Soal ini Belum Aktif!",
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                window.location.href = "soal.php";
            });
        </script>
    </body>
    </html>
    ');
} else {  // Kondisi lain jika soal statusnya aktif atau jika ada logika lain
    $kode = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 5));
    $status = 'Aktif';
    $query = "UPDATE soal SET status = '$status', token = '$kode' WHERE id_soal = '$id_soal'";
    
    // Eksekusi query
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['success'] = 'Token berhasil diperbarui!';
        header('Location: soal.php');
        exit;
    } else {
        $_SESSION['error'] = 'Terjadi kesalahan saat mengupdate data soal.';
        header('Location: soal.php');
        exit;
    }
}
?>
