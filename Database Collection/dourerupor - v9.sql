-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 17, 2025 at 02:15 PM
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
(12, 'Hotel Cox Today', 'Bangladesh', 'Hotel', 12000),
(13, 'Sylhet', 'Bangladesh', 'City', 0),
(14, 'Jaflong', 'Bangladesh', 'Destination', 0),
(15, 'Sadapathor', 'Bangladesh', 'Destination', 0),
(16, 'Hazrat Shahjalal Rah. Mazar Sharif', 'Bangladesh', 'Destination', 0),
(17, 'Dhaka', 'Bangladesh', 'City', 0),
(18, 'Khulna', 'Bangladesh', 'City', 0),
(19, 'Sixty Dome Mosque', 'Bangladesh', 'Destination', 0),
(20, 'Sundarbans Mangrove Forest', 'Bangladesh', 'Destination', 0),
(21, 'Khan Jahan Ali’s Shrine', 'Bangladesh', 'Destination', 100),
(22, 'Rupsha River', 'Bangladesh', 'Destination', 0),
(23, 'Khulna Divisional Museum', 'Bangladesh', 'Destination', 50),
(24, 'Bhola', 'Bangladesh', 'City', 0),
(25, 'Jakob Tower', 'Bangladesh', 'Landmarks', 0),
(26, 'Bels Park Lake - Bhola', 'Bangladesh', 'Lake', 0),
(28, 'Char kukrimukri - Bhola', 'Bangladesh', 'Destination', 0),
(30, 'Durga Sagar - Bhola', 'Bangladesh', 'River', 0);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL,
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `package_id`, `message`, `created_at`, `is_read`) VALUES
(1, 5, 4, 'Package \'Lets visit khulna\' has been updated with new details.', '2025-04-10 14:33:18', 0),
(2, 9, 4, 'Package \'Lets visit khulna\' has been updated with new details.', '2025-04-10 14:33:18', 1),
(3, 5, 4, 'Package \'Lets visit khulna\' has been updated with new details.', '2025-04-10 14:35:07', 0),
(4, 9, 4, 'Package \'Lets visit khulna\' has been updated with new details.', '2025-04-10 14:35:07', 1),
(5, 5, 4, 'Package \'Lets visit khulna\' has been updated with new details.', '2025-04-10 14:44:48', 0),
(6, 9, 4, 'Package \'Lets visit khulna\' has been updated with new details.', '2025-04-10 14:44:48', 1),
(7, 5, 4, 'Package \'Lets visit khulna\' has been updated with new details.', '2025-04-10 14:46:50', 0),
(8, 9, 4, 'Package \'Lets visit khulna\' has been updated with new details.', '2025-04-10 14:46:50', 1),
(9, 5, 4, 'Package \'Lets visit khulna\' has been updated with new details.', '2025-04-10 15:36:39', 0),
(10, 9, 4, 'Package \'Lets visit khulna\' has been updated with new details.', '2025-04-10 15:36:39', 1),
(11, 5, 4, 'Package \'Lets visit khulna\' has been updated with new details.', '2025-04-10 15:37:23', 0),
(12, 9, 4, 'Package \'Lets visit khulna\' has been updated with new details.', '2025-04-10 15:37:23', 1),
(24, 3, 4, 'Your package \'Lets visit khulna\' has been approved.', '2025-04-17 00:21:07', 0),
(25, 2, 9, 'Your package \'Bhola city tour\' has been approved.', '2025-04-17 10:41:51', 1),
(26, 11, 9, 'Package \'Bhola city tour\' has been updated and is pending approval.', '2025-04-17 10:52:05', 0),
(27, 2, 9, 'Your package \'Bhola city tour\' has been approved.', '2025-04-17 10:53:00', 1),
(28, 2, 10, 'Your package \'bhola tour\' has been approved.', '2025-04-17 14:05:22', 1),
(29, 2, 10, 'Package \'bhola tour\' has been updated and is pending approval.', '2025-04-17 14:10:24', 1);

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
  `details` varchar(2000) NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT 'unknown.jpg',
  `rejection_feedback` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`package_id`, `package_name`, `publish_time`, `build_by`, `status`, `details`, `image`, `rejection_feedback`) VALUES
