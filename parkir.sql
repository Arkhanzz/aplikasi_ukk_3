-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 06, 2026 at 03:45 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `parkir`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_area_parkir`
--

CREATE TABLE `tb_area_parkir` (
  `id_area` int(11) NOT NULL,
  `nama_area` varchar(50) DEFAULT NULL,
  `kapasitas` int(5) DEFAULT NULL,
  `terisi` int(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tb_area_parkir`
--

INSERT INTO `tb_area_parkir` (`id_area`, `nama_area`, `kapasitas`, `terisi`) VALUES
(5, 'Area A2', 30, 1),
(12, 'Area A1', 6, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tb_kendaraan`
--

CREATE TABLE `tb_kendaraan` (
  `id_kendaraan` int(11) NOT NULL,
  `plat_nomor` varchar(15) DEFAULT NULL,
  `jenis_kendaraan` varchar(20) DEFAULT NULL,
  `warna` varchar(20) DEFAULT NULL,
  `pemilik` varchar(100) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tb_kendaraan`
--

INSERT INTO `tb_kendaraan` (`id_kendaraan`, `plat_nomor`, `jenis_kendaraan`, `warna`, `pemilik`, `id_user`) VALUES
(9, '1f 657', 'Motor', 'merah', 'fajri', 1),
(10, '4  KTL JFG', 'mobil', 'HITAM', 'BOLOL', 1),
(11, '1f 657 GGHG', 'motor', 'PINK', 'AMBA', 1),
(17, '4  KTL', 'motor', 'HITAM', 'AMBASING', 1),
(18, '1ddff 6575', 'motor', 'HITAM', 'AMBA', 1),
(31, 'KCX 666 GPH', 'mobil', 'HIJAU', 'AMBADEBLOU', 1),
(32, '4  KTL JFG', 'Motor', 'HIJAU', 'AMBATUKAM', 1),
(33, '1ddff 6575', 'motor', 'HITAM', 'AMBASING', 1),
(37, 'B 854 HGF', 'motor', 'PINK', 'AMBALABU', 1),
(47, '4  KTL JFG', 'mobil', 'BIRU', 'fajri', 1),
(54, '1f 657 6666666', 'mobil', 'HITAM', 'fajri', 1),
(55, '3 fgn 64', 'motor', 'HITAM', 'AMBATUKAM', 1),
(57, 'Bh 945 TT', 'mobil', 'Coklat', 'AMBASING', 1),
(58, 'B 854 HGF', 'mobil', 'Pink', 'AMBATUKAM', 1),
(59, 'B  453 JFG', 'motor', 'Merah', 'AMBALABU', 1),
(60, 'B 123', 'mobil', 'Hijau', 'AMBA', 1),
(61, 'B 0164 GDG', 'mobil', 'Biru', 'Firdzy', 1),
(62, 'B 183 GMR', 'mobil', 'Putih', 'Bayu', 1),
(63, 'JP 739 Fht', 'mobil', 'BIRU', 'BOLOL', 1),
(64, '4  KTL JFG', 'mobil', 'HYTAM LEGAM', 'AMBASING', 1),
(65, '1f 657', 'motor', 'HYTAM LEGAM', 'AMBASING', 1),
(66, '4  KTL JFG', 'motor', 'HITAM', 'AMBASING', 1),
(68, '4  KTL', 'lainnya', '', '', 1),
(69, '1f 5658768', 'mobil', 'HIJAU', 'BILAL', 1),
(70, '4  KTL JFG', 'lainnya', '', '', 1),
(71, '1f 5658768', 'motor', '', '', 1),
(72, '4353FGF', 'mobil', 'merah', 'mmg', 1),
(73, '111FFF', 'motor', 'merah', 'mmg', 1),
(74, '222FF', 'mobil', 'hijau', 'mmg', 1),
(75, '333F', 'lainnya', 'biru', 'mmg', 1),
(76, '111FFF', 'motor', 'merah', 'mmg', 1),
(77, '333F', 'motor', 'biru', 'mmg', 1),
(78, '4353FGF', 'lainnya', 'kuning', 'mmg', 1),
(79, '234', 'mobil', 'mer', 'mmg', 1),
(80, '23454', 'motor', 'mer', 'mmg', 1),
(81, '354634', 'motor', 'merah', 'mmg', 1),
(82, '87857587685', 'lainnya', 'kuning', 'ahah', 1),
(83, '234', 'motor', 'mer', 'mmg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tb_log_aktivitas`
--

CREATE TABLE `tb_log_aktivitas` (
  `id_log` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `aktivitas` varchar(100) DEFAULT NULL,
  `waktu_aktivitas` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tb_log_aktivitas`
--

INSERT INTO `tb_log_aktivitas` (`id_log`, `id_user`, `aktivitas`, `waktu_aktivitas`) VALUES
(1, 1, 'Menghapus user ID: 4', NULL),
(2, 2, 'Input kendaraan BK 1945 ML', NULL),
(3, 2, 'Input kendaraan BK 1107 LAB', NULL),
(4, 2, 'Masuk Kendaraan: BK 1945 ML', NULL),
(5, 2, 'Masuk Kendaraan: BK 1945 ML', NULL),
(6, 2, 'Masuk Kendaraan: BK 1945 ML', '2026-01-25 02:37:53'),
(7, 2, 'Masuk: BK 1945 ML', '2026-01-25 02:47:49'),
(8, 2, 'Keluar ID: 6', '2026-01-25 02:58:17'),
(9, 2, 'Masuk: BK 1107 LAB', '2026-01-25 02:58:54'),
(11, 1, 'Keluar ID: 1', '2026-02-02 15:24:38'),
(12, 1, 'Masuk: ', '2026-02-02 15:49:25'),
(13, 1, 'Keluar ID: 2', '2026-02-02 15:49:27'),
(14, 1, 'Masuk: ', '2026-02-02 15:54:11'),
(15, 1, 'Keluar ID: 3', '2026-02-02 15:54:14'),
(16, 1, 'Masuk: 1ddff 6575', '2026-02-02 16:11:21'),
(17, 1, 'Keluar ID: 4', '2026-02-02 16:13:21'),
(18, 1, 'Masuk: 1ddff 6575', '2026-02-02 16:14:20'),
(20, 1, 'Keluar ID: 6', '2026-02-02 18:16:24'),
(21, 1, 'Test log aktivitas', '2026-04-05 15:50:34'),
(22, 1, 'Login ke sistem', '2026-04-05 15:51:04'),
(23, 1, 'Kendaraan masuk parkir: 23454 (motor) - Area 12', '2026-04-05 15:52:13'),
(24, 1, 'Kendaraan keluar parkir: 23454 - Durasi 1 jam, Biaya Rp2,000', '2026-04-05 15:54:43'),
(25, 1, 'Mengedit user: Administrator Sistem', '2026-04-05 16:19:47'),
(26, 1, 'Logout dari sistem', '2026-04-05 16:19:50'),
(27, 1, 'Login ke sistem', '2026-04-05 16:19:56'),
(28, 1, 'Menambah user baru: Pemilik 2 (owner)', '2026-04-05 16:20:31'),
(29, 1, 'Mengedit user: Pemilik 2', '2026-04-05 16:20:44'),
(30, 1, 'Mengedit user: Pemilik 2', '2026-04-05 16:20:50'),
(31, 1, 'Mengedit user: Pemilik 2', '2026-04-05 16:20:59'),
(32, 1, 'Kendaraan masuk parkir: 354634 (motor) - Area 4', '2026-04-05 16:21:32'),
(33, 1, 'Kendaraan masuk parkir: 87857587685 (lainnya) - Area 5', '2026-04-05 16:39:08'),
(34, 1, 'Kendaraan keluar parkir: 354634 - Durasi 1 jam, Biaya Rp2,000', '2026-04-05 16:40:47'),
(35, 1, 'Menghapus area parkir ID: 0', '2026-04-05 16:55:50'),
(36, 1, 'Menghapus area parkir ID: 0', '2026-04-05 16:55:57'),
(37, 1, 'Menghapus area parkir ID: 0', '2026-04-05 16:55:59'),
(38, 1, 'Login ke sistem', '2026-04-05 18:22:14'),
(39, 1, 'Logout dari sistem', '2026-04-05 18:24:26'),
(40, 5, 'Login ke sistem', '2026-04-05 18:24:32'),
(41, 5, 'Logout dari sistem', '2026-04-05 18:24:37'),
(42, 1, 'Login ke sistem', '2026-04-05 18:24:44'),
(43, 1, 'Login ke sistem', '2026-04-05 18:26:07'),
(44, 1, 'Login ke sistem', '2026-04-05 18:29:13'),
(45, 1, 'Login ke sistem', '2026-04-06 06:05:15'),
(46, 1, 'Logout dari sistem', '2026-04-06 06:05:19'),
(47, 1, 'Login ke sistem', '2026-04-06 06:05:33'),
(48, 1, 'Logout dari sistem', '2026-04-06 06:05:45'),
(49, 1, 'Login ke sistem', '2026-04-06 06:06:47'),
(50, 1, 'Logout dari sistem', '2026-04-06 06:06:51'),
(51, 1, 'Login ke sistem', '2026-04-06 08:13:12');

-- --------------------------------------------------------

--
-- Table structure for table `tb_tarif`
--

CREATE TABLE `tb_tarif` (
  `id_tarif` int(11) NOT NULL,
  `jenis_kendaraan` enum('motor','mobil','lainnya','') DEFAULT NULL,
  `tarif_per_jam` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tb_tarif`
--

INSERT INTO `tb_tarif` (`id_tarif`, `jenis_kendaraan`, `tarif_per_jam`) VALUES
(13, 'mobil', 5000),
(15, 'motor', 2000),
(22, 'lainnya', 7000);

-- --------------------------------------------------------

--
-- Table structure for table `tb_transaksi`
--

CREATE TABLE `tb_transaksi` (
  `id_parkir` int(11) NOT NULL,
  `id_kendaraan` int(11) DEFAULT NULL,
  `waktu_masuk` datetime DEFAULT NULL,
  `waktu_keluar` datetime DEFAULT NULL,
  `id_tarif` int(11) DEFAULT NULL,
  `durasi_jam` int(5) DEFAULT NULL,
  `biaya_total` decimal(10,0) DEFAULT NULL,
  `status` enum('masuk','keluar','') DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_area` int(11) DEFAULT NULL,
  `kode_karcis` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tb_transaksi`
--

INSERT INTO `tb_transaksi` (`id_parkir`, `id_kendaraan`, `waktu_masuk`, `waktu_keluar`, `id_tarif`, `durasi_jam`, `biaya_total`, `status`, `id_user`, `id_area`, `kode_karcis`) VALUES
(17, 17, '2026-02-03 04:10:10', '2026-02-03 04:13:03', 15, 1, 2000, 'keluar', 1, NULL, 'KRC-20260203041010-844'),
(18, 18, '2026-02-03 04:13:29', '2026-02-03 04:15:40', 15, 1, 2000, 'keluar', 1, 5, 'KRC-20260203041329-783'),
(31, 31, '2026-02-03 06:13:17', '2026-02-03 09:39:23', 13, 4, 20000, 'keluar', 1, 5, 'KRC-20260203061317-209'),
(32, 32, '2026-02-03 09:43:32', '2026-02-03 12:48:54', 13, 4, 20000, 'keluar', 1, 5, 'KRC-20260203094332-819'),
(33, 33, '2026-02-03 13:01:48', '2026-02-03 14:14:17', 15, 2, 4000, 'keluar', 1, 5, 'KRC-20260203130148-715'),
(37, 37, '2026-02-03 14:17:05', '2026-02-03 15:37:54', 15, 2, 4000, 'keluar', 1, NULL, 'KRC-20260203141705-681'),
(47, 47, '2026-02-04 10:50:15', '2026-02-10 14:31:56', 13, 148, 740000, 'keluar', 1, NULL, 'KRC-20260204105015-155'),
(54, 54, '2026-02-04 11:27:31', '2026-02-04 13:00:34', 13, 2, 10000, 'keluar', 1, NULL, 'KRC-20260204112731-666'),
(55, 55, '2026-02-04 11:55:04', '2026-02-04 12:41:41', 15, 1, 2000, 'keluar', 1, NULL, 'KRC-20260204115504-413'),
(57, 57, '2026-02-10 14:33:42', '2026-02-10 14:34:55', 13, 1, 5000, 'keluar', 1, NULL, 'KRC-20260210143342-179'),
(58, 58, '2026-02-10 14:34:07', '2026-02-10 14:34:49', 13, 1, 5000, 'keluar', 1, 5, 'KRC-20260210143407-746'),
(59, 59, '2026-02-10 14:34:33', '2026-02-10 14:34:42', 15, 1, 2000, 'keluar', 1, NULL, 'KRC-20260210143433-556'),
(60, 60, '2026-02-20 14:37:49', '2026-02-20 14:39:29', 13, 1, 5000, 'keluar', 1, NULL, 'KRC-20260220143749-102'),
(61, 61, '2026-02-20 14:38:32', '2026-02-20 14:39:24', 13, 1, 5000, 'keluar', 1, 5, 'KRC-20260220143832-650'),
(62, 62, '2026-02-20 14:39:06', '2026-02-20 14:39:18', 13, 1, 5000, 'keluar', 1, NULL, 'KRC-20260220143906-468'),
(63, 63, '2026-02-28 14:41:21', '2026-02-28 14:41:36', 13, 1, 5000, 'keluar', 1, NULL, 'KRC-20260228144121-127'),
(64, 64, '2026-03-01 09:51:23', '2026-03-01 09:54:06', 13, 1, 5000, 'keluar', 1, NULL, 'KRC-20260301095123-557'),
(65, 65, '2026-03-01 09:54:42', '2026-03-01 09:56:38', 15, 1, 2000, 'keluar', 1, NULL, 'KRC-20260301095442-366'),
(66, 66, '2026-02-11 19:43:06', '2026-02-11 19:43:40', 15, 1, 2000, 'keluar', 1, 5, 'KRC-20260211194306-181'),
(68, 68, '2026-02-11 21:31:43', '2026-02-12 11:35:13', NULL, 15, 105000, 'keluar', 1, 12, 'KRC-20260211213143-567'),
(69, 69, '2026-02-11 21:40:36', '2026-02-12 11:34:11', 13, 14, 70000, 'keluar', 1, 5, 'KRC-20260211214036-189'),
(70, 70, '2026-02-12 11:36:52', '2026-02-12 16:04:47', NULL, 5, 35000, 'keluar', 1, 5, 'KRC-20260212113652-854'),
(71, 71, '2026-02-12 12:07:49', '2026-04-04 17:22:20', 15, 1230, 2460000, 'keluar', 1, 12, 'KRC-20260212120749-697'),
(72, 72, '2026-04-04 18:04:30', '2026-04-04 18:13:12', 13, 1, 5000, 'keluar', 1, NULL, 'KRC-20260404180430-501'),
(73, 73, '2026-04-04 18:24:50', '2026-04-04 18:29:00', 15, 1, 2000, 'keluar', 1, NULL, 'KRC-20260404182450-316'),
(74, 74, '2026-04-04 18:25:07', '2026-04-05 13:42:58', 13, 20, 100000, 'keluar', 1, 5, 'KRC-20260404182507-730'),
(75, 75, '2026-04-04 18:25:22', '2026-04-04 18:27:31', NULL, 1, 7000, 'keluar', 1, NULL, 'KRC-20260404182522-366'),
(76, 76, '2026-04-04 18:43:25', '2026-04-05 13:42:52', 15, 19, 38000, 'keluar', 1, NULL, 'KRC-20260404184325-730'),
(77, 77, '2026-04-04 18:43:55', '2026-04-05 13:42:47', 15, 19, 38000, 'keluar', 1, NULL, 'KRC-20260404184355-296'),
(78, 78, '2026-04-04 18:44:26', '2026-04-05 13:42:41', NULL, 19, 133000, 'keluar', 1, 12, 'KRC-20260404184426-209'),
(79, 79, '2026-04-05 13:24:40', '2026-04-05 13:25:50', 13, 1, 5000, 'keluar', 1, NULL, 'KRC-20260405132440-698'),
(80, 80, '2026-04-05 15:52:13', '2026-04-05 15:54:43', 15, 1, 2000, 'keluar', 1, 12, 'KRC-20260405155213-806'),
(81, 81, '2026-04-05 16:21:32', '2026-04-05 16:40:47', 15, 1, 2000, 'keluar', 1, NULL, 'KRC-20260405162132-848'),
(82, 82, '2026-04-05 16:39:08', '2026-04-05 18:25:22', NULL, 2, 14000, 'keluar', 1, 5, 'KRC-20260405163908-557'),
(83, 83, '2026-04-06 08:21:16', NULL, 15, 0, 0, 'masuk', 1, 5, 'KRC-20260406082116-383');

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE `tb_user` (
  `id_user` int(11) NOT NULL,
  `nama_lengkap` varchar(50) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `role` enum('admin','petugas','owner','') DEFAULT NULL,
  `status_aktif` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tb_user`
--

INSERT INTO `tb_user` (`id_user`, `nama_lengkap`, `username`, `password`, `role`, `status_aktif`) VALUES
(1, 'Administrator Sistem', 'admin', '54321', 'admin', 1),
(2, 'Petugas Lapangan', 'petugas', 'petugas', 'petugas', 1),
(3, 'Pemilik Parkir', 'owner', '123', 'owner', 1),
(5, 'Pemilik 2', 'owner2', '12345', 'owner', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_area_parkir`
--
ALTER TABLE `tb_area_parkir`
  ADD PRIMARY KEY (`id_area`);

--
-- Indexes for table `tb_kendaraan`
--
ALTER TABLE `tb_kendaraan`
  ADD PRIMARY KEY (`id_kendaraan`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `tb_log_aktivitas`
--
ALTER TABLE `tb_log_aktivitas`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `tb_tarif`
--
ALTER TABLE `tb_tarif`
  ADD PRIMARY KEY (`id_tarif`);

--
-- Indexes for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  ADD PRIMARY KEY (`id_parkir`),
  ADD UNIQUE KEY `kode_karcis` (`kode_karcis`),
  ADD KEY `id_kendaraan` (`id_kendaraan`),
  ADD KEY `id_tarif` (`id_tarif`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_area` (`id_area`);

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_area_parkir`
--
ALTER TABLE `tb_area_parkir`
  MODIFY `id_area` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tb_kendaraan`
--
ALTER TABLE `tb_kendaraan`
  MODIFY `id_kendaraan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `tb_log_aktivitas`
--
ALTER TABLE `tb_log_aktivitas`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `tb_tarif`
--
ALTER TABLE `tb_tarif`
  MODIFY `id_tarif` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  MODIFY `id_parkir` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_kendaraan`
--
ALTER TABLE `tb_kendaraan`
  ADD CONSTRAINT `tb_kendaraan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`);

--
-- Constraints for table `tb_log_aktivitas`
--
ALTER TABLE `tb_log_aktivitas`
  ADD CONSTRAINT `tb_log_aktivitas_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`);

--
-- Constraints for table `tb_transaksi`
--
ALTER TABLE `tb_transaksi`
  ADD CONSTRAINT `tb_transaksi_ibfk_1` FOREIGN KEY (`id_kendaraan`) REFERENCES `tb_kendaraan` (`id_kendaraan`),
  ADD CONSTRAINT `tb_transaksi_ibfk_2` FOREIGN KEY (`id_tarif`) REFERENCES `tb_tarif` (`id_tarif`),
  ADD CONSTRAINT `tb_transaksi_ibfk_3` FOREIGN KEY (`id_user`) REFERENCES `tb_user` (`id_user`),
  ADD CONSTRAINT `tb_transaksi_ibfk_4` FOREIGN KEY (`id_area`) REFERENCES `tb_area_parkir` (`id_area`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
