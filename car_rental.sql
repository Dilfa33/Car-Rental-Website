-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 03, 2025 at 03:34 AM
-- Server version: 8.2.0
-- PHP Version: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `car_rental`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE IF NOT EXISTS `bookings` (
  `booking_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `car_id` int NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`booking_id`),
  KEY `user_id` (`user_id`),
  KEY `car_id` (`car_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `car_id`, `start_date`, `end_date`, `total_price`, `status`, `created_at`) VALUES
(1, 1, 1, '2025-11-10', '2025-11-15', 250.00, 'confirmed', '2025-11-03 00:00:00'),
(2, 2, 4, '2025-11-05', '2025-11-08', 285.00, 'completed', '2025-11-03 00:00:00'),
(3, 1, 3, '2025-12-01', '2025-12-05', 340.00, 'pending', '2025-11-03 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

DROP TABLE IF EXISTS `cars`;
CREATE TABLE IF NOT EXISTS `cars` (
  `car_id` int NOT NULL AUTO_INCREMENT,
  `brand` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `year` int NOT NULL,
  `transmission` enum('manual','automatic') NOT NULL,
  `fuel_type` enum('diesel','petrol','electric','hybrid') NOT NULL,
  `daily_rate` decimal(10,2) NOT NULL,
  `availability_status` enum('available','rented','maintenance') DEFAULT 'available',
  `mileage` int DEFAULT '0',
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`car_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`car_id`, `brand`, `model`, `year`, `transmission`, `fuel_type`, `daily_rate`, `availability_status`, `mileage`, `image_url`, `created_at`) VALUES
(1, 'Toyota', 'Camry', 2023, 'automatic', 'petrol', 50.00, 'available', 15000, 'camry.jpg', '2025-11-03 00:00:00'),
(2, 'Honda', 'Civic', 2023, 'automatic', 'petrol', 45.00, 'available', 12000, 'civic.jpg', '2025-11-03 00:00:00'),
(3, 'Tesla', 'Model 3', 2024, 'automatic', 'electric', 85.00, 'available', 5000, 'tesla.jpg', '2025-11-03 00:00:00'),
(4, 'BMW', '3 Series', 2023, 'automatic', 'petrol', 95.00, 'rented', 8000, 'bmw.jpg', '2025-11-03 00:00:00'),
(5, 'Mercedes', 'C-Class', 2024, 'automatic', 'hybrid', 100.00, 'available', 3000, 'mercedes.jpg', '2025-11-03 00:00:00'),
(6, 'Porsche', 'Carrera', 2022, 'automatic', 'petrol', 120.00, 'available', 12000, 'carrera.jpg', '2025-11-03 03:35:22'),
(7, 'Volkswagen', 'Golf 7', 2017, 'manual', 'diesel', 45.00, 'available', 85000, 'golf7.jpg', '2025-11-03 03:35:22'),
(8, 'Renault', 'Megane', 2019, 'manual', 'petrol', 40.00, 'available', 65000, 'megane.jpg', '2025-11-03 03:35:22'),
(9, 'Mercedes-Benz', 'C-Class', 2021, 'automatic', 'diesel', 90.00, 'available', 30000, 'mercedes.jpg', '2025-11-03 03:35:22'),
(10, 'Volkswagen', 'Passat CC', 2016, 'automatic', 'diesel', 55.00, 'available', 95000, 'passatcc.jpg', '2025-11-03 03:35:22'),
(11, 'Volkswagen', 'Polo', 2018, 'manual', 'petrol', 35.00, 'available', 72000, 'polo.jpg', '2025-11-03 03:35:22'),
(12, 'Opel', 'Vivaro', 2020, 'manual', 'diesel', 60.00, 'available', 50000, 'vivaro.jpg', '2025-11-03 03:35:22');

-- --------------------------------------------------------

--
-- Table structure for table `car_reviews`
--

DROP TABLE IF EXISTS `car_reviews`;
CREATE TABLE IF NOT EXISTS `car_reviews` (
  `review_id` int NOT NULL AUTO_INCREMENT,
  `car_id` int NOT NULL,
  `user_id` int NOT NULL,
  `rating` tinyint DEFAULT NULL,
  `comment` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`review_id`),
  KEY `car_id` (`car_id`),
  KEY `user_id` (`user_id`)
) ;

--
-- Dumping data for table `car_reviews`
--

INSERT INTO `car_reviews` (`review_id`, `car_id`, `user_id`, `rating`, `comment`, `created_at`) VALUES
(1, 1, 1, 5, 'Excellent car! Very reliable and comfortable.', '2025-11-03 00:00:00'),
(2, 2, 2, 4, 'Good car, smooth drive. Minor issue with AC.', '2025-11-03 00:00:00'),
(3, 3, 1, 5, 'Amazing electric car! Loved the autopilot feature.', '2025-11-03 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_records`
--

DROP TABLE IF EXISTS `maintenance_records`;
CREATE TABLE IF NOT EXISTS `maintenance_records` (
  `record_id` int NOT NULL AUTO_INCREMENT,
  `car_id` int NOT NULL,
  `service_date` date NOT NULL,
  `description` text,
  `cost` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`record_id`),
  KEY `car_id` (`car_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `maintenance_records`
--

INSERT INTO `maintenance_records` (`record_id`, `car_id`, `service_date`, `description`, `cost`) VALUES
(1, 1, '2025-10-15', 'Oil change and tire rotation', 75.00),
(2, 2, '2025-10-20', 'Brake pad replacement', 300.00),
(3, 4, '2025-10-25', 'Regular service and inspection', 150.00);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
CREATE TABLE IF NOT EXISTS `transactions` (
  `transaction_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `type` enum('top_up','booking_payment','refund','admin_adjustment') NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`transaction_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `user_id`, `amount`, `type`, `description`, `created_at`) VALUES
(1, 1, 500.00, 'top_up', 'Initial balance deposit', '2025-11-03 00:00:00'),
(2, 2, 750.00, 'top_up', 'Account funding', '2025-11-03 00:00:00'),
(3, 1, -250.00, 'booking_payment', 'Payment for Toyota Camry booking', '2025-11-03 00:00:00'),
(4, 2, -285.00, 'booking_payment', 'Payment for BMW 3 Series booking', '2025-11-03 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `balance` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `password_hash`, `phone`, `role`, `created_at`, `updated_at`, `balance`) VALUES
(1, 'John', 'Doe', 'john.doe@email.com', '$2y$10$abcdefghijklmnopqrstuv', '555-0101', 'customer', '2025-11-03 00:00:00', '2025-11-03 04:25:19', 50),
(2, 'Jane', 'Smith', 'jane.smith@email.com', '$2y$10$abcdefghijklmnopqrstuv', '555-0102', 'customer', '2025-11-03 00:00:00', '2025-11-03 00:00:00', 750),
(3, 'Admin', 'User', 'admin@carrental.com', '$2y$10$abcdefghijklmnopqrstuv', '555-0100', 'admin', '2025-11-03 00:00:00', '2025-11-03 04:26:59', 9999999),
(4, 'Mirza', 'Cakal', 'mirza.cakal@stu.ibu.edu.ba', '$2y$10$/IEo4sItbplwr2aVjAk3MOGNZ0KvLaE4IadL7Gg0SMknvnKbDgPDm', '03312345', 'admin', '2025-11-03 04:33:00', '2025-11-03 04:33:20', 67);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
