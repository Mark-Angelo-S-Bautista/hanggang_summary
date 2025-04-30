-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 28, 2025 at 04:46 AM
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
-- Database: `armansalon`
--

-- --------------------------------------------------------

--
-- Table structure for table `form_info`
--

CREATE TABLE `form_info` (
  `id` int(11) NOT NULL,
  `selected_date` date NOT NULL,
  `selected_time` varchar(255) NOT NULL,
  `stylist` varchar(255) NOT NULL,
  `selected_service` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phoneNum` int(20) NOT NULL,
  `gender` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `form_info`
--

INSERT INTO `form_info` (`id`, `selected_date`, `selected_time`, `stylist`, `selected_service`, `username`, `email`, `phoneNum`, `gender`) VALUES
(10, '2025-05-01', '11:00 AM', 'Jasmine', 'Hair Spa', 'Mark Angelo S. Bautista', 'gelo@gmail.com', 123213123, 'male'),
(11, '2025-05-10', '09:00 AM', 'Joenel', 'Shampoo', 'Mark Angelo S. Bautista', 'gelo@gmail.com', 2147483647, 'male');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `form_info`
--
ALTER TABLE `form_info`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `form_info`
--
ALTER TABLE `form_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
