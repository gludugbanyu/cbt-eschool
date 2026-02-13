-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 04, 2026 at 08:29 AM
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('admin','editor') NOT NULL DEFAULT 'editor'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `nama_admin`, `password`, `created_at`, `role`) VALUES
(1, 'gludug', 'Gludug', '$2y$10$/RMEibFbfc45T0gmAM7H4u9CGhjKCkjFe/ZnQnTS6qc0JFEbOsaoW', '2025-05-05 09:13:31', 'admin'),
(5, 'gusriki', 'Riki Nur Hidayat', '$2y$10$vKppA/picJoSicRRmnNgguiexajiViyWB5Gml3nDhCGtkoaHDgrN6', '2026-02-04 04:05:00', 'editor');

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
(1, 1, 'MAT9-01', 'Sampah anorganik lebih lama terurai dibandingkan dengan sampah organik. Waktu dekomposisi popok sekali pakai lebih lama dari plastik, namun kurang dari kulit sintetis. Berapa waktu dekomposisi yang mungkin dari popok sekali pakai?', 'Pilihan Ganda', '100 tahun', '<p>250 tahun</p><p><img style=\"width: 1100px;\" src=\"../gambar/6982ba77a9a26.png\"></p>', '375 tahun', '475 tahun', '', 'pilihan_4', 'Aktif', '2025-06-03 23:55:37'),
(2, 2, 'MAT9-01', 'Pilih Benar atau Salah pada setiap pernyataan berikut!', 'Benar/Salah', 'Panjang AB = Panjang CD', 'Panjang PQ = Panjang SR', 'Jarak Q ke S = Jarak B ke C', '', '', 'Benar|Benar|Salah', 'Aktif', '2025-06-03 23:55:37'),
(3, 3, 'MAT9-01', 'Suatu kali, PT Suka-Suka Kalian mendapatkan pesanan 30 unit tenda dengan bentuk dan ukuran seperti di atas. Waktu penyelesaian yang diperlukan untuk memenuhi seluruh pesanan adalah 20 hari kerja.', 'Benar/Salah', 'Waktu rata-rata pembuatan 3 buah tenda adalah 2 hari.', 'Waktu penyelesaian semua pesanan bisa tepat waktu jika dalam sehari dihasilkan sebuah tenda.', 'Jika pesanan bertambah 5 tenda, lama penyelesaian bertambah 2 hari.', 'Jika dalam sehari dapat dihasilkan 2 tenda, waktu penyelesaian seluruh pesanan menjadi 5 hari lebih cepat.', '', 'Salah|Salah|Salah|Benar', 'Aktif', '2025-06-03 23:55:37'),
(4, 4, 'MAT9-01', 'Biskuit merupakan camilan yang banyak digemari sebagai pelengkap minum teh setiap waktu. Berikut komposisi 2 jenis biskuit yang sering dijual di pasaran:', 'Pilihan Ganda Kompleks', 'Komposisi protein Biskuit Lezat adalah 0,02 bagian', 'Komposisi natrium Biskuit Sehat adalah 0,01 bagian', 'Komposisi lemak jenuh Biskuit Lezat adalah 0,16 bagian', 'Komposisi lemak jenuh Biskuit Sehat adalah 0,02 bagian', '', 'pilihan_1,pilihan_3', 'Aktif', '2025-06-03 23:55:37'),
(5, 5, 'MAT9-01', 'Pasangkan pernyataan di kolom kiri dengan jawaban yang tepat di kolom kanan dengan menulis huruf di depan nomor yang sesuai!', 'Menjodohkan', '', '', '', '', '', 'Bilangan prima antara 10 dan 15:11 dan 13|Volume kubus dengan rusuk 4 cm:64 cm|Luas persegi dengan sisi 6 cm:36 cm|Nilai x dari 2x+5=19:7', 'Aktif', '2025-06-03 23:55:37'),
(6, 6, 'MAT9-01', 'Faktor persekutuan terbesar (FPB) dari 12 dan 18...', 'Uraian', '', '', '', '', '', '6', 'Aktif', '2025-06-03 23:55:37'),
(90, 1, 'IPS9-01', 'Apa perbedaan utama antara sistem ekonomi kapitalis dan sosialis?&nbsp;&nbsp;', 'Pilihan Ganda', 'Sistem ekonomi kapitalis berdasarkan pada prinsip kebebasan individu, \r\nsedangkan sistem ekonomi sosialis berdasarkan pada prinsip keadilan \r\nsosial.', 'Sistem ekonomi kapitalis berdasarkan pada prinsip keadilan sosial, \r\nsedangkan sistem ekonomi sosialis berdasarkan pada prinsip kebebasan \r\nindividu.', 'Sistem ekonomi kapitalis berdasarkan pada prinsip kekuasaan negara, \r\nsedangkan sistem ekonomi sosialis berdasarkan pada prinsip kebebasan \r\nindividu.', 'Sistem ekonomi kapitalis berdasarkan pada prinsip kebebasan individu, \r\nsedangkan sistem ekonomi sosialis berdasarkan pada prinsip kekuasaan \r\nnegara.', '', 'pilihan_1', 'Aktif', '2026-02-04 04:17:31'),
(91, 2, 'IPS9-01', 'Apa dampak dari perubahan iklim terhadap perekonomian negara?&nbsp;&nbsp;', 'Pilihan Ganda', 'Perubahan iklim dapat menyebabkan penurunan produksi pertanian dan peningkatan biaya energi', 'Perubahan iklim dapat menyebabkan peningkatan produksi pertanian dan penurunan biaya energi', 'Perubahan iklim tidak memiliki dampak signifikan terhadap perekonomian negara.', 'Perubahan iklim dapat menyebabkan peningkatan produksi industri dan penurunan biaya transportasi.', '', 'pilihan_1', 'Aktif', '2026-02-04 04:18:25'),
(92, 3, 'IPS9-01', 'Pada tahun 1998, Indonesia mengalami krisis moneter yang parah. Krisis \r\nini menyebabkan nilai rupiah jatuh drastis dan inflasi meningkat. \r\nBagaimana dampak krisis moneter ini terhadap kehidupan sehari-hari \r\nmasyarakat Indonesia?&nbsp;&nbsp;', 'Pilihan Ganda Kompleks', 'Krisis moneter menyebabkan banyak orang kehilangan pekerjaan dan \r\nkemiskinan meningkat, sehingga banyak keluarga tidak dapat memenuhi \r\nkebutuhan dasar seperti makanan dan tempat tinggal.', 'Krisis moneter tidak memiliki dampak signifikan terhadap kehidupan \r\nsehari-hari masyarakat Indonesia, karena banyak orang masih dapat \r\nmemenuhi kebutuhan dasar mereka.', 'Krisis moneter menyebabkan harga barang dan jasa meningkat, sehingga \r\nbanyak orang harus mengurangi pengeluaran mereka untuk memenuhi \r\nkebutuhan dasar.', 'Krisis moneter tidak menyebabkan inflasi, sehingga harga barang dan jasa tetap stabil.', '', 'pilihan_1,pilihan_3', 'Aktif', '2026-02-04 04:19:35'),
(93, 4, 'IPS9-01', 'Jawab Sesuai dengan Pernyataan Berikut', 'Benar/Salah', 'Ketergantungan ekonomi Indonesia pada impor minyak bumi menyebabkan krisis ekonomi yang berkepanjangan.&nbsp;&nbsp;', 'Pemerintah Indonesia dapat meningkatkan pendapatan negara dengan meningkatkan pajak.&nbsp;&nbsp;', '', '', '', 'Benar|Benar', 'Aktif', '2026-02-04 04:21:35'),
(94, 5, 'IPS9-01', 'Pilih Jawaban yang tepat dari pernyataan berikut', 'Benar/Salah', 'Pembangunan industri tekstil di Inggris pada abad ke-18 menyebabkan peningkatan jumlah pekerja perempuan.&nbsp;&nbsp;', 'Penggunaan teknologi informasi di Indonesia pada tahun 1990-an tidak berdampak signifikan pada peningkatan ekonomi negara.&nbsp;&nbsp;', '', '', '', 'Benar|Salah', 'Aktif', '2026-02-04 04:22:31'),
(95, 6, 'IPS9-01', 'Jelaskan bagaimana peran pemerintah dalam mengelola sumber daya alam di Indonesia?&nbsp;&nbsp;', 'Uraian', NULL, NULL, NULL, NULL, '', 'Pemerintah memiliki peran penting dalam mengelola sumber daya alam di Indonesia. Mereka bertanggung jawab untuk mengatur dan mengawasi penggunaan sumber daya alam, serta mengambil keputusan untuk menjaga keberlanjutan sumber daya alam. Pemerintah juga dapat mengatur hukum dan peraturan untuk melindungi sumber daya alam dan mencegah kegiatan yang merusak lingkungan.', 'Aktif', '2026-02-04 04:23:04'),
(96, 7, 'IPS9-01', '<div class=\"horizontal-scroll-wrapper\"><div class=\"table-block-component\"><response-element class=\"\" ng-version=\"0.0.0-PLACEHOLDER\"><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><!----><table-block _nghost-ng-c261664176=\"\" class=\"ng-star-inserted\"><div _ngcontent-ng-c261664176=\"\" class=\"table-block has-export-button\"><div _ngcontent-ng-c261664176=\"\" not-end-of-paragraph=\"\" class=\"table-content not-end-of-paragraph\" jslog=\"275421;track:impression,attention\" data-hveid=\"0\" decode-data-ved=\"1\" data-ved=\"0CAAQ3ecQahgKEwjN5bXU_r6SAxUAAAAAHQAAAAAQigE\"></div></div></table-block></response-element></div></div></p><p data-path-to-node=\"4\">Jodohkanlah kolom <b data-path-to-node=\"4\" data-index-in-node=\"18\">Pernyataan</b> dengan <b data-path-to-node=\"4\" data-index-in-node=\"36\">Istilah</b> yang tepat!', 'Menjodohkan', NULL, NULL, NULL, NULL, '', 'Posisi suatu wilayah berdasarkan garis lintang dan garis bujur.:Letak Geografis|Posisi suatu wilayah dilihat dari kenyataannya di permukaan bumi.:Letak Geologis|Posisi wilayah berdasarkan struktur batuan di dalam bumi.:Letak Astronomis', 'Aktif', '2026-02-04 04:25:58');

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

