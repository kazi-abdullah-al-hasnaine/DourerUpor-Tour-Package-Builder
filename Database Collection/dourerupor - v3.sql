-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 27, 2025 at 06:32 PM
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
-- Database: `dourerupor`
--

-- --------------------------------------------------------

--
-- Table structure for table `destinations`
--

CREATE TABLE `destinations` (
  `destination_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL DEFAULT 'Bangladesh',
  `type` varchar(255) DEFAULT 'Destination',
  `Cost` decimal(10,0) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `destinations`
--

INSERT INTO `destinations` (`destination_id`, `name`, `country`, `type`, `Cost`) VALUES
(1, 'Rajshahi', 'Bangladesh', 'City', 0),
(2, 'Padma Garden', 'Bangladesh', 'Park', 0),
(3, 'Puthia Rajbari', 'Bangladesh', 'Landmark', 20),
(4, 'Borendro Museum', 'Bangladesh', 'Museum', 40),
(8, 'Hotel x', 'Bangladesh', 'Hotel', 4000);

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `package_id` int(11) NOT NULL,
  `package_name` varchar(255) NOT NULL,
  `publish_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `build_by` int(11) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`package_id`, `package_name`, `publish_time`, `build_by`, `status`) VALUES
(1, 'Trip to Rajshahi', '2025-03-27 17:22:19', 1, 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `package_details`
--

CREATE TABLE `package_details` (
  `package_id` int(11) NOT NULL,
  `destination_id` int(11) NOT NULL,
  `money_saved` decimal(10,0) NOT NULL DEFAULT 0,
  `day_count` int(11) NOT NULL,
  `step_number` int(11) NOT NULL,
  `pickup` varchar(255) DEFAULT NULL,
  `transport_type` varchar(255) DEFAULT NULL,
  `cost` decimal(10,0) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `package_details`
--

INSERT INTO `package_details` (`package_id`, `destination_id`, `money_saved`, `day_count`, `step_number`, `pickup`, `transport_type`, `cost`) VALUES
(1, 1, 50, 1, 1, NULL, NULL, 0),
(1, 2, 10, 1, 2, NULL, NULL, 0),
(1, 2, 0, 2, 3, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `country` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `password`, `dob`, `country`) VALUES
(1, 'Kazi Abdullah Al Hasnaine', 'kazi.hasnaine2000@gmail.com', '123', '2025-03-26', 'USA'),
(8, 'Awsaf', 'awsafwajih@gmail.com', '123', '2025-03-27', 'Australia'),
(10, 'Tahshan Jamil Shadhin', 'tj.shadhin001@gmail.com', '', '0000-00-00', ''),
(11, 'shadhin', 'shadhin001@gmail.com', '123', '2025-03-12', 'UK'),
(12, 'Tahshan Jamil Shadhin 2231571642', 'tahshan.shadhin@northsouth.edu', '', '0000-00-00', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `destinations`
--
ALTER TABLE `destinations`
  ADD PRIMARY KEY (`destination_id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`package_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `destinations`
--
ALTER TABLE `destinations`
  MODIFY `destination_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `package_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
