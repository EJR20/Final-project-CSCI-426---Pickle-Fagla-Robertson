-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 11, 2025 at 07:10 AM
-- Server version: 8.4.7
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fitquest_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `progress_logs`
--

DROP TABLE IF EXISTS `progress_logs`;
CREATE TABLE IF NOT EXISTS `progress_logs` (
  `log_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `log_date` date NOT NULL,
  `mood` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `focus` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `weight_lb` decimal(5,2) NOT NULL,
  `strength_volume` int NOT NULL,
  `cardio_minutes` int NOT NULL,
  `rpe` tinyint NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `progress_logs`
--

INSERT INTO `progress_logs` (`log_id`, `user_id`, `log_date`, `mood`, `focus`, `weight_lb`, `strength_volume`, `cardio_minutes`, `rpe`, `notes`, `created_at`) VALUES
(5, 1, '2025-12-12', 'Tired', 'Cardio', 175.00, 0, 100, 10, 'Crazy hard cardio day. I need to lose weight.', '2025-12-10 22:12:11'),
(4, 1, '2025-12-11', 'Good', 'Strength', 150.00, 100, 0, 4, 'Good day 1', '2025-12-10 22:10:57');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `age` int DEFAULT NULL,
  `height_in` int DEFAULT NULL,
  `weight_lb` decimal(5,2) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `password_hash`, `age`, `height_in`, `weight_lb`, `created_at`) VALUES
(1, 'Evan', 'erobertson2@mcneese.edu', '$2y$10$VThPc/qUjCB6m0B1FlY6yuHknqEmjUQ/1NeMIKAyuI2qv9DLNqbCe', NULL, NULL, NULL, '2025-12-10 22:01:55');

-- --------------------------------------------------------

--
-- Table structure for table `workout_plans`
--

DROP TABLE IF EXISTS `workout_plans`;
CREATE TABLE IF NOT EXISTS `workout_plans` (
  `workout_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `difficulty` enum('Beginner','Intermediate','Advanced') COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_muscle` enum('Full Body','Upper Body','Lower Body','Core') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`workout_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `workout_plans`
--

INSERT INTO `workout_plans` (`workout_id`, `user_id`, `name`, `difficulty`, `target_muscle`, `created_at`) VALUES
(10, 1, 'Day 1', 'Beginner', 'Upper Body', '2025-12-10 22:10:19'),
(11, 1, 'Push day a', 'Advanced', 'Full Body', '2025-12-11 00:13:52');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
