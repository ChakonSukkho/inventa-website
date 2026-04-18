-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 18, 2026 at 03:15 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventa_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(1, 'Sports'),
(2, 'Music'),
(3, 'Technology'),
(4, 'Arts & Design'),
(5, 'Leadership'),
(6, 'Innovation'),
(7, 'Volunteer'),
(8, 'Academic Competition');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `department_id` int NOT NULL,
  `department_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `department_name`) VALUES
(2, 'JKA'),
(3, 'JKE'),
(4, 'JKM'),
(1, 'JTMK');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `matric_no` varchar(50) DEFAULT NULL,
  `program` varchar(100) DEFAULT NULL,
  `year_level` int DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `student_name`, `matric_no`, `program`, `year_level`, `email`, `created_at`, `user_id`, `profile_pic`) VALUES
(1, 'Chakon', '13ddt23f1007', 'JTMK', 3, 'pipo@gmail.com', '2026-04-11 05:04:02', 2, '1775886800_69d9e1d0d9209.png'),
(2, 'AMMAR AIMAN BIN JALALUDDIN', '13DIT23F2019', 'JTMK', 5, 'ammar123@gmail.com', '2026-04-13 13:20:06', 7, NULL),
(3, 'Haikal', '13DKA23F2005', 'JKA', 5, 'haikal123@gmaill.com', '2026-04-14 04:17:50', 8, '1776140345_69ddc0391aa20.jpeg'),
(4, 'SYAZILAH', '13DIT25F1039', NULL, NULL, 'syazilah23@gmail.com', '2026-04-14 16:11:31', 9, NULL),
(5, 'NURAMALINA BINTI SUKRI', '13DIT25F1075', NULL, NULL, 'amalinastema12@gmail.com', '2026-04-14 16:11:32', 10, NULL),
(6, 'AZWA SAFRINA BINTI AHMAD EFENDI', '13DIT25F1108', NULL, NULL, 'azwasafri891@gmail.com', '2026-04-14 16:11:32', 11, NULL),
(7, 'WAN NUR ATHIRAH BINTI WAN ZUHARI', '13DIT25F1023', NULL, NULL, 'athirahzuhari00@gmail.com', '2026-04-14 16:11:32', 12, NULL),
(8, 'TUAN RASYIQAH BINTI TUAN RAHIMI', '13DTP25F1014', NULL, NULL, 'tuanrahimi1@gmail.com', '2026-04-14 16:11:32', 13, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `talents`
--

CREATE TABLE `talents` (
  `talent_id` int NOT NULL,
  `student_id` int DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `achievement` text,
  `level` enum('University','State','National','International') DEFAULT 'University',
  `certificate` varchar(255) DEFAULT NULL,
  `certificate2` varchar(255) DEFAULT NULL,
  `certificate3` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `talents`
--

INSERT INTO `talents` (`talent_id`, `student_id`, `category_id`, `achievement`, `level`, `certificate`, `certificate2`, `certificate3`) VALUES
(1, 1, 4, 'Lukis gambar kereta', 'National', NULL, NULL, NULL),
(2, 1, 6, 'sadsf', 'State', NULL, NULL, NULL),
(4, 2, 1, 'Bola Tampar', 'University', '1776086970_bola tampar.jpg', NULL, NULL),
(5, 3, 1, 'Takraw', 'University', '1776140505_69ddc0d91e1bf.jpg', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff','student') NOT NULL DEFAULT 'student',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `department` varchar(100) DEFAULT NULL,
  `must_change_password` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `created_at`, `department`, `must_change_password`) VALUES
(1, 'joo', '$2y$12$snCh4.7R1OJdbO3zS2KsQuIER8WGFeXa2X4Ia8j7aFqWGvvZcvp7.', 'student', '2026-04-11 04:46:38', NULL, 1),
(2, 'chakon', '$2y$12$ItmSIHqatePM3cRYShE6Y..LB6JEI7RwRK1zJYydqvKY8OnbgE9Jy', 'admin', '2026-04-11 05:04:02', NULL, 1),
(3, 'Ammar', '$2y$10$nZjbskUTqYoWhxJi3/YKR.tgIfkpe/Slz5Yvusdvx/.8D77F/2LHS', 'staff', '2026-04-11 07:13:50', 'JKA', 1),
(4, 'Syafiq', '$2y$12$9MSDUUIathWy5f887VWmEO9VZAaRXsLxLyuvvZxvVw/GFJni0Xlom', 'staff', '2026-04-11 14:48:50', 'JKE', 1),
(5, 'Alia', '$2y$12$KkLu64rC3JnaQhPjojZYaea75uA/mficJXSYJLDf4HNkvx4QDZlwq', 'staff', '2026-04-11 14:49:28', 'JKM', 1),
(6, 'Hakim', '$2y$12$AH0D2slhHQ06Wre2NEW0Ue/RAFiDSFp/qgV..7oPOEyZguurseRzy', 'staff', '2026-04-11 15:05:54', 'JTMK', 1),
(7, 'Aiman', '$2y$10$ryJfleplC3RKW7DyWr7e6u0/vFLwEGgx591H7rrN9JzuWilNtrJ/S', 'student', '2026-04-13 13:20:06', NULL, 1),
(8, 'Haikal', '$2y$10$.zSvS.HXxflARCIv460uD.zOI7839Hxb81Gk7SbDFlstMPITk7l8.', 'student', '2026-04-14 04:17:50', NULL, 1),
(9, '70323030380', '$2y$10$5wyfFhHgu1oztqyJVzqbMOqxBVA/ijt3TUAF63p4tTFP5FKapaPoa', 'student', '2026-04-14 16:11:31', NULL, 1),
(10, '71114030880', '$2y$10$uVyqUscjNARS5zFYNdmGZeDq2y7Ho0EmF31SHocSP6fS6UXvZpfpy', 'student', '2026-04-14 16:11:32', NULL, 1),
(11, '71104030238', '$2y$10$CNQMtJOEQHp8by9Q3MDkUu3LF.gAC8wBSBBmDD90kX/44agr0xuZm', 'student', '2026-04-14 16:11:32', NULL, 0),
(12, '70202110544', '$2y$10$w1BfKnVPDOQzbJIR4fG9ZOTLvgkdkQf44z8adyJ/Js83K92RSbdsq', 'student', '2026-04-14 16:11:32', NULL, 1),
(13, '71217110322', '$2y$10$.fnYdhzCk1mmioKfJWfuTulLTyVTGACNn.j/qnyK.aKvEDWMYPkx.', 'student', '2026-04-14 16:11:32', NULL, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`department_id`),
  ADD UNIQUE KEY `department_name` (`department_name`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `talents`
--
ALTER TABLE `talents`
  ADD PRIMARY KEY (`talent_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `department_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `talents`
--
ALTER TABLE `talents`
  MODIFY `talent_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `talents`
--
ALTER TABLE `talents`
  ADD CONSTRAINT `talents_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `talents_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
