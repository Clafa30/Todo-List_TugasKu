-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 24, 2025 at 07:09 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tugas_mahasiswa`
--

-- --------------------------------------------------------

--
-- Table structure for table `shared_tasks`
--

CREATE TABLE `shared_tasks` (
  `id` int NOT NULL,
  `tugas_id` int NOT NULL,
  `shared_to_user_id` int NOT NULL,
  `shared_by_user_id` int NOT NULL,
  `shared_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shared_tasks`
--

INSERT INTO `shared_tasks` (`id`, `tugas_id`, `shared_to_user_id`, `shared_by_user_id`, `shared_at`) VALUES
(1, 1, 1, 2, '2025-05-23 02:35:50');

-- --------------------------------------------------------

--
-- Table structure for table `tugas_kuliah`
--

CREATE TABLE `tugas_kuliah` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `mata_kuliah` varchar(100) NOT NULL,
  `deskripsi` text,
  `tenggat_waktu` datetime NOT NULL,
  `prioritas` enum('rendah','sedang','tinggi') DEFAULT 'sedang',
  `status` enum('belum_selesai','selesai') DEFAULT 'belum_selesai',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tugas_kuliah`
--

INSERT INTO `tugas_kuliah` (`id`, `user_id`, `mata_kuliah`, `deskripsi`, `tenggat_waktu`, `prioritas`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'STAPRO', '1', '2222-11-13 13:12:00', 'tinggi', 'belum_selesai', '2025-05-22 14:45:17', '2025-05-22 14:45:17'),
(8, 1, 'PBW', 'Buat Video Demo Project kemarin, bahas tentang styling, crud, dan autentikasi nya bagaimana, direcord.', '2025-06-06 12:59:00', 'tinggi', 'belum_selesai', '2025-05-23 15:01:30', '2025-05-23 15:01:30');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `updated_at`) VALUES
(1, 'Jamalludin13', 'udin@gmail.com', '$2y$10$ghBHwKXzT.BHWbLIUFrqCO4DaMs4XhdXAlxcpx8IGTD6u8A1J0A9W', '2025-05-22 14:42:01', '2025-05-24 00:45:16'),
(2, 'uus', 'uus@gmail.com', '$2y$10$Mx.iiAFfTizKtYsJGAQ6w..DQKbGObiizN60gd0S2aDg9HUWLUTSi', '2025-05-23 02:33:24', '2025-05-23 02:33:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `shared_tasks`
--
ALTER TABLE `shared_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tugas_id` (`tugas_id`),
  ADD KEY `shared_to_user_id` (`shared_to_user_id`),
  ADD KEY `shared_by_user_id` (`shared_by_user_id`);

--
-- Indexes for table `tugas_kuliah`
--
ALTER TABLE `tugas_kuliah`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_tugas` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `shared_tasks`
--
ALTER TABLE `shared_tasks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tugas_kuliah`
--
ALTER TABLE `tugas_kuliah`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `shared_tasks`
--
ALTER TABLE `shared_tasks`
  ADD CONSTRAINT `shared_tasks_ibfk_1` FOREIGN KEY (`tugas_id`) REFERENCES `tugas_kuliah` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shared_tasks_ibfk_2` FOREIGN KEY (`shared_to_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shared_tasks_ibfk_3` FOREIGN KEY (`shared_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tugas_kuliah`
--
ALTER TABLE `tugas_kuliah`
  ADD CONSTRAINT `fk_user_tugas` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
