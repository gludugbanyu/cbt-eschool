<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
check_login('admin');
include '../inc/dataadmin.php';
// Periksa apakah id_soal dan aksi ada di parameter GET
if (!isset($_GET['id_soal']) || !isset($_GET['aksi'])) {
    $_SESSION['error'] = 'ID Soal atau Aksi tidak ditemukan.';
    header('Location: soal.php');
    exit;
}

$id_soal = $_GET['id_soal'];
$aksi = $_GET['aksi'];

if ($aksi == 'nonaktif') {
    $status = 'Nonaktif';
    $query = "UPDATE soal SET status = '$status', token = NULL WHERE id_soal = '$id_soal'";
    
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['success'] = 'Soal berhasil dinonaktifkan.';
    } else {
        $_SESSION['error'] = 'Gagal menonaktifkan soal: ' . mysqli_error($koneksi);
    }

    header('Location: soal.php');
    exit;
}

// Untuk aksi AKTIF
if ($aksi == 'aktif') {
    $kode = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 5));
    $status = 'Aktif';

    // Ambil kode_soal dari tabel soal
    $sqlKode = mysqli_query($koneksi, "SELECT kode_soal FROM soal WHERE id_soal = '$id_soal'");
    if ($sqlKode && mysqli_num_rows($sqlKode) > 0) {
        $dataKode = mysqli_fetch_assoc($sqlKode);
        $kode_soal = $dataKode['kode_soal'];

        // Cek apakah ada butir soal
        $cekButir = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM butir_soal WHERE kode_soal = '$kode_soal'");
        $dataButir = mysqli_fetch_assoc($cekButir);
        if ($dataButir['total'] == 0) {
            $_SESSION['error'] = 'Soal tidak dapat diaktifkan karena belum memiliki butir soal.';
            header('Location: soal.php');
            exit;
        }

        // Ambil tipe_soal
        $sqlTipeSoal = mysqli_query($koneksi, "SELECT tipe_soal FROM butir_soal WHERE kode_soal = '$kode_soal' LIMIT 1");
        if ($sqlTipeSoal && mysqli_num_rows($sqlTipeSoal) > 0) {
            $dataTipeSoal = mysqli_fetch_assoc($sqlTipeSoal);
            $tipe_soal = $dataTipeSoal['tipe_soal'];

            // Ambil jawaban_benar dari tabel butir_soal
            $sqlKunci = mysqli_query($koneksi, "SELECT nomer_soal, jawaban_benar FROM butir_soal WHERE kode_soal = '$kode_soal' ORDER BY nomer_soal ASC");
            if ($sqlKunci) {
                if (mysqli_num_rows($sqlKunci) > 0) {
                    $kunci_array = [];
                    $nomor = 1;

                    while ($row = mysqli_fetch_assoc($sqlKunci)) {
                        if ($tipe_soal == 'menjodohkan') {
                            $jawaban = explode('|', $row['jawaban_benar']);
                            $opsi_string = '';
                            foreach ($jawaban as $jawaban_item) {
                                $opsi_parts = explode(':', $jawaban_item);
                                if (count($opsi_parts) == 2) {
                                    $opsi_string .= $opsi_parts[0] . ':' . $opsi_parts[1] . '|';
                                }
                            }
                            $opsi_string = rtrim($opsi_string, '|');
                            $kunci_array[] = '[' . $nomor . ':' . $opsi_string . ']';
                        } else {
                            $kunci_array[] = '[' . $nomor . ':' . $row['jawaban_benar'] . ']';
                        }
                        $nomor++;
                    }

                    $kunci_string = implode(',', $kunci_array);

                    // Update kolom kunci di tabel soal
                    $updateKunci = mysqli_query($koneksi, "UPDATE soal SET kunci = '$kunci_string' WHERE id_soal = '$id_soal'");
                    if (!$updateKunci) {
                        $_SESSION['error'] = 'Gagal memperbarui kolom kunci: ' . mysqli_error($koneksi);
                        header('Location: soal.php');
                        exit;
                    }

                    // ✅ Setelah semua sukses, barulah aktifkan soal
                    $updateStatus = mysqli_query($koneksi, "UPDATE soal SET status = 'Aktif', token = '$kode' WHERE id_soal = '$id_soal'");
                    if (!$updateStatus) {
                        $_SESSION['error'] = 'Gagal mengaktifkan soal: ' . mysqli_error($koneksi);
                        header('Location: soal.php');
                        exit;
                    }

                    $_SESSION['success'] = 'Status soal berhasil diperbarui.';
                    header('Location: soal.php');
                    exit;

                } else {
                    $_SESSION['error'] = 'Tidak ada data jawaban_benar pada butir_soal.';
                    header('Location: soal.php');
                    exit;
                }
            } else {
                $_SESSION['error'] = 'Query butir_soal gagal: ' . mysqli_error($koneksi);
                header('Location: soal.php');
                exit;
            }
        } else {
            $_SESSION['error'] = 'Gagal mengambil tipe_soal dari tabel butir_soal.';
            header('Location: soal.php');
            exit;
        }
    } else {
        $_SESSION['error'] = 'Gagal mengambil kode_soal dari tabel soal.';
        header('Location: soal.php');
        exit;
    }
}

header('Location: soal.php');
exit;
