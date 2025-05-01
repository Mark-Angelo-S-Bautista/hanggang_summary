-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 01, 2025 at 09:51 AM
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
  `gender` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Scheduled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `form_info`
--

INSERT INTO `form_info` (`id`, `selected_date`, `selected_time`, `stylist`, `selected_service`, `username`, `email`, `phoneNum`, `gender`, `status`) VALUES
(3, '2025-05-09', '11:00 AM', 'Mark', 'Shampoo', 'Elisha', 'elisha@gmail.com', 2147483647, 'female', 'Scheduled'),
(4, '2025-05-09', '09:00 AM', 'Joenel', 'Shampoo', 'Adriel', 'ad@gmail.com', 2147483647, 'male', 'Scheduled'),
(20, '2025-05-08', '11:00 AM', 'Jasmine', 'Hair Spa', 'arukimm', 'jasminee@gmail.com', 2147483647, 'female', 'Scheduled'),
(21, '2025-05-07', '11:00 AM', 'Jasmine', 'Hair Spa', 'ad', 'ad@gmail.com', 2147483647, 'female', 'Scheduled'),
(22, '2025-06-05', '11:00 AM', 'Jasmine', 'Hair Spa', 'Adriel Lumanlan', 'ad@gmail.com', 2147483647, 'male', 'Scheduled'),
(23, '2025-05-09', '11:00 AM', 'Jasmine', 'Shampoo', 'arukimm', 'jasminee@gmail.com', 2147483647, 'female', 'Scheduled'),
(24, '2025-05-09', '11:00 AM', 'Jasmine', 'Shampoo', 'arukimm', 'jasminee@gmail.com', 2147483647, 'female', 'Scheduled'),
(25, '2025-05-09', '11:00 AM', 'Jasmine', 'Shampoo', 'memen2', 'jasminee@gmail.com', 2147483647, 'female', 'Scheduled'),
(26, '2025-05-10', '11:00 AM', 'Jasmine', 'Shampoo', 'rhys456@yatto.cfd', 'johndaniel.sarmiento.3@bulsu.edu.ph', 2147483647, 'female', 'Scheduled'),
(27, '2025-05-10', '11:00 AM', 'Jasmine', 'Shampoo', 'rhys456@yatto.cfd', 'johndaniel.sarmiento.3@bulsu.edu.ph', 2147483647, 'female', 'Scheduled'),
(28, '2025-05-10', '11:00 AM', 'Jasmine', 'Shampoo', 'Jasmine Santiago', 'jasminee@gmail.com', 2147483647, 'female', 'Scheduled'),
(29, '2025-05-01', '11:00 AM', 'Jasmine', 'Shampoo', 'jas', 'ad@gmail.com', 2147483647, 'female', 'Scheduled'),
(30, '2025-05-08', '11:00 AM', 'Jasmine', 'Shampoo', 'Adriel', 'ad@gmail.com', 2147483647, 'female', 'Scheduled'),
(31, '2025-05-09', '11:00 AM', 'Jasmine', 'Shampoo', 'Adriel', 'ad@gmail.com', 2147483647, 'male', 'Scheduled'),
(32, '2025-05-10', '11:00 AM', 'Jasmine', 'Shampoo', 'jas', 'ad@gmail.com', 2147483647, 'female', 'Scheduled'),
(33, '2025-06-01', '09:00 AM', 'Mark', 'Shampoo', 'Serena van der Woodsen', 'serena.vdw@vanderwoodsen.com', 2147483647, 'female', 'Scheduled');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
