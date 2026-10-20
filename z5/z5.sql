-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 20, 2026 at 06:49 PM
-- Server version: 11.8.6-MariaDB-0+deb13u1 from Debian
-- PHP Version: 8.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `z5`
--

-- --------------------------------------------------------

--
-- Table structure for table `break_ins`
--

CREATE TABLE `break_ins` (
  `id` int(11) NOT NULL,
  `ipaddress` varchar(45) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `domeny`
--

CREATE TABLE `domeny` (
  `id` int(11) NOT NULL,
  `host` varchar(255) NOT NULL,
  `port` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `errors` int(11) DEFAULT 0,
  `failure` datetime DEFAULT NULL,
  `total_errors` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `goscieportalu`
--

CREATE TABLE `goscieportalu` (
  `id` int(11) NOT NULL,
  `ipaddress` varchar(45) NOT NULL,
  `datetime` datetime DEFAULT current_timestamp(),
  `browser` varchar(255) DEFAULT NULL,
  `screen_res` varchar(50) DEFAULT NULL,
  `window_res` varchar(50) DEFAULT NULL,
  `screen_colors` varchar(20) DEFAULT NULL,
  `cookies_enabled` tinyint(1) DEFAULT 0,
  `java_enabled` tinyint(1) DEFAULT 0,
  `language` varchar(10) DEFAULT NULL,
  `event_type` varchar(20) NOT NULL DEFAULT 'visit',
  `username` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `goscieportalu`
--

INSERT INTO `goscieportalu` (`id`, `ipaddress`, `datetime`, `browser`, `screen_res`, `window_res`, `screen_colors`, `cookies_enabled`, `java_enabled`, `language`, `event_type`, `username`) VALUES
(1, '37.209.144.202', '2026-10-20 20:43:17', 'Firefox', '1920x1080', '1586x948', '24', 1, 0, 'pl', 'visit', 'user1'),
(2, '37.209.144.202', '2026-10-20 20:43:22', 'Firefox', '1920x1080', '1586x948', '24', 1, 0, 'pl', 'visit', 'user1'),
(3, '37.209.144.202', '2026-10-20 20:43:28', 'Firefox', '', '', '', 0, 0, '', 'login_fail', 'www'),
(4, '37.209.144.202', '2026-10-20 20:43:33', 'Firefox', '', '', '', 0, 0, '', 'login_success', 'user1'),
(5, '37.209.144.202', '2026-10-20 20:43:34', 'Firefox', '1920x1080', '1586x948', '24', 1, 0, 'pl', 'visit', 'user1'),
(6, '37.209.144.202', '2026-10-20 20:43:40', 'Firefox', '1920x1080', '1586x948', '24', 1, 0, 'pl', 'visit', 'user1'),
(7, '37.209.144.202', '2026-10-20 20:43:45', 'Firefox', '1920x1080', '1586x948', '24', 1, 0, 'pl', 'visit', 'user1'),
(8, '37.209.144.202', '2026-10-20 20:43:49', 'Firefox', '1920x1080', '1586x948', '24', 1, 0, 'pl', 'visit', 'user1'),
(9, '37.209.144.202', '2026-10-20 20:43:52', 'Firefox', '1920x1080', '1586x948', '24', 1, 0, 'pl', 'visit', 'user1');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT 'media/avatars/default.png',
  `worker` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `avatar`, `worker`) VALUES
(1, 'user1', 'pass1', 'media/avatars/default.png', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `break_ins`
--
ALTER TABLE `break_ins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `domeny`
--
ALTER TABLE `domeny`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `goscieportalu`
--
ALTER TABLE `goscieportalu`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `break_ins`
--
ALTER TABLE `break_ins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `domeny`
--
ALTER TABLE `domeny`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `goscieportalu`
--
ALTER TABLE `goscieportalu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `domeny`
--
ALTER TABLE `domeny`
  ADD CONSTRAINT `domeny_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
