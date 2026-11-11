-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 11, 2026 at 01:50 PM
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
-- Database: `db_mahasiswa`
--

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswaa`
--

CREATE TABLE `mahasiswaa` (
  `id_mahasiswa` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jurusan` varchar(50) NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `telepon` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mahasiswaa`
--

INSERT INTO `mahasiswaa` (`id_mahasiswa`, `nama`, `jurusan`, `alamat`, `telepon`) VALUES
('231011403245', 'Anisa Lukman', 'Teknik Informatika', 'sawarna', '0835677654');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `mahasiswaa`
--
ALTER TABLE `mahasiswaa`
  ADD PRIMARY KEY (`id_mahasiswa`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
