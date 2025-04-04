-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 04, 2025 at 08:56 PM
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
  `cost` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `destinations`
--

INSERT INTO `destinations` (`destination_id`, `name`, `country`, `type`, `cost`) VALUES
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
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `details` varchar(1000) NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT 'unknown.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`package_id`, `package_name`, `publish_time`, `build_by`, `status`, `details`, `image`) VALUES
(1, 'Trip to Rajshahi', '2025-03-27 17:22:19', 1, 'pending', '', 'unknown.jpg'),
(2, 'After Design pattern', '2025-04-02 16:51:11', 0, 'Pending', '', 'unknown.jpg'),
(3, 'hello', '2025-04-02 16:52:24', 0, 'Pending', '', 'unknown.jpg'),
(5, 'After Design pattern', '2025-04-02 17:09:15', 0, 'Pending', '', 'unknown.jpg'),
(6, 'After Design pattern', '2025-04-03 10:36:41', 0, 'Pending', 'Hello!!Hello!!', 'unknown.jpg'),
(7, 'hello', '2025-04-03 10:38:58', 0, 'Pending', 'hello', 'unknown.jpg'),
(8, 'After Design pattern', '2025-04-03 10:39:33', 0, 'Pending', 'hello', 'unknown.jpg'),
(9, 'After Design pattern', '2025-04-03 10:56:24', 0, 'Pending', 'hello', 'unknown.jpg'),
(10, 'bbb', '2025-04-03 11:02:28', 0, 'Pending', 'hello', 'unknown.jpg'),
(11, 'bbb', '2025-04-03 11:04:32', 0, 'Pending', 'hello', 'unknown.jpg'),
(12, 'After Design pattern', '2025-04-04 18:47:45', 0, 'Pending', 'hello', 'unknown.jpg');

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
(5, 1, 1, 1, 1, '', '', 0),
(6, 8, 1, 1, 1, '', '', 4000),
(7, 8, 1, 1, 1, '', '', 4000),
(8, 3, 1, 1, 1, 'dhaka', 'bus', 4000),
(8, 8, 2, 2, 2, '', '', 20),
(9, 4, 1, 1, 1, '', '', 40),
(10, 1, 2, 1, 1, '', '', 4000),
(10, 8, 0, 2, 2, '', '', 0),
(11, 1, 2, 1, 1, '', '', 4000),
(11, 8, 0, 2, 2, '', '', 0),
(12, 8, 1, 1, 1, '', '', 4000);

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
(2, 'Tahshan Jamil Shadhin', 'shadhin001@gmail.com', '123', '2025-03-27', 'India'),
(3, 'Sunny', 'asaduzzaman.sunny@northsouth.edu', '1234', '2020-02-02', 'Bangladesh');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`package_id`);

--
-- Indexes for table `package_details`
--
ALTER TABLE `package_details`
  ADD KEY `Package_id_fk` (`package_id`);

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
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `package_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `package_details`
--
ALTER TABLE `package_details`
  ADD CONSTRAINT `Package_id_fk` FOREIGN KEY (`package_id`) REFERENCES `packages` (`package_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
