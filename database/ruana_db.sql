-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 10, 2026 at 04:14 PM
-- Server version: 8.0.30
-- PHP Version: 8.2.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ruana_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `anggota`
--

CREATE TABLE `anggota` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `nim` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `jabatan` varchar(100) DEFAULT NULL,
  `status` enum('aktif','tidak aktif') DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `anggota`
--

INSERT INTO `anggota` (`id`, `user_id`, `nim`, `nama`, `email`, `no_hp`, `jabatan`, `status`, `created_at`) VALUES
(9, 1, '225550023002', 'Ridho', 'ridho@gmail.com', '08244245737', 'Anggota', 'aktif', '2026-07-06 23:39:12'),
(10, 1, '2555301058', 'Egi', 'egi@pcr.ac.id', '083776326362', 'Ketua', 'aktif', '2026-07-10 01:07:15');

-- --------------------------------------------------------

--
-- Table structure for table `dokumen`
--

CREATE TABLE `dokumen` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `nama_organisasi` varchar(150) NOT NULL DEFAULT '',
  `approved_by` int DEFAULT NULL,
  `judul` varchar(200) NOT NULL,
  `kategori` enum('Proposal','Surat Masuk','Surat Keluar','LPJ','Lainnya') NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `status_approval` enum('pending','disetujui','ditolak') DEFAULT 'pending',
  `catatan_dosen` varchar(255) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `dokumen`
--

INSERT INTO `dokumen` (`id`, `user_id`, `nama_organisasi`, `approved_by`, `judul`, `kategori`, `nama_file`, `tanggal`, `status_approval`, `catatan_dosen`, `approved_at`, `created_at`) VALUES
(8, 2, 'HIMA', 4, 'Proposal seminar IT 2028', 'Proposal', '1783228320_ChatGPT_Image_3_Jul_2026,_16.27.24.png', '2026-07-05', 'disetujui', '', '2026-07-05 05:12:12', '2026-07-05 05:12:00'),
(11, 2, 'ITSA', 4, 'Surat Organisasi Lomba', 'Proposal', '1783247611_Guide_Book_UI___UX_Design_Competition_ITO_XIV_2026_(1).pdf', '2026-07-05', 'disetujui', '', '2026-07-05 10:33:58', '2026-07-05 10:33:31'),
(12, 2, 'ITSA', 4, 'Surat Organisasi Lomba LAUT', 'Surat Masuk', '1783247849_erdkafe.jpg', '2026-07-05', 'ditolak', 'Kurang menarik', '2026-07-05 10:37:56', '2026-07-05 10:37:29'),
(13, 2, 'ITSA', 4, 'Proposal Seminar', 'Proposal', '1783645709_Laporan_Proyek_Akhir_PBO_-_TID_-_Kelompok_1_(5).pdf', '2026-07-17', 'disetujui', '', '2026-07-10 01:09:01', '2026-07-10 01:08:29'),
(14, 2, 'ITSA', NULL, 'Proposal Seminar', 'Proposal', '1783697417_images.png', '2026-07-10', 'pending', NULL, NULL, '2026-07-10 15:30:17'),
(15, 2, 'BEM', NULL, 'Surat Masuk Organisasi', 'Surat Masuk', '1783697455_Laporan_ProyekAkhir_WRPL_[Kel_09]_(1).pdf', '2026-07-10', 'pending', NULL, NULL, '2026-07-10 15:30:55');

-- --------------------------------------------------------

--
-- Table structure for table `keuangan`
--

CREATE TABLE `keuangan` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `jenis` enum('pemasukan','pengeluaran') NOT NULL,
  `keterangan` varchar(200) NOT NULL,
  `jumlah` decimal(12,2) NOT NULL,
  `tanggal` date NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `keuangan`
--

INSERT INTO `keuangan` (`id`, `user_id`, `jenis`, `keterangan`, `jumlah`, `tanggal`, `created_at`) VALUES
(2, 3, 'pengeluaran', 'Beli kertas & ATK', '75000.00', '2026-06-05', '2026-06-29 07:47:26'),
(3, 3, 'pemasukan', 'Iuran anggota bulan juli', '200000.00', '2026-07-05', '2026-07-05 05:10:29'),
(4, 3, 'pemasukan', 'Iuran beli makan', '500000.00', '2026-07-05', '2026-07-05 10:38:51'),
(5, 3, 'pengeluaran', 'Beli makan', '400000.00', '2026-07-05', '2026-07-05 10:39:10');

-- --------------------------------------------------------

--
-- Table structure for table `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `judul` varchar(150) NOT NULL,
  `pesan` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notifikasi`
--

