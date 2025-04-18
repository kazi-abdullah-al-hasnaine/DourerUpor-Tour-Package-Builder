-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 18, 2025 at 06:24 AM
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
(1, 'Trip to Rajshahi', '2025-03-27 17:22:19', 1, 'approved', 'Explore the historic charm and cultural richness of Rajshahi with our specially curated day tour package. Known as the “Silk City” and the “Education City” of Bangladesh, Rajshahi offers a perfect blend of heritage, natural beauty, and local craftsmanship.\r\n\r\nThe tour begins with a visit to the Varendra Research Museum, one of the oldest museums in Bangladesh, where you’ll discover ancient artifacts, sculptures, and exhibits showcasing the region’s glorious past. Next, take a walk through Rajshahi University, known for its scenic campus and architectural beauty.\r\n\r\nAfterward, head to the Padma River bank for a relaxing time with stunning views, especially during sunset. A short boat ride on the river gives you a peaceful break and a great chance to capture beautiful moments.\r\n\r\nThe tour also includes a visit to a local silk factory, where you can see how the famous Rajshahi silk is made and even buy some authentic silk products.\r\n\r\nEnd your day at the Shah Makhdum Mazar, a revered reli', '1.jpg', ''),
(2, 'Lets visit cox\'s Bazar', '2025-04-05 16:55:14', 1, 'approved', 'Discover the stunning beauty of Cox’s Bazar, home to the world’s longest natural sea beach, in this unforgettable one-day tour package. Perfect for beach lovers and adventure seekers, this tour offers a mix of relaxation, exploration, and scenic views.\r\n\r\nStart your day with a walk along the Cox’s Bazar Beach, where the golden sands stretch for miles and the waves gently touch your feet. Enjoy the fresh sea breeze and capture the perfect sunrise photo. Then, visit Laboni Point, a popular spot for swimming and beach activities, or just relax with a fresh coconut in hand.\r\n\r\nNext, explore the peaceful Himchari Waterfall and Hill, just a short drive away. The combination of forest, hilltop views, and waterfall scenery makes it a great place for nature lovers. Continue to Inani Beach, known for its unique coral stones and quiet ambiance, ideal for taking stunning pictures and enjoying a peaceful walk.\r\n\r\nEnd your day with some souvenir shopping at the local Burmese Market and enjoy fresh s', '2.jpg', ''),
(3, 'Sylhet day tour', '2025-04-07 06:12:43', 2, 'approved', 'Discover the natural beauty and spiritual charm of Sylhet with our exciting day tour package. Start your journey in Sylhet city, a vibrant cultural hub surrounded by rolling hills and tea gardens. The tour begins with a visit to Hazrat Shahjalal\'s Mazar, a sacred site and pilgrimage destination for many. Experience the serene atmosphere and learn about the legacy of this revered Sufi saint.\r\n\r\nFrom there, we head towards the breathtaking Jaflong, located near the border of India. Known for its scenic landscapes, crystal-clear river, and views of the Meghalaya hills, Jaflong is perfect for nature lovers. You can witness stone collection activities along the river and enjoy a peaceful boat ride across the Dawki River.\r\n\r\nNext, we visit Sadapathor in Bholaganj, a hidden gem known for its white stone beds and turquoise waters. It’s a quiet, photogenic spot ideal for relaxing and soaking in the natural beauty of Sylhet.\r\n\r\nThis full-day tour combines spiritual sites, natural wonders, and lo', '3.jpg', ''),
(4, 'Lets visit khulna', '2025-04-07 06:28:56', 3, 'approved', 'Experience the charm of southern Bangladesh with our exciting **Two-Day Khulna Tour Package**, a perfect blend of history, culture, and nature. This tour is ideal for travelers looking to explore both urban attractions and natural wonders in a short time.\r\nEdit 2\r\n**Day 1** begins in **Khulna city**, where you\'ll visit the **Khulna Divisional Museum**, showcasing regional history and heritage. Next, explore the **Shaheed Hadis Park** and take a stroll along the **Rupsha River**, enjoying the local lifestyle and riverside views. Visit the **Shrine of Khan Jahan Ali** in Bagerhat (UNESCO World Heritage Site), including the iconic **Sixty Dome Mosque (Shat Gambuj Masjid)**, a masterpiece of medieval Islamic architecture.\r\nEdit\r\n**Day 2** is dedicated to a half-day adventure to the edge of the **Sundarbans**, the world’s largest mangrove forest. You’ll enjoy a peaceful boat ride through the narrow creeks, observe wildlife like monkeys and birds, and experience the unique beauty of this UN', '4.jpg', ''),
(9, 'Bhola city tour', '2025-04-17 04:41:02', 2, 'approved', '🌆 Bhola City Tour – Nature, Heritage & Coastal Beauty in One Journey!\r\n\r\nTour Overview;-\r\n\r\nDiscover the hidden gems of southern Bangladesh with our Bhola City Tour — a perfect blend of scenic landscapes, historical wonders, and coastal charm. This day-long journey takes you through the heart of Bhola, exploring iconic landmarks and nature’s serene beauty. Whether you\'re a history buff, nature lover, or just looking for a peaceful escape, this tour promises an unforgettable experience.\r\n\r\n📍 Tour Highlights:\r\n\r\n🗼 Jacob Tower\r\nStart your adventure with a panoramic view from the tallest watchtower in Bangladesh! Standing proudly in Char Fasson, Jacob Tower offers breathtaking 360° views of the island\'s rivers, green fields, and coastal horizon. It’s a photographer’s dream and the perfect spot to kick off your tour.\r\n\r\n🌊 Bells Park Lake\r\nNext, unwind by the peaceful Bells Park Lake, a tranquil oasis in the heart of Bhola town. Enjoy a leisurely stroll along its banks, spot local birds, or simply relax under the shade of ancient trees. The lake’s calm waters and green surroundings offer a refreshing break from the bustle of daily life.\r\n\r\n🏝️ Char Kukrimukri\r\nEnd the day with an exciting trip to Char Kukrimukri — a magical coastal island known for its pristine beaches, rich biodiversity, and adventurous mangrove forests. You might even spot deer, exotic birds, and crabs as you explore this untouched paradise. A must-visit for eco-tourists and adventure seekers!', '9.jpg', ''),
(10, 'bhola tour', '2025-04-17 08:03:22', 2, 'pending', 'j,zdhfgr hjrbgehjrgr hhh', '10.jpg', ''),
(11, 'ddddddddddd', '2025-04-18 03:15:49', 7, 'rejected', 'gegjegjbgcccccccccc', '11.jpg', 'ddddddddd');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`package_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `package_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