--
-- Dumping data for table `chat`
--

INSERT INTO `chat` (`id`, `id_user`, `pesan`, `waktu`, `deleted`, `role`) VALUES
(75, 1, '🔥', '2026-02-02 22:21:25', 0, 'admin'),
(76, 5, 'tes', '2026-02-04 14:10:45', 1, 'admin'),
(77, 5, 'eqwr', '2026-02-04 14:12:36', 1, 'admin'),
(78, 5, 'qwesdf', '2026-02-04 14:12:47', 1, 'admin'),
(79, 5, 'sasd', '2026-02-04 14:15:41', 1, 'admin'),
(80, 1, 'sdfsdf', '2026-02-04 14:15:51', 1, 'admin'),
(81, 5, 'asd', '2026-02-04 14:16:52', 1, 'admin'),
(82, 1, 'Oke pak', '2026-02-04 14:17:58', 0, 'admin'),
(83, 5, 'yuhu', '2026-02-04 14:18:05', 0, 'admin'),
(84, 5, '😎', '2026-02-04 14:18:41', 0, 'siswa'),
(85, 5, 'adoh', '2026-02-04 14:19:26', 1, 'admin');

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
(1, 5, 'Corbuzier', 'pilihan 5', '', '[6:pilihan_1][7:pilihan_4][1:pilihan_2][2:pilihan_1][10:pilihan_2,pilihan_3][3:Benar|Salah|Salah][8:Salah|Salah|Benar][9:Salah|Salah|Benar|Salah|Benar][4:asdsad 1:asdads 1|asdasd 2:asdasd 2][11:Bilangan prima antara 10 dan 15:11 dan 13|Volume kubus dengan rusuk 4 cm:64 cm|Luas persegi dengan sisi 6 cm:36 cm|Nilai x dari 2x+5=19:7][12:7][5:dsafsdf]', '116', '2026-02-02 15:32:14', 'Selesai'),
(7, 5, 'Corbuzier', 'MAT9-01', '', '[1:pilihan_4][4:pilihan_3,pilihan_4][3:Salah|Salah|Salah][2:Benar|Salah|Salah][5:Bilangan prima antara 10 dan 15:11 dan 13|Volume kubus dengan rusuk 4 cm:64 cm|Luas persegi dengan sisi 6 cm:36 cm|Nilai x dari 2x+5=19:7][6:6]', '120', '2026-02-03 15:14:05', 'Selesai'),
(9, 5, 'Corbuzier', 'IPS9', '', '[1:pilihan_2][6:pilihan_1][7:pilihan_5][2:pilihan_2][10:pilihan_4,pilihan_5][3:Benar|Salah|Benar][9:Salah|Salah|Salah|Benar|Benar][8:Benar|Salah|Benar][11:Bilangan prima antara 10 dan 15:11 dan 13|Volume kubus dengan rusuk 4 cm:64 cm|Luas persegi dengan sisi 6 cm:36 cm|Nilai x dari 2x+5=19:7][4:asdsad 1:asdads 1|asdasd 2:asdasd 2][5:asdasd]', '90', '2026-02-03 15:16:04', 'Selesai');

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
(2, 5, 'Corbuzier', 'MAT9-01', 6, '2', '1', '3', '[1:pilihan_4][4:pilihan_3,pilihan_4][3:Salah|Salah|Salah][2:Benar|Salah|Salah][5:Bilangan prima antara 10 dan 15:11 dan 13|Volume kubus dengan rusuk 4 cm:64 cm|Luas persegi dengan sisi 6 cm:36 cm|Nilai x dari 2x+5=19:7][6:6]', '[1:pilihan_4],[2:Benar|Benar|Salah],[3:Salah|Salah|Salah|Benar],[4:pilihan_1,pilihan_3],[5:Bilangan prima antara 10 dan 15:11 dan 13|Volume kubus dengan rusuk 4 cm:64 cm|Luas persegi dengan sisi 6 cm:36 cm|Nilai x dari 2x+5=19:7],[6:6]', 56.94, 15.52, '[6:15.52]', '2026-02-03 22:15:18', 'perlu_dinilai'),
(3, 5, 'Corbuzier', 'IPS9', 11, '4', '4', '3', '[1:pilihan_2][6:pilihan_1][7:pilihan_5][2:pilihan_2][10:pilihan_4,pilihan_5][3:Benar|Salah|Benar][9:Salah|Salah|Salah|Benar|Benar][8:Benar|Salah|Benar][11:Bilangan prima antara 10 dan 15:11 dan 13|Volume kubus dengan rusuk 4 cm:64 cm|Luas persegi dengan sisi 6 cm:36 cm|Nilai x dari 2x+5=19:7][4:asdsad 1:asdads 1|asdasd 2:asdasd 2][5:asdasd]', '[1:pilihan_2],[2:pilihan_4,pilihan_5],[3:Benar|Salah|Salah],[4:asdsad 1:asdads 1|asdasd 2:asdasd 2],[5:asd],[6:pilihan_2],[7:pilihan_4],[8:Benar|Benar|Salah],[9:Salah|Salah|Salah|Benar|Benar],[10:pilihan_1,pilihan_3],[11:Bilangan prima antara 10 dan 15:11 dan 13|Volume kubus dengan rusuk 4 cm:64 cm|Luas persegi dengan sisi 6 cm:36 cm|Nilai x dari 2x+5=19:7]', 45.45, 9.09, '[5:9.09]', '2026-02-03 22:17:03', 'perlu_dinilai');

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
(1, 'CBT-Eschool', 'logo_1747650742.png', '#343a40', 60, 1, 'izinkan', 'izinkan', '2.0.7', 'tidak');

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
(5, 'Corbuzier', '/SbMMmTczf7Ry0qUn/f6XmhpM1BYS0l6S1F0cmlHSlB3ZjE1cEE9PQ==', '123461', '9', 'E', 'Nonaktif', '', NULL, 'http://localhost/cbt-eschool/siswa/game.php', 1),
(13, 'Erina', 'FQOm8MYUIes79E36AQv1AU5VMVdUanhIaTBVTURVS0hXckFRUXc9PQ==', '721731', '9', 'A', 'Nonaktif', '', '0000-00-00 00:00:00', 'http://localhost/cbt-eschool/siswa/dashboard.php', 1),
(14, 'Phoebe', 'Dwl3VYW4ysVVEjO67sy6QmdYb2h0NjNFWjhlV3ViamtWY01hc0E9PQ==', '122345', '7', 'C', 'Nonaktif', '9a394ef4a7893401d0185c9a489d7f17483e8e47a105da3ee7174307346d701c', '2025-05-25 21:08:44', 'http://localhost/cbt-eschool/siswa/preview_hasil.php?id_siswa=14&kode_soal=BINDO7-1', 0),
(15, 'Zevan', 'mG5EAQl0ttZQFaqBXlYCgGdMVkdTMjNQQXZ3VmRKdmFNbTJBeEE9PQ==', '257174', '7', 'D', 'Nonaktif', '', '2025-05-25 00:13:02', 'http://localhost/cbt-eschool/siswa/ujian.php', 0),
(16, 'Denny', 'N2ugxO2xwJR74bjbZQv19nYrMFVFbi9JTEk5MFNDeVdITWxmM0E9PQ==', '641343', '7', 'F', 'Nonaktif', '', '2025-05-25 14:58:48', 'http://localhost/cbt-eschool/siswa/hasil.php', 0),
(17, 'Lintar', 'CJ7fgqg1+lzEgNuqTQwdCUtBeHlsdXdGU3FabGdhQ3lQbXQ2NlE9PQ==', '252743', '9', 'D', 'Nonaktif', '', '2025-05-28 13:42:55', 'http://localhost/cbt-eschool/siswa/ujian.php', 0),
(18, 'andy', '5IiPhwyWU7/GiyYe622atFErOVViUmNXOXRheVk0Z2U1V0tiK2c9PQ==', '876543', '8', 'D', 'Nonaktif', '', '0000-00-00 00:00:00', 'http://localhost/cbt-eschool/siswa/dashboard.php', 1),
(20, 'Robi', 'XpqGGiL6DfhOGnPqcV9EnUdLdTlEbzdwN1pyVE5wb2FJSStLdUE9PQ==', '252645', '9', 'G', 'Nonaktif', '', '2025-05-24 22:26:17', 'http://localhost/cbt-eschool/siswa/dashboard.php', 0),
(21, 'Intan', 'Ya+NHgRRNME9cYTmSRYUz2sxMS9tczlZMGJDaFd0NkN5TzErV0E9PQ==', '1241322', '7', 'B', 'Nonaktif', '', '0000-00-00 00:00:00', 'http://localhost/cbt-eschool/siswa/dashboard.php', 1),
(27, 'sdfsdf', 'H2v097Jzcyt2mZJGQ3crqXJSSnU1S3pSc3JUdlB2MmxteExhK0E9PQ==', 'sdf', 'XI', 'A', 'Nonaktif', '', NULL, '', 0);

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
  `id_pembuat` varchar(100) NOT NULL,
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

