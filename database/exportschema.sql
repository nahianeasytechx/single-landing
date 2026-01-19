-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 10, 2026 at 12:32 PM
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
-- Database: `sidratul_muntaha`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `objectives` text NOT NULL,
  `short_description` text NOT NULL,
  `description` text NOT NULL,
  `type` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `image` varchar(255) DEFAULT NULL,
  `sections_data` text DEFAULT NULL COMMENT 'JSON data for dynamic sections',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`id`, `title`, `objectives`, `short_description`, `description`, `type`, `status`, `image`, `sections_data`, `created_at`, `updated_at`) VALUES
(21, 'this is test activity', 'Objectives (Activity Goals)', 'Short Description', 'Detailed Description', 'Regular', 'Active', NULL, '[\n    {\n        \"title\": \"1st section\",\n        \"items\": [\n            \"this is item 1\"\n        ]\n    }\n]', '2026-01-10 06:04:36', '2026-01-10 06:04:36'),
(22, 'this is test activity', 'Objectives (Activity Goals)', 'Short Description', 'Detailed Description', 'Regular', 'Active', NULL, '[\n    {\n        \"title\": \"1st section\",\n        \"items\": [\n            \"this is item 1\"\n        ]\n    }\n]', '2026-01-10 06:06:55', '2026-01-10 06:06:55'),
(23, 'this is test activity', 'Objectives (Activity Goals)', 'Short Description', 'Detailed Description', 'Regular', 'Active', NULL, '[\n    {\n        \"title\": \"1st section\",\n        \"items\": [\n            \"this is item 1\"\n        ]\n    }\n]', '2026-01-10 06:08:00', '2026-01-10 06:08:00'),
(24, 'this is test activity 2', 'Objectives (Activity Goals)', 'Short Description', 'Detailed Description', 'Regular', 'Active', NULL, NULL, '2026-01-10 06:08:50', '2026-01-10 06:08:50'),
(25, 'this is test activity 2', 'Objectives (Activity Goals)', 'Short Description', 'Detailed Description', 'Regular', 'Active', NULL, NULL, '2026-01-10 06:10:15', '2026-01-10 06:10:15'),
(26, 'this is test activity 3', 'Objectives (Activity Goals)', 'Short Description', 'Detailed Description', 'Regular', 'Active', NULL, NULL, '2026-01-10 06:11:09', '2026-01-10 06:11:09'),
(28, 'this is test activity 4', 'Objectives (Activity Goals)', 'Short Description', 'Detailed Description', 'Financial', 'Active', NULL, '[\n    {\n        \"title\": \"1st one\",\n        \"items\": [\n            \"this is item 1\",\n            \"this is item 2\",\n            \"this is item 3\"\n        ]\n    },\n    {\n        \"title\": \"2nd one\",\n        \"items\": [\n            \"this is item 1\",\n            \"this is item 2\"\n        ]\n    },\n    {\n        \"title\": \"3rd\",\n        \"items\": [\n            \"this is item 1\"\n        ]\n    }\n]', '2026-01-10 06:14:59', '2026-01-10 06:14:59'),
(29, 'this is test img', 'asd', 'asdfsaf', 'asdgasd', 'Financial', 'Active', '6961f2cfbc477_3492.jpg', NULL, '2026-01-10 06:19:35', '2026-01-10 06:41:16'),
(30, 'afd', 'asdfsadas', 'dfasfa', 'sdfasf', 'Regular', 'Draft', '6961f79bce303_iPhone-17-white-4953.webp', '[\n    {\n        \"title\": \"1st one\",\n        \"items\": [\n            \"this is item 1\",\n            \"this is item 2\",\n            \"this is item 3\"\n        ]\n    },\n    {\n        \"title\": \"2nd one\",\n        \"items\": [\n            \"this is item 1\",\n            \"this is item 2\",\n            \"this is item 3\"\n        ]\n    }\n]', '2026-01-10 06:54:19', '2026-01-10 06:57:37');

-- --------------------------------------------------------

--
-- Table structure for table `donation_categories`
--

CREATE TABLE `donation_categories` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `image` varchar(500) NOT NULL,
  `description` text NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `donation_categories`
--

