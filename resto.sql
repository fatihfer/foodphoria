-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 29, 2025 at 11:17 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `resto`
--

-- --------------------------------------------------------

--
-- Table structure for table `cabang`
--

CREATE TABLE `cabang` (
  `id` varchar(50) NOT NULL,
  `nama_cabang` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cabang`
--

INSERT INTO `cabang` (`id`, `nama_cabang`, `alamat`, `created_at`, `updated_at`) VALUES
('CBG-001', 'Cabang Utama', 'Jl. Raya No.1', '2025-09-29 10:14:53', '2025-09-29 10:14:53');

-- --------------------------------------------------------

--
-- Table structure for table `kas_kecil`
--

CREATE TABLE `kas_kecil` (
  `id` int(11) NOT NULL,
  `cabang_id` varchar(50) NOT NULL,
  `periode` varchar(7) NOT NULL,
  `uang_masuk` decimal(15,2) DEFAULT 0.00,
  `uang_keluar` decimal(15,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kas_kecil`
--

INSERT INTO `kas_kecil` (`id`, `cabang_id`, `periode`, `uang_masuk`, `uang_keluar`, `created_at`, `updated_at`) VALUES
(2, 'CBG-001', '2025-09', '0.00', '500000.00', '2025-09-29 10:25:24', '2025-09-29 10:25:24');

-- --------------------------------------------------------

--
-- Table structure for table `pendapatan_offline`
--

CREATE TABLE `pendapatan_offline` (
  `id` varchar(50) NOT NULL,
  `tanggal` date DEFAULT NULL,
  `penjualan_cash` decimal(15,2) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `cabang_id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pendapatan_offline`
--

INSERT INTO `pendapatan_offline` (`id`, `tanggal`, `penjualan_cash`, `created_at`, `updated_at`, `cabang_id`) VALUES
('POF-20250913-0001', '2025-09-13', '300000.00', '2025-09-25 00:18:39', '2025-09-25 00:18:39', NULL),
('POF-20250929-0001', '2025-09-29', '1500000.00', '2025-09-29 16:12:38', '2025-09-29 16:12:38', 'CAB-001'),
('POF-20250929-0002', '2025-09-29', '1500000.00', '2025-09-29 16:13:33', '2025-09-29 16:13:33', 'CBG-001');

-- --------------------------------------------------------

--
-- Table structure for table `pendapatan_online`
--

CREATE TABLE `pendapatan_online` (
  `id` varchar(50) NOT NULL,
  `tanggal` date DEFAULT NULL,
  `total` decimal(15,2) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `cabang_id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pendapatan_online`
--

INSERT INTO `pendapatan_online` (`id`, `tanggal`, `total`, `created_at`, `updated_at`, `cabang_id`) VALUES
('PON-20250913-0001', '2025-09-13', '450000.00', '2025-09-25 00:18:38', '2025-09-25 00:18:38', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pendapatan_online_detail`
--

CREATE TABLE `pendapatan_online_detail` (
  `id` varchar(50) NOT NULL,
  `pendapatan_online_id` varchar(50) DEFAULT NULL,
  `sumber` enum('qris','gofood','grabfood','shopeefood','transfer') DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `jumlah` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pendapatan_online_detail`
--

INSERT INTO `pendapatan_online_detail` (`id`, `pendapatan_online_id`, `sumber`, `keterangan`, `jumlah`) VALUES
('POD-20250913-0001', 'PON-20250913-0001', 'qris', 'QRIS BCA', '200000.00'),
('POD-20250913-0002', 'PON-20250913-0001', 'gofood', 'GoFood Lunch', '150000.00'),
('POD-20250913-0003', 'PON-20250913-0001', 'grabfood', 'GrabFood Dinner', '100000.00');

-- --------------------------------------------------------

--
-- Table structure for table `pengeluaran`
--

CREATE TABLE `pengeluaran` (
  `id` varchar(50) NOT NULL,
  `tanggal` date DEFAULT NULL,
  `total` decimal(15,2) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `cabang_id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengeluaran`
--

INSERT INTO `pengeluaran` (`id`, `tanggal`, `total`, `created_at`, `updated_at`, `cabang_id`) VALUES
('EXP-20250929-0001', '2025-09-29', '500000.00', '2025-09-29 10:25:24', '2025-09-29 15:25:24', 'CBG-001');

-- --------------------------------------------------------

--
-- Table structure for table `pengeluaran_detail`
--

CREATE TABLE `pengeluaran_detail` (
  `id` varchar(50) NOT NULL,
  `pengeluaran_id` varchar(50) DEFAULT NULL,
  `kategori` enum('bahan_baku','gas_listrik','atk','transportasi','lainnya') DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `jumlah` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengeluaran_detail`
--

INSERT INTO `pengeluaran_detail` (`id`, `pengeluaran_id`, `kategori`, `keterangan`, `jumlah`) VALUES
('EXD-20250929-0001', 'EXP-20250929-0001', 'bahan_baku', 'Beli ayam 10kg', '300000.00'),
('EXD-20250929-0002', 'EXP-20250929-0001', 'gas_listrik', 'Token listrik', '200000.00');

-- --------------------------------------------------------

--
-- Table structure for table `total_omset`
--

CREATE TABLE `total_omset` (
  `id` int(11) NOT NULL,
  `cabang_id` varchar(50) NOT NULL,
  `tanggal` date NOT NULL,
  `total_omset_offline` decimal(15,2) DEFAULT 0.00,
  `total_omset_online` decimal(15,2) DEFAULT 0.00,
  `total_omset` decimal(16,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `total_omset`
--

INSERT INTO `total_omset` (`id`, `cabang_id`, `tanggal`, `total_omset_offline`, `total_omset_online`, `total_omset`, `created_at`, `updated_at`) VALUES
(2, 'CBG-001', '2025-09-29', '1500000.00', '0.00', '1500000.00', '2025-09-29 16:13:33', '2025-09-29 16:13:33');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` varchar(50) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('super_admin','admin','manager_resto') DEFAULT NULL,
  `cabang_id` varchar(50) DEFAULT NULL,
  `api_token` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `cabang_id`, `api_token`, `is_deleted`, `created_at`, `updated_at`) VALUES
('1', 'superadmin', '$2a$12$/AihPXfU/vnEX66VY7Yud.qWursf.feM/YwIxsz8.NCBNZZjwBomi', 'super_admin', NULL, NULL, 0, NULL, NULL),
('USR-069', 'manager', '$2y$10$YbCCI0MLwto74jTDKilgWOi/ipYqHpYJgTXOMJgFfbEKOcJLavhgy', 'manager_resto', 'CBG-001', NULL, 0, '2025-09-29 05:14:59', '2025-09-29 05:14:59'),
('USR-68d62dcb6f8d3', 'admin_resto', '$2y$10$VmSEu3wQwwcPETnAHdncCOOAnYltfDrsjhyEGbQxRU/p..BPUVKMO', 'manager_resto', NULL, NULL, 0, '2025-09-26 08:08:11', '2025-09-26 08:08:11'),
('USR-68d633992d09a', 'admin_resto2', '$2y$10$9b1HwO/1WfJfizTTLFSIxuKO3RqFBiyRobZSwkF37/sUanz.nduAi', 'admin', NULL, NULL, 0, '2025-09-26 08:32:57', '2025-09-26 08:32:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cabang`
--
ALTER TABLE `cabang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kas_kecil`
--
ALTER TABLE `kas_kecil`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ux_kas_periode_cabang` (`cabang_id`,`periode`),
  ADD KEY `idx_kas_periode` (`cabang_id`,`periode`);

--
-- Indexes for table `pendapatan_offline`
--
ALTER TABLE `pendapatan_offline`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ux_offline_cabang_tgl` (`cabang_id`,`tanggal`),
  ADD KEY `idx_offline_cabang_tanggal` (`cabang_id`,`tanggal`);

--
-- Indexes for table `pendapatan_online`
--
ALTER TABLE `pendapatan_online`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ux_online_cabang_tgl` (`cabang_id`,`tanggal`),
  ADD KEY `idx_online_cabang_tanggal` (`cabang_id`,`tanggal`);

--
-- Indexes for table `pendapatan_online_detail`
--
ALTER TABLE `pendapatan_online_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pendapatan_online_id` (`pendapatan_online_id`),
  ADD KEY `idx_pod_parent` (`pendapatan_online_id`);

--
-- Indexes for table `pengeluaran`
--
ALTER TABLE `pengeluaran`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ux_pengeluaran_cabang_tgl` (`cabang_id`,`tanggal`),
  ADD KEY `idx_pengeluaran_cabang_tanggal` (`cabang_id`,`tanggal`);

--
-- Indexes for table `pengeluaran_detail`
--
ALTER TABLE `pengeluaran_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengeluaran_id` (`pengeluaran_id`),
  ADD KEY `idx_pend_parent` (`pengeluaran_id`);

--
-- Indexes for table `total_omset`
--
ALTER TABLE `total_omset`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ux_total_per_day` (`cabang_id`,`tanggal`),
  ADD KEY `idx_cabang_tanggal` (`cabang_id`,`tanggal`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_cabang` (`cabang_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kas_kecil`
--
ALTER TABLE `kas_kecil`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `total_omset`
--
ALTER TABLE `total_omset`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pendapatan_online_detail`
--
ALTER TABLE `pendapatan_online_detail`
  ADD CONSTRAINT `pendapatan_online_detail_ibfk_1` FOREIGN KEY (`pendapatan_online_id`) REFERENCES `pendapatan_online` (`id`);

--
-- Constraints for table `pengeluaran_detail`
--
ALTER TABLE `pengeluaran_detail`
  ADD CONSTRAINT `fk_pengeluaran_detail` FOREIGN KEY (`pengeluaran_id`) REFERENCES `pengeluaran` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pengeluaran_detail_ibfk_1` FOREIGN KEY (`pengeluaran_id`) REFERENCES `pengeluaran` (`id`);

--
-- Constraints for table `total_omset`
--
ALTER TABLE `total_omset`
  ADD CONSTRAINT `fk_total_cabang` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_cabang` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
