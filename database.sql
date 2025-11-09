-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 02, 2025 at 06:30 AM
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
-- Database: `web_keamanan_data`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `user_id`, `amount`, `description`) VALUES
(11, 1, 150.00, 'Invoice milik Mayatul (ID=1)'),
(12, 2, 500.00, 'Invoice milik Rizal (ID=2) (RAHASIA)');

-- --------------------------------------------------------

--
-- Table structure for table `sqli_users_safe`
--

CREATE TABLE `sqli_users_safe` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sqli_users_safe`
--

INSERT INTO `sqli_users_safe` (`id`, `username`, `password`, `fullname`, `created_at`) VALUES
(1, 'Mayda', '$2y$10$KIuTwmtt4zLOrBdEXJbnUuBu2hNQL/393pG30vMVTObx8yEwmWaBe', 'Maydatul', '2025-11-01 20:16:10'),
(2, 'Rizal', '$2y$10$558dxotO5ePPOKrczPH.Eu0nXKRYIeJ2/OAaWjWAgR4rDCRTTdOVe', 'Shahrizal', '2025-11-01 20:32:34');

-- --------------------------------------------------------

--
-- Table structure for table `sqli_users_vul`
--

CREATE TABLE `sqli_users_vul` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sqli_users_vul`
--

INSERT INTO `sqli_users_vul` (`id`, `username`, `password`, `fullname`, `created_at`) VALUES
(1, 'Mira', '123456789', 'Viva miranda', '2025-11-01 20:20:12'),
(2, 'Shahrizal', '123456789', 'rizal', '2025-11-01 20:32:55');

-- --------------------------------------------------------

--
-- Table structure for table `upload_articles`
--

CREATE TABLE `upload_articles` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `author_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `upload_articles`
--

INSERT INTO `upload_articles` (`id`, `title`, `body`, `file_path`, `author_id`, `created_at`) VALUES
(1, 'Unimus Melepas Sebanyak 575 Lulusan Pada Wisuda Ke – 45', 'Universitas Muhammadiyah (Unimus) berhasil meluluskan sebanyak 575 mahasiswa yang dilepas dalam acara wisdua ke-45 periode februari 2025 pada Program Pascasarjana, Profesi, Sarjana dan Diploma. Kegiatan ini digelar di Gedung Serba Guna kampus Unimus jalan Kedungmundu Raya no. 18 Semarang.\r\n\r\nHadir dalam kegiatan Wisuda ini Wakil Rektror 1 (Prof. Dr. budi Santosa, M.Si.med), Wakil Rektor II (Dr. Hardiwinoto, M.Si) Wakil Rektor III (Dr. Eny Winaryati, M.Pd), Wakil Rektor IV (Muhammad Yusuf, Ph.D) anggota senat universitas dan jajaran pimpinan di lingkungan Unimus. Tidak lupa Unimus juga mengundang Kepala LLDIKTI Wilayan VI (Dr. Bhimo Widyo Andoko, S.H., M.H.) Ketua Pimpinan Wilayah Muhammadiyah dan Aisyiyah Jawa Tengah, Mitra Jejaring serta para orang tua dan Wali wisudawan.\r\n\r\nDalam kesempatan ini, Wakil Rektor I Unimus, Dr. Budi Santosa, M.Si. Med, menyampaikan dalam sambutannya harapan besar kepada para wisudawan agar dapat memberikan kontribusi positif kepada masyarakat. “Kami berharap para lulusan Unimus dapat menjadi alumni yang bermanfaat, mampu beradaptasi dengan cepat, dan memberikan solusi atas permasalahan yang ada di tengah masyarakat,” ujar Dr. Budi Santosa dalam pidatonya.\r\n\r\nPada acara yang dihadiri oleh keluarga wisudawan dan sejumlah tamu undangan ini, para wisudawan yang telah menyelesaikan pendidikan di jenjang Diploma, Sarjana, Profesi dan Magister, turut merayakan keberhasilan mereka dalam menempuh perjalanan akademik. Wisuda kali ini menjadi momen yang penuh makna, di mana para lulusan siap untuk melangkah ke dunia profesional dan memberikan dampak positif bagi bangsa dan negara.\r\n\r\nDidalam perjalanannya Unimus selalu terus berkomitmen untuk mencetak lulusan yang unggul, kompeten, dan memiliki nilai-nilai keislaman yang kuat dalam menghadapi tantangan zaman. Dengan 8 fakultas dan program pascasarjana yang terus berkembang, Unimus terus berupaya memberikan pendidikan berkualitas bagi generasi muda.\r\n\r\nTak lupa Ketua LLDIKTI Wilayah VI Dr. Bhimo Widyo Andoko yang turut hadir dalam kegiatan wisuda ke 45 Unimus menyampaikan pesan pada sambutannya kepada para lulusan agar mempunyai langkah dan konsep bagaimana membuat karya yang bermanfaat bagi Masyarakat. “Kalian para lulusan yang saat ini mengikuti prosesi wisuda merupakan bagian dari masyarakat yang tentunya setelah lulus juga akan terjun langsung ke masyakarat, jelas harus mempunyai sebuah langkah strategis, trobosan juga konsep yang harus dikeluarkan untuk memberikan karya yang dapat bermanfaat di Masyarakat” ucap Dr. Bhimo.\r\n\r\nDengan diselenggarakannya Wisuda ke-45 ini, Universitas Muhammadiyah Semarang berharap dapat semakin memperkuat kontribusinya dalam menciptakan generasi penerus yang siap menghadapi tantangan global dengan penuh keyakinan dan semangat.\r\n\r\n\r\n', 'uploads/6906ec2779af2-WhatsApp-Image-2025-02-27-at-16.24.53-8-1536x1023.jpeg', 1, '2025-11-02 05:29:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `article_id` (`article_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `sqli_users_safe`
--
ALTER TABLE `sqli_users_safe`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `sqli_users_vul`
--
ALTER TABLE `sqli_users_vul`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `upload_articles`
--
ALTER TABLE `upload_articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id` (`author_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `sqli_users_safe`
--
ALTER TABLE `sqli_users_safe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sqli_users_vul`
--
ALTER TABLE `sqli_users_vul`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `upload_articles`
--
ALTER TABLE `upload_articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `upload_articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `sqli_users_safe` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `sqli_users_safe` (`id`);

--
-- Constraints for table `upload_articles`
--
ALTER TABLE `upload_articles`
  ADD CONSTRAINT `upload_articles_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `sqli_users_safe` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