INSERT INTO `donation_categories` (`id`, `title`, `image`, `description`, `status`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'Education Support', '../uploads/donation-categories/category_1768038246_69621f66440ed.webp', 'Help provide quality education to underprivileged children. Your donation will help buy books, stationery, and educational materials.', 'active', 1, '2026-01-10 08:45:56', '2026-01-10 09:44:06'),
(2, 'Medical Emergency', '', 'Support medical treatments for those who cannot afford healthcare. Every contribution saves lives and brings hope.', 'active', 2, '2026-01-10 08:45:56', '2026-01-10 08:50:01'),
(3, 'Food Distribution', '', 'Fight hunger by providing nutritious meals to families in need. Join us in making sure no one goes to bed hungry.', 'active', 3, '2026-01-10 08:45:56', '2026-01-10 08:50:05'),
(4, 'Clean Water Projects', '', 'Bring clean drinking water to communities lacking access. Help us build wells and water purification systems.', 'active', 4, '2026-01-10 08:45:56', '2026-01-10 08:50:08'),
(5, 'Orphan Care', '', 'Provide shelter, education, and care for orphaned children. Give them a chance at a brighter future.', 'active', 5, '2026-01-10 08:45:56', '2026-01-10 08:50:11'),
(6, 'Disaster Relief', '', 'Support communities affected by natural disasters. Help provide emergency supplies, shelter, and rebuilding efforts.', 'active', 6, '2026-01-10 08:45:56', '2026-01-10 08:50:15');

-- --------------------------------------------------------

--
-- Table structure for table `notices`
--

CREATE TABLE `notices` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `publish_date` date NOT NULL,
  `duration` int(11) NOT NULL COMMENT 'Duration in months',
  `expiry_date` date GENERATED ALWAYS AS (`publish_date` + interval `duration` month) STORED,
  `type` enum('General','Urgent','Info','Announcement') NOT NULL,
  `age_limit` int(11) DEFAULT NULL,
  `category` enum('Education','Scholarship','Health','Events','General') NOT NULL,
  `status` enum('Active','Expired','Draft') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notices`
--

INSERT INTO `notices` (`id`, `title`, `description`, `publish_date`, `duration`, `type`, `age_limit`, `category`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Scholarship Application Open', 'Applications for merit-based scholarships are now open for the academic year 2026.', '2026-01-08', 6, 'Announcement', 18, 'Scholarship', 'Active', '2026-01-08 09:50:30', '2026-01-08 09:50:30'),
(2, 'Health Awareness Program', 'Join our health awareness program this month to learn about preventive healthcare.', '2026-01-10', 3, 'Info', NULL, 'Health', 'Active', '2026-01-08 09:50:30', '2026-01-08 09:50:30'),
(3, 'Annual Sports Event', 'Registration for annual sports event is now open. All students can participate.', '2026-01-15', 2, 'General', 16, 'Events', 'Active', '2026-01-08 09:50:30', '2026-01-08 09:50:30'),
(7, 'Hello this is notice 2', 'This is the description of notice 2', '2026-01-08', 1, 'Urgent', 22, 'Scholarship', 'Active', '2026-01-08 10:21:24', '2026-01-08 10:21:24'),
(8, 'this is test', 'this is test', '2026-01-08', 5, 'General', 55, 'Events', 'Active', '2026-01-08 11:49:35', '2026-01-08 11:49:35');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `user_id`, `username`, `full_name`, `phone`, `profile_image`, `updated_at`) VALUES
(1, 1, NULL, 'admin', NULL, NULL, '2026-01-10 09:59:34');

-- --------------------------------------------------------

--
-- Table structure for table `sliders`
--

CREATE TABLE `sliders` (
  `id` int(11) NOT NULL,
  `slider_img` varchar(255) NOT NULL,
  `slider_title` varchar(100) NOT NULL,
  `slider_description` varchar(250) NOT NULL,
  `link_text` varchar(100) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 1,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `created_at`) VALUES
(1, 'admin', '$2y$10$Kixv8Jx0M1Q3uN9', '2026-01-08 09:39:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `donation_categories`
--
ALTER TABLE `donation_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notices`
--
ALTER TABLE `notices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_publish_date` (`publish_date`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_type` (`type`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD CONSTRAINT `settings_ibfk_1`
  FOREIGN KEY (`user_id`)
  REFERENCES `users` (`id`)
  ON DELETE CASCADE;


--
-- Indexes for table `sliders`
--
ALTER TABLE `sliders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_order` (`display_order`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `donation_categories`
--
ALTER TABLE `donation_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `notices`
--
ALTER TABLE `notices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sliders`
--
ALTER TABLE `sliders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `settings`
--
ALTER TABLE `settings`
  ADD CONSTRAINT `settings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
  ADD CONSTRAINT `settings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