(1, 'Trip to Rajshahi', '2025-03-27 17:22:19', 1, 'Approved', 'Explore the historic charm and cultural richness of Rajshahi with our specially curated day tour package. Known as the “Silk City” and the “Education City” of Bangladesh, Rajshahi offers a perfect blend of heritage, natural beauty, and local craftsmanship.\r\n\r\nThe tour begins with a visit to the Varendra Research Museum, one of the oldest museums in Bangladesh, where you’ll discover ancient artifacts, sculptures, and exhibits showcasing the region’s glorious past. Next, take a walk through Rajshahi University, known for its scenic campus and architectural beauty.\r\n\r\nAfterward, head to the Padma River bank for a relaxing time with stunning views, especially during sunset. A short boat ride on the river gives you a peaceful break and a great chance to capture beautiful moments.\r\n\r\nThe tour also includes a visit to a local silk factory, where you can see how the famous Rajshahi silk is made and even buy some authentic silk products.\r\n\r\nEnd your day at the Shah Makhdum Mazar, a revered reli', '1.jpg', ''),
(2, 'Lets visit cox\'s Bazar', '2025-04-05 16:55:14', 1, 'Approved', 'Discover the stunning beauty of Cox’s Bazar, home to the world’s longest natural sea beach, in this unforgettable one-day tour package. Perfect for beach lovers and adventure seekers, this tour offers a mix of relaxation, exploration, and scenic views.\r\n\r\nStart your day with a walk along the Cox’s Bazar Beach, where the golden sands stretch for miles and the waves gently touch your feet. Enjoy the fresh sea breeze and capture the perfect sunrise photo. Then, visit Laboni Point, a popular spot for swimming and beach activities, or just relax with a fresh coconut in hand.\r\n\r\nNext, explore the peaceful Himchari Waterfall and Hill, just a short drive away. The combination of forest, hilltop views, and waterfall scenery makes it a great place for nature lovers. Continue to Inani Beach, known for its unique coral stones and quiet ambiance, ideal for taking stunning pictures and enjoying a peaceful walk.\r\n\r\nEnd your day with some souvenir shopping at the local Burmese Market and enjoy fresh s', '2.jpg', ''),
(3, 'Sylhet day tour', '2025-04-07 06:12:43', 2, 'Approved', 'Discover the natural beauty and spiritual charm of Sylhet with our exciting day tour package. Start your journey in Sylhet city, a vibrant cultural hub surrounded by rolling hills and tea gardens. The tour begins with a visit to Hazrat Shahjalal\'s Mazar, a sacred site and pilgrimage destination for many. Experience the serene atmosphere and learn about the legacy of this revered Sufi saint.\r\n\r\nFrom there, we head towards the breathtaking Jaflong, located near the border of India. Known for its scenic landscapes, crystal-clear river, and views of the Meghalaya hills, Jaflong is perfect for nature lovers. You can witness stone collection activities along the river and enjoy a peaceful boat ride across the Dawki River.\r\n\r\nNext, we visit Sadapathor in Bholaganj, a hidden gem known for its white stone beds and turquoise waters. It’s a quiet, photogenic spot ideal for relaxing and soaking in the natural beauty of Sylhet.\r\n\r\nThis full-day tour combines spiritual sites, natural wonders, and lo', '3.jpg', ''),
(4, 'Lets visit khulna', '2025-04-07 06:28:56', 3, 'approved', 'Experience the charm of southern Bangladesh with our exciting **Two-Day Khulna Tour Package**, a perfect blend of history, culture, and nature. This tour is ideal for travelers looking to explore both urban attractions and natural wonders in a short time.\r\nEdit 2\r\n**Day 1** begins in **Khulna city**, where you\'ll visit the **Khulna Divisional Museum**, showcasing regional history and heritage. Next, explore the **Shaheed Hadis Park** and take a stroll along the **Rupsha River**, enjoying the local lifestyle and riverside views. Visit the **Shrine of Khan Jahan Ali** in Bagerhat (UNESCO World Heritage Site), including the iconic **Sixty Dome Mosque (Shat Gambuj Masjid)**, a masterpiece of medieval Islamic architecture.\r\nEdit\r\n**Day 2** is dedicated to a half-day adventure to the edge of the **Sundarbans**, the world’s largest mangrove forest. You’ll enjoy a peaceful boat ride through the narrow creeks, observe wildlife like monkeys and birds, and experience the unique beauty of this UN', '4.jpg', ''),
(9, 'Bhola city tour', '2025-04-17 04:41:02', 2, 'approved', '🌆 Bhola City Tour – Nature, Heritage & Coastal Beauty in One Journey!\r\n\r\nTour Overview;-\r\n\r\nDiscover the hidden gems of southern Bangladesh with our Bhola City Tour — a perfect blend of scenic landscapes, historical wonders, and coastal charm. This day-long journey takes you through the heart of Bhola, exploring iconic landmarks and nature’s serene beauty. Whether you\'re a history buff, nature lover, or just looking for a peaceful escape, this tour promises an unforgettable experience.\r\n\r\n📍 Tour Highlights:\r\n\r\n🗼 Jacob Tower\r\nStart your adventure with a panoramic view from the tallest watchtower in Bangladesh! Standing proudly in Char Fasson, Jacob Tower offers breathtaking 360° views of the island\'s rivers, green fields, and coastal horizon. It’s a photographer’s dream and the perfect spot to kick off your tour.\r\n\r\n🌊 Bells Park Lake\r\nNext, unwind by the peaceful Bells Park Lake, a tranquil oasis in the heart of Bhola town. Enjoy a leisurely stroll along its banks, spot local birds, or simply relax under the shade of ancient trees. The lake’s calm waters and green surroundings offer a refreshing break from the bustle of daily life.\r\n\r\n🏝️ Char Kukrimukri\r\nEnd the day with an exciting trip to Char Kukrimukri — a magical coastal island known for its pristine beaches, rich biodiversity, and adventurous mangrove forests. You might even spot deer, exotic birds, and crabs as you explore this untouched paradise. A must-visit for eco-tourists and adventure seekers!', '9.jpg', ''),
(10, 'bhola tour', '2025-04-17 08:03:22', 2, 'pending', 'j,zdhfgr hjrbgehjrgr hhh', '10.jpg', '');

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
(2, 12, 100, 1, 1, 1, 'Bus', 12000),
(2, 10, 0, 1, 2, 12, 'Bike', 100),
(2, 11, 0, 1, 3, 10, 'Bike', 100),
(1, 4, 0, 1, 1, NULL, NULL, 0),
(1, 2, 0, 1, 2, 4, 'Bike', 30),
(4, 18, 0, 1, 1, 17, 'Train', 450),
(4, 18, 43, 2, 2, 12, 'Bus', 34),
(4, 2, 45, 1, 3, 10, 'Bus', 3),
(3, 13, 0, 1, 1, 17, 'Bus', 600),
(9, 24, 0, 1, 1, 17, 'Bus', 400),
(9, 25, 0, 1, 2, 24, 'Bike', 50),
(9, 26, 0, 1, 3, 25, 'Bus', 20),
(9, 28, 0, 1, 4, 26, 'Bus', 20),
(10, 26, 0, 1, 1, 17, 'bus', 400);

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
(2, 5, '2025-04-07 12:39:56'),
(4, 5, '2025-04-07 12:40:05'),
(2, 1, '2025-04-07 12:43:57'),
(1, 1, '2025-04-07 12:44:06'),
(2, 9, '2025-04-10 08:24:46'),
(4, 9, '2025-04-10 08:26:57'),
(4, 2, '2025-04-14 10:43:06'),
(2, 11, '2025-04-16 17:13:21'),
(9, 11, '2025-04-17 04:51:33'),
(9, 2, '2025-04-17 04:53:40'),
(9, 1, '2025-04-17 04:54:08'),
(9, 7, '2025-04-17 04:55:23'),
(2, 2, '2025-04-17 08:00:49'),
(10, 2, '2025-04-17 08:10:11');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL DEFAULT 5,
  `review` varchar(300) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `review_publish_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `package_id`, `rating`, `review`, `user_id`, `review_publish_time`) VALUES
(1, 2, 5, 'Absolutely loved this tour! the local experiences were unforgettable.', 7, '2025-04-01 14:23:00'),
(3, 3, 2, 'Was expecting more based on the package description. The hotel was subpar and some attractions were closed.', 3, '2025-04-04 17:45:00'),
(4, 4, 3, 'Mediocre experience. While the nature spots were breathtaking, the service wasn’t up to the mark.', 5, '2025-04-06 12:30:00'),
(5, 1, 5, 'Incredible trip! Every detail was handled perfectly. Would definitely recommend to anyone wanting a stress-free vacation.', 5, '2025-04-07 08:10:00'),
(6, 1, 1, 'Really disappointed. The bus was late, no proper communication, and the meals included were terrible.', 7, '2025-04-07 21:50:00'),
(7, 3, 4, 'A well-organized tour with stunning scenery. Just wish we had a little more free time to explore.', 1, '2025-04-08 09:05:00'),
(8, 4, 5, 'This was hands-down the best tour I’ve ever taken. Friendly staff, seamless logistics, and amazing views.', 1, '2025-04-09 16:40:00'),
(9, 4, 2, 'I had high hopes based on the reviews, but sadly it didn’t deliver.', 2, '2025-04-08 13:00:00'),
(10, 2, 1, 'The whole experience was chaotic. The pickup was late, the hotel wasn’t as described.', 3, '2025-04-05 11:20:00'),
(11, 1, 4, 'Great experience overall. The locations were beautiful, and everything went smoothly. Just wish the hotel had better Wi-Fi.', 2, '2025-04-06 12:30:00'),
(12, 3, 5, 'Best vacation I’ve had in a while! Everything was top-notch — the views, the food, the people. Will definitely book again.', 7, '2025-04-07 17:10:00'),
(13, 2, 5, 'This was exactly the break I needed. Peaceful locations, comfortable transport, and fantastic service from start to finish.', 1, '2025-04-08 08:20:00'),
(14, 4, 4, 'Well-organized trip with a good balance of activities and free time. Loved the experiences included in the package.', 7, '2025-04-09 14:50:00'),
(20, 3, 1, 'niceeeeee', 2, '2025-04-12 18:38:35'),
(27, 2, 1, 'Informations are incorrect', 2, '2025-04-14 16:37:33'),
(28, 2, 1, 'not    Sheiii mamaamaaaamama', 11, '2025-04-16 23:13:32'),
(30, 9, 3, 'The information were correct. It was really helpful. Recommended for one day tour.', 11, '2025-04-17 10:51:27'),
(31, 9, 2, 'Informations are old.', 7, '2025-04-17 10:57:12');

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
(4, 'Tahshan Jamil Shadhin', 'tj.shadhin001@gmail.com', '', '0000-00-00', ''),
(5, 'mojo', 'mojo@gmail.com', '123', '2025-04-01', 'Bangladesh'),
(6, 'Uma Banik 2233679042', 'uma.banik@northsouth.edu', '', '0000-00-00', ''),
(7, 'Uma Banik', 'uma.banik@gmail.com', '1234', '2004-06-02', 'Bangladesh'),
(9, 'Kazi Abdullah Al Hasnaine 2211688642', 'kazi.hasnaine@northsouth.edu', '', '0000-00-00', ''),
(10, 'Knocked Down', 'kamrulhasanshimul420@gmail.com', '', '0000-00-00', ''),
(11, 'Md. Wajih Awsaf', 'awsafwajih@gmail.com', '1234', '2025-06-03', 'UK');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `destinations`
--
ALTER TABLE `destinations`
  ADD PRIMARY KEY (`destination_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `package_id` (`package_id`);

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
  MODIFY `destination_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `package_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `packages` (`package_id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