INSERT INTO `soal` (`id_soal`, `id_pembuat`, `kode_soal`, `nama_soal`, `mapel`, `kelas`, `waktu_ujian`, `tanggal`, `status`, `tampilan_soal`, `kunci`, `token`, `jumlah_opsi`, `tampil_tombol_selesai`) VALUES
(1, '1', 'MAT9-01', 'Matematika 9', 'Matematika', '9', 120, '2025-06-05', 'Nonaktif', 'Acak', '[1:pilihan_4],[2:Benar|Benar|Salah],[3:Salah|Salah|Salah|Benar],[4:pilihan_1,pilihan_3],[5:Bilangan prima antara 10 dan 15:11 dan 13|Volume kubus dengan rusuk 4 cm:64 cm|Luas persegi dengan sisi 6 cm:36 cm|Nilai x dari 2x+5=19:7],[6:6]', '', 4, 0),
(10, '5', 'IPS9-01', 'IPS9-01', 'IPS', '9', 90, '2026-02-04', 'Nonaktif', 'Acak', '', '', 4, 0);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `butir_soal`
--
ALTER TABLE `butir_soal`
  MODIFY `id_soal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `chat`
--
ALTER TABLE `chat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `faq`
--
ALTER TABLE `faq`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `jawaban_siswa`
--
ALTER TABLE `jawaban_siswa`
  MODIFY `id_jawaban` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `nilai`
--
ALTER TABLE `nilai`
  MODIFY `id_nilai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id_siswa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `skor_game`
--
ALTER TABLE `skor_game`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `soal`
--
ALTER TABLE `soal`
  MODIFY `id_soal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
