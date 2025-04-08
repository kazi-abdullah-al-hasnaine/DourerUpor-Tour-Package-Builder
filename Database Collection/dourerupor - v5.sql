-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 05, 2025 at 07:29 PM
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
(8, 'Hotel x', 'Bangladesh', 'Hotel', 4000),
(9, 'Sugondha Beach', 'Bangladesh', 'Beach', 0),
(10, 'Himchori', 'Bangladesh', 'Mountain', 20),
(11, 'Inani Beach', 'Bangladesh', 'Beach', 0),
(12, 'Hotel Cox Today', 'Bangladesh', 'Hotel', 12000);

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
(13, 'Lets visit cox\'s Bazar', '2025-04-05 16:55:14', 1, 'pending', 'La la la ala ala!! Let\'s goooooo!!!!', 'unknown.jpg');

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
  `pickup` int(11) DEFAULT NULL,
  `transport_type` varchar(255) DEFAULT NULL,
  `cost` decimal(10,0) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `package_details`
--

INSERT INTO `package_details` (`package_id`, `destination_id`, `money_saved`, `day_count`, `step_number`, `pickup`, `transport_type`, `cost`) VALUES
(13, 12, 100, 1, 1, 1, 'Bus', 12000),
(13, 10, 0, 1, 2, 12, 'Bike', 100),
(13, 11, 0, 1, 3, 10, 'Bike', 100);

-- --------------------------------------------------------

--
-- Table structure for table `package_followers`
--

CREATE TABLE `package_followers` (
  `package_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `package_followers`
--

INSERT INTO `package_followers` (`package_id`, `user_id`, `time`) VALUES
(13, 2, '2025-04-05 17:27:09');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL DEFAULT 5,
  `review` varchar(300) DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `package_id`, `rating`, `review`, `user_id`) VALUES
(1, 13, 4, 'baddd', 2);

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
(3, 'Sunny', 'asaduzzaman.sunny@northsouth.edu', '1234', '2020-02-02', 'Bangladesh'),
(4, 'Tahshan Jamil Shadhin', 'tj.shadhin001@gmail.com', '', '0000-00-00', '');

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
-- Indexes for table `package_details`
--
ALTER TABLE `package_details`
  ADD KEY `Package_id_fk` (`package_id`),
  ADD KEY `destination_id_fk` (`destination_id`),
  ADD KEY `pickup_id_fk` (`pickup`);

--
-- Indexes for table `package_followers`
--
ALTER TABLE `package_followers`
  ADD KEY `packageID_FK` (`package_id`),
  ADD KEY `userID_FK` (`user_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `package_id_fkk` (`package_id`),
  ADD KEY `user_id_fkk` (`user_id`);

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
  MODIFY `destination_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `package_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `package_details`
--
ALTER TABLE `package_details`
  ADD CONSTRAINT `Package_id_fk` FOREIGN KEY (`package_id`) REFERENCES `packages` (`package_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `destination_id_fk` FOREIGN KEY (`destination_id`) REFERENCES `destinations` (`destination_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pickup_id_fk` FOREIGN KEY (`pickup`) REFERENCES `destinations` (`destination_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `package_followers`
--
ALTER TABLE `package_followers`
  ADD CONSTRAINT `packageID_FK` FOREIGN KEY (`package_id`) REFERENCES `packages` (`package_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `userID_FK` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `package_id_fkk` FOREIGN KEY (`package_id`) REFERENCES `packages` (`package_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_id_fkk` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