INSERT INTO `notifikasi` (`id`, `user_id`, `judul`, `pesan`, `link`, `is_read`, `created_at`) VALUES
(1, 4, 'Dokumen Baru Menunggu Approval', 'Sekretaris dari ITSA mengajukan \"Surat Organisasi Lomba\".', 'index.php?page=dokumen&action=approval', 1, '2026-07-05 10:29:14'),
(2, 2, 'Status Dokumen Diperbarui', 'Dokumen \"Surat Organisasi Lomba\" disetujui ✓ oleh Dosen. Catatan: apa ni wok', 'index.php?page=dokumen&action=index', 1, '2026-07-05 10:30:15'),
(3, 1, 'Dokumen Disetujui', 'Dokumen \"Surat Organisasi Lomba\" telah disetujui ✓ oleh dosen.', 'index.php?page=dokumen&action=index', 1, '2026-07-05 10:30:15'),
(4, 4, 'Dokumen Baru Menunggu Approval', 'Sekretaris dari ITSA mengajukan \"Surat Organisasi Lomba\".', 'index.php?page=dokumen&action=approval', 1, '2026-07-05 10:33:31'),
(5, 2, 'Status Dokumen Diperbarui', 'Dokumen \"Surat Organisasi Lomba\" disetujui ✓ oleh Dosen.', 'index.php?page=dokumen&action=index', 1, '2026-07-05 10:33:58'),
(6, 1, 'Dokumen Disetujui', 'Dokumen \"Surat Organisasi Lomba\" telah disetujui ✓ oleh dosen.', 'index.php?page=dokumen&action=index', 1, '2026-07-05 10:33:58'),
(7, 4, 'Dokumen Baru Menunggu Approval', 'Sekretaris dari ITSA mengajukan \"Surat Organisasi Lomba LAUT\".', 'index.php?page=dokumen&action=approval', 1, '2026-07-05 10:37:29'),
(8, 2, 'Status Dokumen Diperbarui', 'Dokumen \"Surat Organisasi Lomba LAUT\" ditolak ✗ oleh Dosen. Catatan: Kurang menarik', 'index.php?page=dokumen&action=index', 1, '2026-07-05 10:37:56'),
(9, 1, 'Dokumen Ditolak', 'Dokumen \"Surat Organisasi Lomba LAUT\" telah ditolak ✗ oleh dosen.', 'index.php?page=dokumen&action=index', 1, '2026-07-05 10:37:56'),
(10, 4, 'Dokumen Baru Menunggu Approval', 'Sekretaris dari ITSA mengajukan \"Proposal Seminar\".', 'index.php?page=dokumen&action=approval', 1, '2026-07-10 01:08:29'),
(11, 2, 'Status Dokumen Diperbarui', 'Dokumen \"Proposal Seminar\" disetujui ✓ oleh Dosen.', 'index.php?page=dokumen&action=index', 0, '2026-07-10 01:09:01'),
(12, 1, 'Dokumen Disetujui', 'Dokumen \"Proposal Seminar\" telah disetujui ✓ oleh dosen.', 'index.php?page=dokumen&action=index', 0, '2026-07-10 01:09:01'),
(13, 4, 'Dokumen Baru Menunggu Approval', 'Sekretaris dari ITSA mengajukan \"Proposal Seminar\".', 'index.php?page=dokumen&action=approval', 1, '2026-07-10 15:30:17'),
(14, 4, 'Dokumen Baru Menunggu Approval', 'Sekretaris dari BEM mengajukan \"Surat Masuk Organisasi\".', 'index.php?page=dokumen&action=approval', 1, '2026-07-10 15:30:55');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('ketua','sekretaris','bendahara','anggota','dosen') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'Ketua', 'ketua', 'admin123', 'ketua', '2026-06-29 07:47:26'),
(2, 'Sekretaris', 'sekretaris', 'admin123', 'sekretaris', '2026-06-29 07:47:26'),
(3, 'Bendahara', 'bendahara', 'admin123', 'bendahara', '2026-06-29 07:47:26'),
(4, 'Dosen', 'dosen', 'admin123', 'dosen', '2026-06-29 07:47:26'),
(5, 'Anggota', 'anggota', 'admin123', 'anggota', '2026-07-03 01:27:33'),
(6, 'Egi', 'sigma', 'sigma123', 'anggota', '2026-07-03 01:30:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anggota`
--
ALTER TABLE `anggota`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nim` (`nim`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `dokumen`
--
ALTER TABLE `dokumen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `keuangan`
--
ALTER TABLE `keuangan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anggota`
--
ALTER TABLE `anggota`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `dokumen`
--
ALTER TABLE `dokumen`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `keuangan`
--
ALTER TABLE `keuangan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `anggota`
--
ALTER TABLE `anggota`
  ADD CONSTRAINT `anggota_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `dokumen`
--
ALTER TABLE `dokumen`
  ADD CONSTRAINT `dokumen_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `dokumen_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `keuangan`
--
ALTER TABLE `keuangan`
  ADD CONSTRAINT `keuangan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `notifikasi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
