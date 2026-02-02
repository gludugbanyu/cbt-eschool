-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 02, 2026 at 11:28 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cbt_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `nama_admin` text NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `nama_admin`, `password`, `created_at`) VALUES
(1, 'gludug', 'Admin', '$2y$10$/RMEibFbfc45T0gmAM7H4u9CGhjKCkjFe/ZnQnTS6qc0JFEbOsaoW', '2025-05-05 09:13:31');

-- --------------------------------------------------------

--
-- Table structure for table `butir_soal`
--

CREATE TABLE `butir_soal` (
  `id_soal` int(11) NOT NULL,
  `nomer_soal` int(11) NOT NULL,
  `kode_soal` varchar(50) NOT NULL,
  `pertanyaan` text NOT NULL,
  `tipe_soal` enum('Pilihan Ganda','Pilihan Ganda Kompleks','Benar/Salah','Uraian','Menjodohkan') NOT NULL,
  `pilihan_1` varchar(255) DEFAULT NULL,
  `pilihan_2` varchar(255) DEFAULT NULL,
  `pilihan_3` varchar(255) DEFAULT NULL,
  `pilihan_4` varchar(255) DEFAULT NULL,
  `pilihan_5` varchar(255) NOT NULL,
  `jawaban_benar` text DEFAULT NULL,
  `status_soal` enum('Aktif','Tidak Aktif') DEFAULT 'Aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `butir_soal`
--

INSERT INTO `butir_soal` (`id_soal`, `nomer_soal`, `kode_soal`, `pertanyaan`, `tipe_soal`, `pilihan_1`, `pilihan_2`, `pilihan_3`, `pilihan_4`, `pilihan_5`, `jawaban_benar`, `status_soal`, `created_at`) VALUES
(1, 1, 'MAT9-01', 'Sampah anorganik lebih lama terurai dibandingkan dengan sampah organik. Waktu dekomposisi popok sekali pakai lebih lama dari plastik, namun kurang dari kulit sintetis. Berapa waktu dekomposisi yang mungkin dari popok sekali pakai?', 'Pilihan Ganda', '100 tahun', '250 tahun', '375 tahun', '475 tahun', '', 'pilihan_4', 'Aktif', '2025-06-03 23:55:37'),
(2, 2, 'MAT9-01', 'Pilih Benar atau Salah pada setiap pernyataan berikut!', 'Benar/Salah', 'Panjang AB = Panjang CD', 'Panjang PQ = Panjang SR', 'Jarak Q ke S = Jarak B ke C', '', '', 'Benar|Benar|Salah', 'Aktif', '2025-06-03 23:55:37'),
(3, 3, 'MAT9-01', 'Suatu kali, PT Suka-Suka Kalian mendapatkan pesanan 30 unit tenda dengan bentuk dan ukuran seperti di atas. Waktu penyelesaian yang diperlukan untuk memenuhi seluruh pesanan adalah 20 hari kerja.', 'Benar/Salah', 'Waktu rata-rata pembuatan 3 buah tenda adalah 2 hari.', 'Waktu penyelesaian semua pesanan bisa tepat waktu jika dalam sehari dihasilkan sebuah tenda.', 'Jika pesanan bertambah 5 tenda, lama penyelesaian bertambah 2 hari.', 'Jika dalam sehari dapat dihasilkan 2 tenda, waktu penyelesaian seluruh pesanan menjadi 5 hari lebih cepat.', '', 'Salah|Salah|Salah|Benar', 'Aktif', '2025-06-03 23:55:37'),
(4, 4, 'MAT9-01', 'Biskuit merupakan camilan yang banyak digemari sebagai pelengkap minum teh setiap waktu. Berikut komposisi 2 jenis biskuit yang sering dijual di pasaran:', 'Pilihan Ganda Kompleks', 'Komposisi protein Biskuit Lezat adalah 0,02 bagian', 'Komposisi natrium Biskuit Sehat adalah 0,01 bagian', 'Komposisi lemak jenuh Biskuit Lezat adalah 0,16 bagian', 'Komposisi lemak jenuh Biskuit Sehat adalah 0,02 bagian', '', 'pilihan_1,pilihan_3', 'Aktif', '2025-06-03 23:55:37'),
(5, 5, 'MAT9-01', 'Pasangkan pernyataan di kolom kiri dengan jawaban yang tepat di kolom kanan dengan menulis huruf di depan nomor yang sesuai!', 'Menjodohkan', '', '', '', '', '', 'Bilangan prima antara 10 dan 15:11 dan 13|Volume kubus dengan rusuk 4 cm:64 cm|Luas persegi dengan sisi 6 cm:36 cm|Nilai x dari 2x+5=19:7', 'Aktif', '2025-06-03 23:55:37'),
(6, 6, 'MAT9-01', 'Faktor persekutuan terbesar (FPB) dari 12 dan 18', 'Uraian', '', '', '', '', '', '6', 'Aktif', '2025-06-03 23:55:37'),
(7, 1, 'pilihan 5', 'Soal 1', 'Pilihan Ganda', 'sdf', 'asdads', 'asd', 'asd', 'asd', 'pilihan_2', 'Aktif', '2026-02-02 06:31:29'),
(8, 2, 'pilihan 5', 'asdasd', 'Pilihan Ganda Kompleks', 'asd', 'asd', 'asd', 'asd', 'asd', 'pilihan_4,pilihan_5', 'Aktif', '2026-02-02 06:31:46'),
(9, 3, 'pilihan 5', 'sadasd', 'Benar/Salah', 'asdasd', 'asdasd', 'Pernyataan 3', '', '', 'Benar|Salah|Salah', 'Aktif', '2026-02-02 06:32:11'),
(10, 4, 'pilihan 5', 'asasdasd', 'Menjodohkan', NULL, NULL, NULL, NULL, '', 'asdsad 1:asdads 1|asdasd 2:asdasd 2', 'Aktif', '2026-02-02 06:32:24'),
(11, 5, 'pilihan 5', 'asdasd', 'Uraian', NULL, NULL, NULL, NULL, '', 'asd', 'Aktif', '2026-02-02 06:32:31'),
(12, 6, 'pilihan 5', 'zxczxczxc', 'Pilihan Ganda', 'sdf', 'zxc', 'zxc', 'zxc', 'xz', 'pilihan_2', 'Aktif', '2026-02-02 06:39:31');

-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

CREATE TABLE `chat` (
  `id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `pesan` text NOT NULL,
  `waktu` datetime DEFAULT current_timestamp(),
  `deleted` tinyint(1) DEFAULT 0,
  `role` enum('siswa','admin') NOT NULL DEFAULT 'siswa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faq`
--

CREATE TABLE `faq` (
  `id` int(11) NOT NULL,
  `question` text NOT NULL,
  `answer` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faq`
--

INSERT INTO `faq` (`id`, `question`, `answer`) VALUES
(1, 'cara Ujian?', 'Siswa dapat mengikuti ujian dengan login ke dashboard, memilih menu \'Ujian\', dan mengikuti instruksi yang tersedia.'),
(2, 'Lupa password', 'Hubungi admin atau Guru untuk reset password'),
(3, 'jawaban hilang', 'Jika koneksi terputus jawaban masih tersimpan dan kamu bisa melanjutkan ujian lagi. Silakan hubungi guru atau admin untuk informasi lebih lanjut.'),
(4, 'hasil ujian', 'Setelah ujian selesai, hasil dapat dilihat pada menu \'Nilai\' di dashboard siswa.'),
(5, 'Perangkat', 'Ujian dapat diakses melalui komputer, laptop, atau perangkat mobile dengan koneksi internet yang stabil.'),
(6, 'Jaringan Terputus', 'Silakan buka kembali aplikasi ujian seperti biasa,  Jika tidak bisa masuk atau muncul pesan error, segera hubungi pengawas atau admin ujian untuk reset login.'),
(13, 'Reset Login.', 'hubungi pengawas atau admin ujian untuk reset login.'),
(20, 'Nilai tersembunyi', 'ya, admin bisa menyembunyikan maupun menampilkan nilai, agar siswa tidak bisa melihat jawaban benar.'),
(21, 'Apa itu CBT?', 'CBT adalah Computer-Based Test atau ujian berbasis komputer.');

-- --------------------------------------------------------

--
-- Table structure for table `jawaban_siswa`
--

CREATE TABLE `jawaban_siswa` (
  `id_jawaban` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `nama_siswa` text NOT NULL,
  `kode_soal` varchar(50) NOT NULL,
  `total_soal` text NOT NULL,
  `jawaban_siswa` text DEFAULT NULL,
  `waktu_sisa` text NOT NULL,
  `waktu_dijawab` timestamp NOT NULL DEFAULT current_timestamp(),
  `status_ujian` enum('Aktif','Non-Aktif','Selesai') DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jawaban_siswa`
--

INSERT INTO `jawaban_siswa` (`id_jawaban`, `id_siswa`, `nama_siswa`, `kode_soal`, `total_soal`, `jawaban_siswa`, `waktu_sisa`, `waktu_dijawab`, `status_ujian`) VALUES
(1, 1, 'Jokowi JK', 'MAT9-01', '', '[6:5][1:pilihan_4][4:pilihan_2,pilihan_3][3:Salah|Salah|Salah|Benar][2:Benar|Salah|Benar][5:Bilangan prima antara 10 dan 15:11 dan 13|Volume kubus dengan rusuk 4 cm:64 cm|Luas persegi dengan sisi 6 cm:36 cm|Nilai x dari 2x+5=19:7]', '56', '2025-06-10 05:12:00', 'Selesai'),
(6, 5, 'Corbuzier', 'MAT9-01', '', '[4:pilihan_1,pilihan_3][5:Bilangan prima antara 10 dan 15:11 dan 13|Volume kubus dengan rusuk 4 cm:64 cm|Luas persegi dengan sisi 6 cm:36 cm|Nilai x dari 2x+5=19:7][1:pilihan_4][2:Benar|Benar|Salah][6:7][3:Salah|Salah|Salah|Benar]', '98', '2026-02-02 04:51:35', 'Selesai'),
(77, 5, 'Corbuzier', 'pilihan 5', '', '[6:pilihan_2][1:pilihan_2][2:pilihan_4,pilihan_5][3:Benar|Salah|Salah][4:asdsad 1:asdads 1|asdasd 2:asdasd 2][5:asd]', '99', '2026-02-02 07:31:36', 'Aktif'),
(101, 4, 'Deddy ', 'pilihan 5', '', '[5:sdsdasd][2:pilihan_2][4:asdasd 2:asdads 1][1:pilihan_3][3:Salah][6:pilihan_1]', '104', '2026-02-02 08:24:10', 'Non-Aktif');

-- --------------------------------------------------------

--
-- Table structure for table `nilai`
--

CREATE TABLE `nilai` (
  `id_nilai` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `nama_siswa` text NOT NULL,
  `kode_soal` varchar(250) NOT NULL,
  `total_soal` int(11) NOT NULL,
  `jawaban_benar` varchar(100) NOT NULL,
  `jawaban_salah` varchar(100) NOT NULL,
  `jawaban_kurang` varchar(100) NOT NULL,
  `jawaban_siswa` text NOT NULL,
  `kunci` text NOT NULL,
  `nilai` decimal(5,2) NOT NULL,
  `nilai_uraian` decimal(5,2) DEFAULT 0.00,
  `detail_uraian` text NOT NULL,
  `tanggal_ujian` datetime NOT NULL,
  `status_penilaian` enum('otomatis','perlu_dinilai','selesai') DEFAULT 'otomatis'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nilai`
--

INSERT INTO `nilai` (`id_nilai`, `id_siswa`, `nama_siswa`, `kode_soal`, `total_soal`, `jawaban_benar`, `jawaban_salah`, `jawaban_kurang`, `jawaban_siswa`, `kunci`, `nilai`, `nilai_uraian`, `detail_uraian`, `tanggal_ujian`, `status_penilaian`) VALUES
(3, 5, 'Corbuzier', 'MAT9-01', 6, '5', '0', '1', '[4:pilihan_1,pilihan_3][5:Bilangan prima antara 10 dan 15:11 dan 13|Volume kubus dengan rusuk 4 cm:64 cm|Luas persegi dengan sisi 6 cm:36 cm|Nilai x dari 2x+5=19:7][1:pilihan_4][2:Benar|Benar|Salah][6:7][3:Salah|Salah|Salah|Benar]', '[1:pilihan_4],[2:Benar|Benar|Salah],[3:Salah|Salah|Salah|Benar],[4:pilihan_1,pilihan_3],[5:Bilangan prima antara 10 dan 15:11 dan 13|Volume kubus dengan rusuk 4 cm:64 cm|Luas persegi dengan sisi 6 cm:36 cm|Nilai x dari 2x+5=19:7],[6:6]', 83.33, 12.93, '[6:12.93]', '2026-02-02 15:08:11', 'perlu_dinilai');

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan`
--

CREATE TABLE `pengaturan` (
  `id` int(11) NOT NULL DEFAULT 1,
  `nama_aplikasi` varchar(100) DEFAULT 'CBT Siswa',
  `logo_sekolah` varchar(255) DEFAULT '',
  `warna_tema` varchar(10) DEFAULT '#0d6efd',
  `waktu_sinkronisasi` int(11) DEFAULT 60,
  `sembunyikan_nilai` tinyint(1) DEFAULT 0,
  `login_ganda` enum('izinkan','blokir') DEFAULT 'blokir',
  `chat` varchar(100) NOT NULL,
  `versi_aplikasi` varchar(20) DEFAULT '1.0.0',
  `izinkan_lanjut_ujian` enum('ya','tidak') NOT NULL DEFAULT 'tidak'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengaturan`
--

INSERT INTO `pengaturan` (`id`, `nama_aplikasi`, `logo_sekolah`, `warna_tema`, `waktu_sinkronisasi`, `sembunyikan_nilai`, `login_ganda`, `chat`, `versi_aplikasi`, `izinkan_lanjut_ujian`) VALUES
(1, 'CBT-Eschool', 'logo_1747650742.png', '#2f90c1', 60, 1, 'izinkan', 'izinkan', '2.0.0', 'tidak');

-- --------------------------------------------------------

--
-- Table structure for table `profil`
--

CREATE TABLE `profil` (
  `id` int(11) NOT NULL,
  `encrypt` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profil`
--

INSERT INTO `profil` (`id`, `encrypt`) VALUES
(1, 'JmNvcHk7IDIwMjUgR2x1ZHVnIGNvZGVsaXRl');

-- --------------------------------------------------------

--
-- Table structure for table `siswa`
--

CREATE TABLE `siswa` (
  `id_siswa` int(11) NOT NULL,
  `nama_siswa` varchar(100) NOT NULL,
  `password` text NOT NULL,
  `username` varchar(200) NOT NULL,
  `kelas` varchar(100) NOT NULL,
  `rombel` varchar(100) NOT NULL,
  `status` text NOT NULL DEFAULT 'Nonaktif',
  `session_token` varchar(255) NOT NULL,
  `last_activity` datetime DEFAULT NULL,
  `page_url` text NOT NULL,
  `force_logout` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `siswa`
--

INSERT INTO `siswa` (`id_siswa`, `nama_siswa`, `password`, `username`, `kelas`, `rombel`, `status`, `session_token`, `last_activity`, `page_url`, `force_logout`) VALUES
(1, 'Jokowi JK', 'h7fV3os6WcZ+hNwtoIN5Si9hbEVndnEzRmNodzJlSktYZ2hVMUE9PQ==', '123456', '9', 'A', 'Nonaktif', 'fc41c4223036fdac03b67df191c321e5b10f6d668a1dbbe16318f934a9fe5b5c', '2025-06-10 12:15:20', 'http://localhost/cbt-eschool/siswa/hasil.php', 0),
(2, 'Prabowo', 'm9MaPSetPwkYW68qNsWwUlUrOW9HNWFlRzJRVENVVi9xNW9vN0E9PQ==', '123457', '9', 'B', 'Nonaktif', 'b21f01ccbda5fcc557d693a0c9466a40afe9e3c1265cf59b2da08061f8edec18', '2025-05-24 00:41:14', 'http://localhost/cbt-eschool/siswa/chat.php', 0),
(3, 'Agum Gumelar', '5mv6Upz6eP/GpQrkjcebOHcyOFNxV2RRT2xQdkVxRUh0ZVZ0d3c9PQ==', '123458', '9', 'C', 'nonaktif', '', '2025-05-24 22:22:37', 'http://localhost/cbt-eschool/siswa/ujian.php', 0),
(4, 'Deddy ', '5uKDYI7JoYmjpgBTg8LxUi9YZ2dIVGFucU5FM2wySDYvcmFVQXc9PQ==', '123459', '9', 'D', 'nonaktif', '', '2026-02-02 16:29:04', 'http://localhost/cbt-eschool/siswa/dashboard.php', 0),
(5, 'Corbuzier', '/SbMMmTczf7Ry0qUn/f6XmhpM1BYS0l6S1F0cmlHSlB3ZjE1cEE9PQ==', '123461', '9', 'E', 'Nonaktif', '34130709a9675947f5e4593bbb611deb5524e35d1f70d321a675b9103acbae42', '2026-02-02 17:28:00', 'http://localhost/cbt-eschool/siswa/mulaiujian.php', 0),
(13, 'Erina', 'FQOm8MYUIes79E36AQv1AU5VMVdUanhIaTBVTURVS0hXckFRUXc9PQ==', '721731', '9', 'A', 'Nonaktif', '', '0000-00-00 00:00:00', 'http://localhost/cbt-eschool/siswa/dashboard.php', 1),
(14, 'Phoebe', 'Dwl3VYW4ysVVEjO67sy6QmdYb2h0NjNFWjhlV3ViamtWY01hc0E9PQ==', '122345', '7', 'C', 'Nonaktif', '9a394ef4a7893401d0185c9a489d7f17483e8e47a105da3ee7174307346d701c', '2025-05-25 21:08:44', 'http://localhost/cbt-eschool/siswa/preview_hasil.php?id_siswa=14&kode_soal=BINDO7-1', 0),
(15, 'Zevan', 'mG5EAQl0ttZQFaqBXlYCgGdMVkdTMjNQQXZ3VmRKdmFNbTJBeEE9PQ==', '257174', '7', 'D', 'Nonaktif', '', '2025-05-25 00:13:02', 'http://localhost/cbt-eschool/siswa/ujian.php', 0),
(16, 'Denny', 'N2ugxO2xwJR74bjbZQv19nYrMFVFbi9JTEk5MFNDeVdITWxmM0E9PQ==', '641343', '7', 'F', 'Nonaktif', '', '2025-05-25 14:58:48', 'http://localhost/cbt-eschool/siswa/hasil.php', 0),
(17, 'Lintar', 'CJ7fgqg1+lzEgNuqTQwdCUtBeHlsdXdGU3FabGdhQ3lQbXQ2NlE9PQ==', '252743', '9', 'D', 'Nonaktif', '', '2025-05-28 13:42:55', 'http://localhost/cbt-eschool/siswa/ujian.php', 0),
(18, 'andy', '5IiPhwyWU7/GiyYe622atFErOVViUmNXOXRheVk0Z2U1V0tiK2c9PQ==', '876543', '8', 'D', 'Nonaktif', '', '0000-00-00 00:00:00', 'http://localhost/cbt-eschool/siswa/dashboard.php', 1),
(20, 'Robi', 'XpqGGiL6DfhOGnPqcV9EnUdLdTlEbzdwN1pyVE5wb2FJSStLdUE9PQ==', '252645', '9', 'G', 'Nonaktif', '', '2025-05-24 22:26:17', 'http://localhost/cbt-eschool/siswa/dashboard.php', 0),
(21, 'Intan', 'Ya+NHgRRNME9cYTmSRYUz2sxMS9tczlZMGJDaFd0NkN5TzErV0E9PQ==', '1241322', '7', 'B', 'Nonaktif', '', '0000-00-00 00:00:00', 'http://localhost/cbt-eschool/siswa/dashboard.php', 1);

-- --------------------------------------------------------

--
-- Table structure for table `skor_game`
--

CREATE TABLE `skor_game` (
  `id` int(11) NOT NULL,
  `id_siswa` int(11) DEFAULT NULL,
  `nama_game` varchar(50) DEFAULT NULL,
  `skor` int(11) DEFAULT 0,
  `waktu` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `soal`
--

CREATE TABLE `soal` (
  `id_soal` int(11) NOT NULL,
  `kode_soal` varchar(200) NOT NULL,
  `nama_soal` varchar(255) NOT NULL,
  `mapel` varchar(100) NOT NULL,
  `kelas` varchar(50) NOT NULL,
  `waktu_ujian` int(11) DEFAULT 60,
  `tanggal` date DEFAULT curdate(),
  `status` text NOT NULL DEFAULT 'Nonaktif',
  `tampilan_soal` varchar(10) NOT NULL,
  `kunci` text NOT NULL,
  `token` varchar(6) NOT NULL,
  `jumlah_opsi` tinyint(1) NOT NULL DEFAULT 4,
  `tampil_tombol_selesai` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `soal`
--

INSERT INTO `soal` (`id_soal`, `kode_soal`, `nama_soal`, `mapel`, `kelas`, `waktu_ujian`, `tanggal`, `status`, `tampilan_soal`, `kunci`, `token`, `jumlah_opsi`, `tampil_tombol_selesai`) VALUES
(1, 'MAT9-01', 'Matematika 9', 'Matematika', '9', 120, '2025-06-05', 'Nonaktif', 'Acak', '[1:pilihan_4],[2:Benar|Benar|Salah],[3:Salah|Salah|Salah|Benar],[4:pilihan_1,pilihan_3],[5:Bilangan prima antara 10 dan 15:11 dan 13|Volume kubus dengan rusuk 4 cm:64 cm|Luas persegi dengan sisi 6 cm:36 cm|Nilai x dari 2x+5=19:7],[6:6]', '', 4, 10),
(3, 'pilihan 5', 'pilihan 5', 'SENI RUPA', '9', 120, '2026-02-02', 'Aktif', 'Acak', '[1:pilihan_2],[2:pilihan_4,pilihan_5],[3:Benar|Salah|Salah],[4:asdsad 1:asdads 1|asdasd 2:asdasd 2],[5:asd],[6:pilihan_2]', 'QKZDC', 5, 109);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `butir_soal`
--
ALTER TABLE `butir_soal`
  ADD PRIMARY KEY (`id_soal`);

--
-- Indexes for table `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jawaban_siswa`
--
ALTER TABLE `jawaban_siswa`
  ADD PRIMARY KEY (`id_jawaban`),
  ADD UNIQUE KEY `id_jawaban` (`id_jawaban`),
  ADD UNIQUE KEY `unik_jawaban` (`id_siswa`,`kode_soal`),
  ADD KEY `kode_soal` (`kode_soal`);

--
-- Indexes for table `nilai`
--
ALTER TABLE `nilai`
  ADD PRIMARY KEY (`id_nilai`),
  ADD UNIQUE KEY `unique_siswa_soal` (`id_siswa`,`kode_soal`);

--
-- Indexes for table `pengaturan`
--
ALTER TABLE `pengaturan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `profil`
--
ALTER TABLE `profil`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id_siswa`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `skor_game`
--
ALTER TABLE `skor_game`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_siswa` (`id_siswa`);

--
-- Indexes for table `soal`
--
ALTER TABLE `soal`
  ADD PRIMARY KEY (`id_soal`),
  ADD UNIQUE KEY `kode_soal` (`kode_soal`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `butir_soal`
--
ALTER TABLE `butir_soal`
  MODIFY `id_soal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `chat`
--
ALTER TABLE `chat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `faq`
--
ALTER TABLE `faq`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `jawaban_siswa`
--
ALTER TABLE `jawaban_siswa`
  MODIFY `id_jawaban` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=183;

--
-- AUTO_INCREMENT for table `nilai`
--
ALTER TABLE `nilai`
  MODIFY `id_nilai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id_siswa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `skor_game`
--
ALTER TABLE `skor_game`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `soal`
--
ALTER TABLE `soal`
  MODIFY `id_soal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `skor_game`
--
ALTER TABLE `skor_game`
  ADD CONSTRAINT `skor_game_ibfk_1` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
