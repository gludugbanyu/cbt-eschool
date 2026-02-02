<?php
session_start();
include '../koneksi/koneksi.php';
include '../inc/functions.php';
include '../inc/encrypt.php';
check_login('admin');
include '../inc/dataadmin.php';
require 'autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file_excel'])) {
    $allowed_types = [
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/csv'
    ];

    $file_type = $_FILES['file_excel']['type'];
    $file_name = $_FILES['file_excel']['name'];
    $tmp_file  = $_FILES['file_excel']['tmp_name'];

    // Ambil kode_soal dari input form
    $form_kode_soal = isset($_POST['kode_soal']) ? trim($_POST['kode_soal']) : '';


    if (in_array($file_type, $allowed_types)) {
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $reader = IOFactory::createReader(match(strtolower($ext)) {
            'csv'  => 'Csv',
            'xls'  => 'Xls',
            default => 'Xlsx'
        });

        try {
            $spreadsheet = $reader->load($tmp_file);
            $data = $spreadsheet->getActiveSheet()->toArray();

            if (count($data) < 2) {
                $_SESSION['import_error'] = "Data Excel kosong atau tidak lengkap.";
                header("Location: daftar_butir_soal.php?kode_soal=" . urlencode($form_kode_soal));
                exit;
            }

            // Validasi: semua baris harus punya kode_soal yang sama dengan form
            for ($i = 1; $i < count($data); $i++) {
                $excel_kode_soal = trim($data[$i][1]);
                if ($excel_kode_soal !== $form_kode_soal) {
                    $_SESSION['import_error'] = "Kode soal di Excel baris ke-" . ($i + 1) . " tidak sesuai dengan kode soal yang dipilih.";
                    header("Location: daftar_butir_soal.php?kode_soal=" . urlencode($form_kode_soal));
                    exit;
                }
            }

            $successCount = 0;
            $duplicateEntries = [];

            for ($i = 1; $i < count($data); $i++) {
                $nomer_soal     = intval($data[$i][0]);
                $kode_soal      = trim($data[$i][1]);
                $pertanyaan     = trim($data[$i][2]);
                $tipe_soal      = trim($data[$i][3]);
                $pilihan_1 = trim($data[$i][4] ?? '');
                $pilihan_2 = trim($data[$i][5] ?? '');
                $pilihan_3 = trim($data[$i][6] ?? '');
                $pilihan_4 = trim($data[$i][7] ?? '');
                $pilihan_5 = trim($data[$i][8] ?? ''); // boleh kosong kalau opsi 4
                
                $jawaban_benar = trim($data[$i][9] ?? '');
                $status_soal   = trim($data[$i][10] ?? '');
                


                $cek = $koneksi->prepare("SELECT COUNT(*) FROM butir_soal WHERE nomer_soal = ? AND kode_soal = ?");
                $cek->bind_param("is", $nomer_soal, $kode_soal);
                $cek->execute();
                $cek->bind_result($count);
                $cek->fetch();
                $cek->close();

                if ($count > 0) {
                    $duplicateEntries[] = "No. $nomer_soal (Kode: $kode_soal)";
                    continue;
                }

                $stmt = $koneksi->prepare("INSERT INTO butir_soal 
(nomer_soal, kode_soal, pertanyaan, tipe_soal, pilihan_1, pilihan_2, pilihan_3, pilihan_4, pilihan_5, jawaban_benar, status_soal)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("issssssssss",
$nomer_soal, 
$kode_soal, 
$pertanyaan, 
$tipe_soal,
$pilihan_1, 
$pilihan_2, 
$pilihan_3, 
$pilihan_4,
$pilihan_5,
$jawaban_benar, 
$status_soal
);

                if ($stmt->execute()) {
                    $successCount++;
                }
                $stmt->close();
            }

            $_SESSION['import_result'] = [
                'successCount' => $successCount,
                'failCount' => count($duplicateEntries),
                'duplicates' => $duplicateEntries
            ];

            header("Location: daftar_butir_soal.php?kode_soal=" . urlencode($form_kode_soal));
            exit;

        } catch (Exception $e) {
            $_SESSION['import_error'] = "Terjadi kesalahan saat membaca file Excel: " . $e->getMessage();
            header("Location: daftar_butir_soal.php?kode_soal=" . urlencode($form_kode_soal));
            exit;
        }

    } else {
        $_SESSION['import_error'] = "Format file tidak valid!";
        header("Location: daftar_butir_soal.php?kode_soal=" . urlencode($form_kode_soal));
        exit;
    }
}
