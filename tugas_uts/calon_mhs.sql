-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 28, 2026 at 01:33 PM
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
-- Database: `calon_mhs`
--

-- --------------------------------------------------------

--
-- Table structure for table `calon_mhs`
--

CREATE TABLE `calon_mhs` (
  `id` int(10) UNSIGNED NOT NULL,
  `kode` varchar(30) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `jenis_kelamin` varchar(20) NOT NULL,
  `ttl` varchar(100) NOT NULL,
  `asal_sekolah` varchar(150) NOT NULL,
  `pekerjaan_ortu` varchar(100) DEFAULT NULL,
  `matematika` decimal(5,2) NOT NULL,
  `inggris` decimal(5,2) NOT NULL,
  `umum` decimal(5,2) NOT NULL,
  `rata_rata` decimal(5,2) NOT NULL,
  `keterangan` varchar(30) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `calon_mhs`
--

INSERT INTO `calon_mhs` (`id`, `kode`, `nama`, `jenis_kelamin`, `ttl`, `asal_sekolah`, `pekerjaan_ortu`, `matematika`, `inggris`, `umum`, `rata_rata`, `keterangan`, `created_at`) VALUES
(1, 'A2-101-9', 'Anisa Lukman', 'Perempuan', 'Lebak, 27 Juli 2005', 'SMAN 2 BAYAH', 'WIRAUSAHA', 90.00, 80.00, 100.00, 90.00, 'Lulus', '2026-10-28 12:20:12'),
(2, 'B2-102-6', 'Firman Ardiansyah', 'Laki-laki', 'Depok, 10 Januari 2005', 'SMAN 1 DEPOK', 'WIRAUSAHA', 100.00, 90.00, 80.00, 90.00, 'Lulus', '2026-10-28 12:24:14'),
(3, 'V5-103-5', 'Salsabila Sucy', 'Perempuan', 'TANGERANG, 28 OKTOBER 2005', 'SMAN 1 TANGERANG', 'WIRAUSAHA', 50.00, 40.00, 30.00, 40.00, 'Tidak Lulus', '2026-10-28 12:25:45'),
(4, 'B3-104-12', 'Bagas Kurnia Ramadhan', 'Laki-laki', 'Tangerang, 17 November 2004', 'SMAN 1 TANGERANG', 'WIRAUSAHA', 70.00, 50.00, 80.00, 66.67, 'Cadangan', '2026-10-28 12:31:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `calon_mhs`
--
ALTER TABLE `calon_mhs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode` (`kode`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `calon_mhs`
--
ALTER TABLE `calon_mhs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
