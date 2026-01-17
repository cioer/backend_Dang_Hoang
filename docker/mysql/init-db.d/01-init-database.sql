-- ==========================================
-- AUTO-GENERATED FOR DOCKER INIT
-- ==========================================

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS school_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE school_management;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 11, 2026 at 01:32 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `school_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` enum('present','absent','late') DEFAULT 'present',
  `note` varchar(255) DEFAULT NULL,
  `recorded_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `class_id`, `student_id`, `date`, `status`, `note`, `recorded_by`, `created_at`) VALUES
(1, 1, 3, '2026-01-08', 'present', NULL, 2, '2026-01-08 10:05:10'),
(3, 3, 12, '2026-01-08', 'present', NULL, 10, '2026-01-08 10:36:16'),
(4, 3, 12, '2026-01-07', 'late', 'Đến muộn 5p', 10, '2026-01-08 10:36:16'),
(5, 3, 12, '2026-01-06', 'absent', 'Vắng có phép', 10, '2026-01-08 10:36:16'),
(6, 3, 12, '2026-01-05', 'present', NULL, 10, '2026-01-08 10:36:16'),
(7, 3, 12, '2026-01-04', 'present', NULL, 10, '2026-01-08 10:36:16'),
(14, 3, 131, '2026-01-10', 'present', '', 1, '2026-01-09 17:47:10'),
(15, 3, 140, '2026-01-10', 'present', '', 1, '2026-01-09 17:47:10'),
(16, 3, 141, '2026-01-10', 'present', '', 1, '2026-01-09 17:47:10'),
(17, 3, 142, '2026-01-10', 'present', '', 1, '2026-01-09 17:47:10'),
(18, 3, 143, '2026-01-10', 'present', '', 1, '2026-01-09 17:47:10'),
(19, 3, 144, '2026-01-10', 'present', '', 1, '2026-01-09 17:47:10'),
(20, 3, 145, '2026-01-10', 'present', '', 1, '2026-01-09 17:47:10'),
(21, 3, 146, '2026-01-10', 'present', '', 1, '2026-01-09 17:47:10'),
(22, 3, 147, '2026-01-10', 'present', '', 1, '2026-01-09 17:47:10'),
(23, 3, 148, '2026-01-10', 'present', '', 1, '2026-01-09 17:47:10'),
(24, 3, 149, '2026-01-10', 'present', '', 1, '2026-01-09 17:47:10'),
(25, 3, 132, '2026-01-10', 'present', '', 1, '2026-01-09 17:47:10'),
(26, 3, 150, '2026-01-10', 'present', '', 1, '2026-01-09 17:47:10'),
(27, 3, 133, '2026-01-10', 'present', '', 1, '2026-01-09 17:47:10'),
(28, 3, 134, '2026-01-10', 'present', '', 1, '2026-01-09 17:47:10'),
(29, 3, 135, '2026-01-10', 'present', '', 1, '2026-01-09 17:47:10'),
(30, 3, 136, '2026-01-10', 'present', '', 1, '2026-01-09 17:47:10'),
(31, 3, 137, '2026-01-10', 'present', '', 1, '2026-01-09 17:47:10'),
(32, 3, 138, '2026-01-10', 'present', '', 1, '2026-01-09 17:47:10'),
(33, 3, 139, '2026-01-10', 'present', '', 1, '2026-01-09 17:47:10'),
(34, 1, 64, '2026-01-11', 'present', '', 2, '2026-01-10 17:25:07'),
(35, 1, 120, '2026-01-11', 'present', '', 2, '2026-01-10 17:25:07'),
(36, 1, 121, '2026-01-11', 'present', '', 2, '2026-01-10 17:25:07'),
(37, 1, 122, '2026-01-11', 'present', '', 2, '2026-01-10 17:25:07'),
(38, 1, 123, '2026-01-11', 'present', '', 2, '2026-01-10 17:25:07'),
(39, 1, 124, '2026-01-11', 'present', '', 2, '2026-01-10 17:25:07'),
(40, 1, 125, '2026-01-11', 'present', '', 2, '2026-01-10 17:25:07'),
(41, 1, 126, '2026-01-11', 'present', '', 2, '2026-01-10 17:25:07'),
(42, 1, 127, '2026-01-11', 'present', '', 2, '2026-01-10 17:25:07'),
(43, 1, 128, '2026-01-11', 'present', '', 2, '2026-01-10 17:25:07'),
(44, 1, 129, '2026-01-11', 'present', '', 2, '2026-01-10 17:25:07'),
(45, 1, 130, '2026-01-11', 'present', '', 2, '2026-01-10 17:25:07'),
(46, 1, 3, '2026-01-11', 'present', '', 2, '2026-01-10 17:25:07');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `details` text DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `details`, `ip`, `created_at`) VALUES
(1, 2, 'CREATE_NEWS', 'Tin: Khai giảng', '127.0.0.1', '2026-01-08 10:05:11'),
(2, 2, 'CREATE_NEWS', 'Tin: Khai giảng', '127.0.0.1', '2026-01-08 10:36:16'),
(3, 2, 'CREATE_NEWS', 'Tin: Khai giảng', '127.0.0.1', '2026-01-08 10:38:54'),
(4, 10, 'PROFILE_UPDATE', '{\"before\":{\"id\":10,\"full_name\":\"Tr\\u1ea7n Quang Huy\",\"email\":\"gv00002@thpt-thd.edu.vn\",\"phone\":\"0900000002\",\"avatar\":null},\"after\":{\"full_name\":\"Tr\\u1ea7n Quang Huy\",\"email\":\"gv00002@thpt-thd.edu.vn\",\"phone\":\"0900000002\",\"avatar\":null}}', '127.0.0.1', '2026-01-08 12:28:39'),
(5, 2, 'CREATE_NEWS', 'Tin: Khai giảng', '127.0.0.1', '2026-01-08 12:49:58'),
(6, 2, 'CREATE_NEWS', 'Tin: Khai giảng', '127.0.0.1', '2026-01-08 12:50:36'),
(7, 2, 'CREATE_NEWS', 'Tin: Khai giảng', '127.0.0.1', '2026-01-08 12:51:35'),
(8, 2, 'CREATE_NEWS', 'Tin: Khai giảng', '127.0.0.1', '2026-01-08 12:58:35'),
(9, 2, 'CREATE_NEWS', 'Tin: Khai giảng', '127.0.0.1', '2026-01-08 13:00:01'),
(10, 64, 'PROFILE_UPDATE', '{\"before\":{\"id\":64,\"full_name\":\"C\\u00f4 Gi\\u00e1o Test\",\"email\":\"gvtest01@thpt-thd.edu.vn\",\"phone\":\"0900000111\",\"avatar\":null},\"after\":{\"full_name\":\"C\\u00f4 Gi\\u00e1o Test\",\"email\":\"gvtest01@thpt-thd.edu.vn\",\"phone\":\"0900000111\",\"avatar\":null}}', '127.0.0.1', '2026-01-09 18:22:14'),
(11, 64, 'PROFILE_UPDATE', '{\"before\":{\"id\":64,\"full_name\":\"C\\u00f4 Gi\\u00e1o Test\",\"email\":\"gvtest01@thpt-thd.edu.vn\",\"phone\":\"0900000111\",\"avatar\":\"avatar_64_1767982934.jpg\"},\"after\":{\"full_name\":\"C\\u00f4 Gi\\u00e1o Test\",\"email\":\"gvtest01@thpt-thd.edu.vn\",\"phone\":\"0900000111\",\"avatar\":\"avatar_64_1767982934.jpg\"}}', '127.0.0.1', '2026-01-09 18:22:24'),
(12, 2, 'PROFILE_UPDATE', '{\"before\":{\"id\":2,\"full_name\":\"Nguy\\u1ec5n V\\u0103n A\",\"email\":\"gv00001@thpt-thd.edu.vn\",\"phone\":\"0900000001\",\"avatar\":\"avatar_2_1768066915.jpg\"},\"after\":{\"full_name\":\"Nguy\\u1ec5n V\\u0103n A\",\"email\":\"gv00001@thpt-thd.edu.vn\",\"phone\":\"0900000001\",\"avatar\":\"avatar_2_1768066915.jpg\"}}', '127.0.0.1', '2026-01-10 17:41:55'),
(13, 10, 'PROFILE_UPDATE', '{\"before\":{\"id\":10,\"full_name\":\"Tr\\u1ea7n Quang Huy\",\"email\":\"gv00002@thpt-thd.edu.vn\",\"phone\":\"0900000002\",\"avatar\":null},\"after\":{\"full_name\":\"Tr\\u1ea7n Quang Huy\",\"email\":\"gv00002@thpt-thd.edu.vn\",\"phone\":\"0900000002\",\"avatar\":null}}', '127.0.0.1', '2026-01-10 18:18:56'),
(14, 10, 'PROFILE_UPDATE', '{\"before\":{\"id\":10,\"full_name\":\"Tr\\u1ea7n Quang Huy\",\"email\":\"gv00002@thpt-thd.edu.vn\",\"phone\":\"0900000002\",\"avatar\":null},\"after\":{\"full_name\":\"Tr\\u1ea7n Quang Huy\",\"email\":\"gv00002@thpt-thd.edu.vn\",\"phone\":\"0900000002\",\"avatar\":null}}', '127.0.0.1', '2026-01-10 18:20:10'),
(15, 120, 'PROFILE_UPDATE', '{\"before\":{\"id\":120,\"full_name\":\"H\\u1ecdc Sinh 10A1 S\\u1ed1 10\",\"email\":\"pham.cuong@thpt-thd.edu.vn\",\"phone\":null,\"avatar\":null},\"after\":{\"full_name\":\"H\\u1ecdc Sinh 10A1 S\\u1ed1 10\",\"email\":\"pham.cuong@thpt-thd.edu.vn\",\"phone\":\"\",\"avatar\":null}}', '127.0.0.1', '2026-01-10 18:32:24');

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `cta_text` varchar(100) DEFAULT NULL,
  `link_url` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `priority` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `banner_logs`
--

CREATE TABLE `banner_logs` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `banner_info` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `banner_logs`
--

INSERT INTO `banner_logs` (`id`, `admin_id`, `action`, `banner_info`, `created_at`) VALUES
(1, 1, 'CREATE', 'Banner 1', '2026-01-08 10:05:11');

-- --------------------------------------------------------

--
-- Table structure for table `behavior_reports`
--

CREATE TABLE `behavior_reports` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `type` enum('reward','discipline') NOT NULL,
  `description` text NOT NULL,
  `date` date NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `behavior_reports`
--

INSERT INTO `behavior_reports` (`id`, `student_id`, `type`, `description`, `date`, `created_by`, `created_at`) VALUES
(1, 3, 'discipline', 'Đi học muộn 1 lần', '2026-01-08', 2, '2026-01-08 10:05:11'),
(2, 3, 'discipline', 'Đi học muộn 1 lần', '2026-01-08', 2, '2026-01-08 10:36:16'),
(3, 3, 'discipline', 'Đi học muộn 1 lần', '2026-01-08', 2, '2026-01-08 10:38:54'),
(4, 3, 'discipline', 'Đi học muộn 1 lần', '2026-01-08', 2, '2026-01-08 12:49:58'),
(5, 3, 'discipline', 'Đi học muộn 1 lần', '2026-01-08', 2, '2026-01-08 12:50:36'),
(6, 3, 'discipline', 'Đi học muộn 1 lần', '2026-01-08', 2, '2026-01-08 12:51:35'),
(7, 3, 'discipline', 'Đi học muộn 1 lần', '2026-01-08', 2, '2026-01-08 12:58:35'),
(8, 3, 'discipline', 'Đi học muộn 1 lần', '2026-01-08', 2, '2026-01-08 13:00:01');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `name` varchar(20) NOT NULL,
  `code` varchar(30) DEFAULT NULL,
  `homeroom_teacher_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `name`, `code`, `homeroom_teacher_id`) VALUES
(1, '10A1', '10A1', 2),
(3, '10A2', '10A2', 10),
(4, '10A3', '10A3', 11),
(5, '11A1', '11A1', 2),
(6, '11A2', '11A2', 10),
(7, '12A1', '12A1', 11),
(40, 'KL-01', NULL, 64);

-- --------------------------------------------------------

--
-- Table structure for table `class_registrations`
--

CREATE TABLE `class_registrations` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_registrations`
--

INSERT INTO `class_registrations` (`id`, `student_id`, `class_id`, `registered_at`) VALUES
(1, 3, 1, '2026-01-08 10:05:11'),
(2, 3, 1, '2026-01-08 10:36:16'),
(3, 3, 1, '2026-01-08 10:38:54'),
(4, 3, 1, '2026-01-08 12:49:58'),
(5, 3, 1, '2026-01-08 12:50:36'),
(6, 3, 1, '2026-01-08 12:51:35'),
(7, 3, 1, '2026-01-08 12:58:35'),
(8, 3, 1, '2026-01-08 13:00:01'),
(9, 64, 1, '2026-01-08 13:31:51'),
(17, 120, 1, '2026-01-10 18:32:20'),
(18, 121, 1, '2026-01-08 13:51:19'),
(19, 122, 1, '2026-01-08 13:51:19'),
(20, 123, 1, '2026-01-08 13:51:19'),
(21, 124, 1, '2026-01-08 13:51:19'),
(22, 125, 1, '2026-01-08 13:51:19'),
(23, 126, 1, '2026-01-08 13:51:19'),
(24, 127, 1, '2026-01-08 13:51:19'),
(25, 128, 1, '2026-01-08 13:51:19'),
(26, 129, 1, '2026-01-08 13:51:19'),
(27, 130, 1, '2026-01-08 13:51:19'),
(28, 131, 3, '2026-01-08 13:51:19'),
(29, 132, 3, '2026-01-08 13:51:19'),
(30, 133, 3, '2026-01-08 13:51:19'),
(31, 134, 3, '2026-01-08 13:51:19'),
(32, 135, 3, '2026-01-08 13:51:19'),
(33, 136, 3, '2026-01-08 13:51:19'),
(34, 137, 3, '2026-01-08 13:51:19'),
(35, 138, 3, '2026-01-08 13:51:19'),
(36, 139, 3, '2026-01-08 13:51:19'),
(37, 140, 3, '2026-01-08 13:51:19'),
(38, 141, 3, '2026-01-08 13:51:19'),
(39, 142, 3, '2026-01-08 13:51:19'),
(40, 143, 3, '2026-01-08 13:51:19'),
(41, 144, 3, '2026-01-08 13:51:19'),
(42, 145, 3, '2026-01-08 13:51:19'),
(43, 146, 3, '2026-01-08 13:51:19'),
(44, 147, 3, '2026-01-08 13:51:19'),
(45, 148, 3, '2026-01-08 13:51:19'),
(46, 149, 3, '2026-01-08 13:51:19'),
(47, 150, 3, '2026-01-08 13:51:19'),
(48, 151, 4, '2026-01-08 13:51:19'),
(49, 152, 4, '2026-01-08 13:51:19'),
(50, 153, 4, '2026-01-08 13:51:19'),
(51, 154, 4, '2026-01-08 13:51:19'),
(52, 155, 4, '2026-01-08 13:51:19'),
(53, 156, 4, '2026-01-08 13:51:19'),
(54, 157, 4, '2026-01-08 13:51:19'),
(55, 158, 4, '2026-01-08 13:51:19'),
(56, 159, 4, '2026-01-08 13:51:19'),
(57, 160, 4, '2026-01-08 13:51:19'),
(58, 161, 4, '2026-01-08 13:51:19'),
(59, 162, 4, '2026-01-08 13:51:19'),
(60, 163, 4, '2026-01-08 13:51:19'),
(61, 164, 4, '2026-01-08 13:51:19'),
(62, 165, 4, '2026-01-08 13:51:19'),
(63, 166, 4, '2026-01-08 13:51:19'),
(64, 167, 4, '2026-01-08 13:51:19'),
(65, 168, 4, '2026-01-08 13:51:19'),
(66, 169, 4, '2026-01-08 13:51:19'),
(67, 170, 4, '2026-01-08 13:51:19'),
(68, 171, 5, '2026-01-08 13:51:19'),
(69, 172, 5, '2026-01-08 13:51:19'),
(70, 173, 5, '2026-01-08 13:51:19'),
(71, 174, 5, '2026-01-08 13:51:19'),
(72, 175, 5, '2026-01-08 13:51:19'),
(73, 176, 5, '2026-01-08 13:51:19'),
(74, 177, 5, '2026-01-08 13:51:19'),
(75, 178, 5, '2026-01-08 13:51:19'),
(76, 179, 5, '2026-01-08 13:51:19'),
(77, 180, 5, '2026-01-08 13:51:19'),
(78, 181, 5, '2026-01-08 13:51:19'),
(79, 182, 5, '2026-01-08 13:51:19'),
(80, 183, 5, '2026-01-08 13:51:19'),
(81, 184, 5, '2026-01-08 13:51:19'),
(82, 185, 5, '2026-01-08 13:51:19'),
(83, 186, 5, '2026-01-08 13:51:19'),
(84, 187, 5, '2026-01-08 13:51:19'),
(85, 188, 5, '2026-01-08 13:51:19'),
(86, 189, 5, '2026-01-08 13:51:19'),
(87, 190, 5, '2026-01-08 13:51:19'),
(88, 191, 6, '2026-01-08 13:51:19'),
(89, 192, 6, '2026-01-08 13:51:19'),
(90, 193, 6, '2026-01-08 13:51:19'),
(91, 194, 6, '2026-01-08 13:51:19'),
(92, 195, 6, '2026-01-08 13:51:19'),
(93, 196, 6, '2026-01-08 13:51:19'),
(94, 197, 6, '2026-01-08 13:51:19'),
(95, 198, 6, '2026-01-08 13:51:19'),
(96, 199, 6, '2026-01-08 13:51:19'),
(97, 200, 6, '2026-01-08 13:51:19'),
(98, 201, 6, '2026-01-08 13:51:19'),
(99, 202, 6, '2026-01-08 13:51:19'),
(100, 203, 6, '2026-01-08 13:51:19'),
(101, 204, 6, '2026-01-08 13:51:19'),
(102, 205, 6, '2026-01-08 13:51:19'),
(103, 206, 6, '2026-01-08 13:51:19'),
(104, 207, 6, '2026-01-08 13:51:19'),
(105, 208, 6, '2026-01-08 13:51:19'),
(106, 209, 6, '2026-01-08 13:51:19'),
(107, 210, 6, '2026-01-08 13:51:19'),
(108, 211, 7, '2026-01-08 13:51:19'),
(109, 212, 7, '2026-01-08 13:51:19'),
(110, 213, 7, '2026-01-08 13:51:19'),
(111, 214, 7, '2026-01-08 13:51:19'),
(112, 215, 7, '2026-01-08 13:51:19'),
(113, 216, 7, '2026-01-08 13:51:19'),
(114, 217, 7, '2026-01-08 13:51:19'),
(115, 218, 7, '2026-01-08 13:51:19'),
(116, 219, 7, '2026-01-08 13:51:19'),
(117, 220, 7, '2026-01-08 13:51:19'),
(118, 221, 7, '2026-01-08 13:51:19'),
(119, 222, 7, '2026-01-08 13:51:19'),
(120, 223, 7, '2026-01-08 13:51:19'),
(121, 224, 7, '2026-01-08 13:51:19'),
(122, 225, 7, '2026-01-08 13:51:19'),
(123, 226, 7, '2026-01-08 13:51:19'),
(124, 227, 7, '2026-01-08 13:51:19'),
(125, 228, 7, '2026-01-08 13:51:20'),
(126, 229, 7, '2026-01-08 13:51:20'),
(127, 230, 7, '2026-01-08 13:51:20'),
(128, 231, 40, '2026-01-08 13:51:20'),
(129, 232, 40, '2026-01-08 13:51:20'),
(130, 233, 40, '2026-01-08 13:51:20'),
(131, 234, 40, '2026-01-08 13:51:20'),
(132, 235, 40, '2026-01-08 13:51:20'),
(133, 236, 40, '2026-01-08 13:51:20'),
(134, 237, 40, '2026-01-08 13:51:20'),
(135, 238, 40, '2026-01-08 13:51:20'),
(136, 239, 40, '2026-01-08 13:51:20'),
(137, 240, 40, '2026-01-08 13:51:20'),
(138, 241, 40, '2026-01-08 13:51:20'),
(139, 242, 40, '2026-01-08 13:51:20'),
(140, 243, 40, '2026-01-08 13:51:20'),
(141, 244, 40, '2026-01-08 13:51:20'),
(142, 245, 40, '2026-01-08 13:51:20'),
(143, 246, 40, '2026-01-08 13:51:20'),
(144, 247, 40, '2026-01-08 13:51:20'),
(145, 248, 40, '2026-01-08 13:51:20'),
(146, 249, 40, '2026-01-08 13:51:20'),
(147, 250, 40, '2026-01-08 13:51:20');

-- --------------------------------------------------------

--
-- Table structure for table `class_teacher_assignments`
--

CREATE TABLE `class_teacher_assignments` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_teacher_assignments`
--

INSERT INTO `class_teacher_assignments` (`id`, `class_id`, `teacher_id`, `assigned_at`) VALUES
(1, 1, 2, '2026-01-08 10:05:11'),
(9, 5, 2, '2026-01-08 13:22:18'),
(10, 3, 10, '2026-01-08 13:22:18'),
(11, 6, 10, '2026-01-08 13:22:18'),
(12, 4, 11, '2026-01-08 13:22:18'),
(13, 7, 11, '2026-01-08 13:22:18'),
(14, 40, 64, '2026-01-09 18:23:05');

-- --------------------------------------------------------

--
-- Table structure for table `conduct`
--

CREATE TABLE `conduct` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `type` enum('reward','discipline') NOT NULL,
  `content` text NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `conduct`
--

INSERT INTO `conduct` (`id`, `student_id`, `type`, `content`, `date`) VALUES
(1, 3, 'reward', 'Giúp đỡ bạn bè', '2026-01-08'),
(2, 3, 'reward', 'Giúp đỡ bạn bè', '2026-01-08'),
(3, 3, 'reward', 'Giúp đỡ bạn bè', '2026-01-08'),
(4, 3, 'reward', 'Giúp đỡ bạn bè', '2026-01-08'),
(5, 3, 'reward', 'Giúp đỡ bạn bè', '2026-01-08'),
(6, 3, 'reward', 'Giúp đỡ bạn bè', '2026-01-08');

-- --------------------------------------------------------

--
-- Table structure for table `conduct_results`
--

CREATE TABLE `conduct_results` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `score` int(11) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `conduct_results`
--

INSERT INTO `conduct_results` (`id`, `student_id`, `teacher_id`, `class_id`, `date`, `score`, `comment`, `created_at`) VALUES
(1, 3, 2, 1, '2026-01-08', 90, 'Tiến bộ tốt', '2026-01-08 10:05:11'),
(2, 3, 2, 1, '2026-01-08', 90, 'Tiến bộ tốt', '2026-01-08 10:36:16'),
(3, 3, 2, 1, '2026-01-08', 90, 'Tiến bộ tốt', '2026-01-08 10:38:54'),
(4, 3, 2, 1, '2026-01-08', 90, 'Tiến bộ tốt', '2026-01-08 12:49:58'),
(5, 3, 2, 1, '2026-01-08', 90, 'Tiến bộ tốt', '2026-01-08 12:50:36'),
(6, 3, 2, 1, '2026-01-08', 90, 'Tiến bộ tốt', '2026-01-08 12:51:35');

-- --------------------------------------------------------

--
-- Table structure for table `conduct_rules`
--

CREATE TABLE `conduct_rules` (
  `id` int(11) NOT NULL,
  `rule_name` varchar(255) NOT NULL,
  `points` int(11) NOT NULL DEFAULT 0,
  `type` enum('plus','minus') DEFAULT 'minus',
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `conduct_rules`
--

INSERT INTO `conduct_rules` (`id`, `rule_name`, `points`, `type`, `description`) VALUES
(1, 'Đi học muộn', 2, 'minus', 'Đến lớp sau giờ vào học'),
(2, 'Không mặc đồng phục', 2, 'minus', 'Thiếu đồng phục theo quy định'),
(3, 'Nói chuyện trong giờ', 1, 'minus', 'Gây mất trật tự'),
(4, 'Đi học muộn', 2, 'minus', 'Đến lớp sau giờ vào học'),
(5, 'Không mặc đồng phục', 2, 'minus', 'Thiếu đồng phục theo quy định'),
(6, 'Nói chuyện trong giờ', 1, 'minus', 'Gây mất trật tự'),
(7, 'Đi học muộn', 2, 'minus', 'Đến lớp sau giờ vào học'),
(8, 'Không mặc đồng phục', 2, 'minus', 'Thiếu đồng phục theo quy định'),
(9, 'Nói chuyện trong giờ', 1, 'minus', 'Gây mất trật tự'),
(10, 'Đi học muộn', 2, 'minus', 'Đến lớp sau giờ vào học'),
(11, 'Không mặc đồng phục', 2, 'minus', 'Thiếu đồng phục theo quy định'),
(12, 'Nói chuyện trong giờ', 1, 'minus', 'Gây mất trật tự'),
(13, 'Đi học muộn', 2, 'minus', 'Đến lớp sau giờ vào học'),
(14, 'Không mặc đồng phục', 2, 'minus', 'Thiếu đồng phục theo quy định'),
(15, 'Nói chuyện trong giờ', 1, 'minus', 'Gây mất trật tự'),
(16, 'Đi học muộn', 2, 'minus', 'Đến lớp sau giờ vào học'),
(17, 'Không mặc đồng phục', 2, 'minus', 'Thiếu đồng phục theo quy định'),
(18, 'Nói chuyện trong giờ', 1, 'minus', 'Gây mất trật tự'),
(19, 'Đi học muộn', 2, 'minus', 'Học sinh đến lớp sau giờ truy bài'),
(20, 'Không mặc đồng phục', 5, 'minus', 'Thiếu áo đồng phục, phù hiệu hoặc khăn quàng'),
(21, 'Mất trật tự trong giờ', 2, 'minus', 'Gây ồn ào ảnh hưởng lớp học'),
(22, 'Xả rác bừa bãi', 5, 'minus', 'Vứt rác không đúng nơi quy định'),
(23, 'Sử dụng điện thoại trái phép', 10, 'minus', 'Dùng điện thoại trong giờ học khi chưa được phép'),
(24, 'Vô lễ với giáo viên', 20, 'minus', 'Có lời nói hoặc hành động thiếu tôn trọng'),
(25, 'Đi học muộn', 2, 'minus', 'Đến lớp sau giờ truy bài'),
(26, 'Không mặc đồng phục', 2, 'minus', 'Thiếu áo, quần, hoặc khăn quàng'),
(27, 'Nói chuyện riêng', 1, 'minus', 'Gây mất trật tự trong giờ học'),
(28, 'Xả rác bừa bãi', 5, 'minus', 'Vứt rác không đúng nơi quy định'),
(29, 'Vô lễ với giáo viên', 20, 'minus', 'Thái độ không tôn trọng'),
(30, 'Đánh nhau', 50, 'minus', 'Gây gổ, đánh nhau trong trường'),
(31, 'Sử dụng điện thoại', 5, 'minus', 'Dùng điện thoại trong giờ khi chưa cho phép'),
(32, 'Nhặt được của rơi', 5, 'plus', 'Trả lại đồ cho người mất'),
(33, 'Giúp đỡ bạn bè', 2, 'plus', 'Hỗ trợ bạn học tập hoặc khó khăn'),
(34, 'Tham gia phong trào', 5, 'plus', 'Tích cực tham gia hoạt động trường');

-- --------------------------------------------------------

--
-- Table structure for table `discipline_points`
--

CREATE TABLE `discipline_points` (
  `student_id` int(11) NOT NULL,
  `points` int(11) NOT NULL DEFAULT 100,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `discipline_points`
--

INSERT INTO `discipline_points` (`student_id`, `points`, `updated_at`) VALUES
(3, 100, '2026-01-08 12:51:25'),
(120, 100, '2026-01-08 13:53:25'),
(123, 98, '2026-01-11 11:49:12'),
(131, 100, '2026-01-08 17:42:20'),
(132, 100, '2026-01-08 17:42:23'),
(151, 100, '2026-01-08 13:53:35'),
(154, 100, '2026-01-08 13:53:53'),
(158, 100, '2026-01-08 13:53:45'),
(165, 98, '2026-01-09 23:11:45');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `success` tinyint(1) NOT NULL,
  `reason` varchar(100) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `username`, `success`, `reason`, `ip`, `created_at`) VALUES
(1, 'HS-00001', 1, 'success', '127.0.0.1', '2026-01-08 10:05:11'),
(2, 'admin', 0, 'wrong_password', '127.0.0.1', '2026-01-08 10:12:19'),
(3, 'admin', 0, 'wrong_password', '127.0.0.1', '2026-01-08 10:12:21'),
(4, 'admin', 0, 'wrong_password', '127.0.0.1', '2026-01-08 10:19:41'),
(5, 'admin', 1, 'success', '127.0.0.1', '2026-01-08 10:19:54'),
(6, 'admin', 1, 'success', '127.0.0.1', '2026-01-08 10:28:21'),
(7, 'HS-00001', 1, 'success', '127.0.0.1', '2026-01-08 10:36:16'),
(8, 'HS-00001', 1, 'success', '127.0.0.1', '2026-01-08 10:38:54'),
(9, 'GV-00002', 1, 'success', '127.0.0.1', '2026-01-08 12:25:11'),
(10, 'admin', 1, 'success', '127.0.0.1', '2026-01-08 12:44:27'),
(11, 'HS-00001', 1, 'success', '127.0.0.1', '2026-01-08 12:49:58'),
(12, 'HS-00001', 1, 'success', '127.0.0.1', '2026-01-08 12:50:36'),
(13, 'HS-00001', 1, 'success', '127.0.0.1', '2026-01-08 12:51:35'),
(14, 'HS-00001', 1, 'success', '127.0.0.1', '2026-01-08 12:58:35'),
(15, 'HS-00001', 1, 'success', '127.0.0.1', '2026-01-08 13:00:01'),
(16, 'admin', 1, 'success', '127.0.0.1', '2026-01-08 13:14:18'),
(17, 'admin', 1, 'success', '127.0.0.1', '2026-01-09 17:22:43'),
(18, 'admin', 0, 'wrong_password', '127.0.0.1', '2026-01-09 17:45:51'),
(19, 'admin', 1, 'success', '127.0.0.1', '2026-01-09 17:46:00'),
(20, 'GV-TEST-01', 1, 'success', '127.0.0.1', '2026-01-09 18:19:39'),
(21, 'admin', 1, 'success', '127.0.0.1', '2026-01-09 18:22:52'),
(22, 'GV-00001', 1, 'success', '127.0.0.1', '2026-01-10 10:10:18'),
(23, 'PH-00001', 1, 'success', '127.0.0.1', '2026-01-10 18:16:50'),
(24, 'GV-00002', 1, 'success', '127.0.0.1', '2026-01-10 18:18:08'),
(25, 'admin', 1, 'success', '127.0.0.1', '2026-01-10 18:19:14'),
(26, 'GV-00002', 1, 'success', '127.0.0.1', '2026-01-10 18:20:04'),
(27, 'GV-00001', 1, 'success', '127.0.0.1', '2026-01-10 18:22:26'),
(28, 'pham.cuong@thpt-thd.edu.vn', 1, 'success', '127.0.0.1', '2026-01-10 18:25:17'),
(29, 'pham.cuong@thpt-thd.edu.vn', 0, 'not_found', '127.0.0.1', '2026-01-10 18:46:06'),
(30, 'pham.cuong@thpt-thd.edu.vn', 1, 'success', '127.0.0.1', '2026-01-10 18:46:11'),
(31, 'GV-00001', 1, 'success', '127.0.0.1', '2026-01-10 18:46:45'),
(32, 'pham.cuong@thpt-thd.edu.vn', 1, 'success', '127.0.0.1', '2026-01-10 18:48:38'),
(33, 'GV-00001', 1, 'success', '127.0.0.1', '2026-01-10 18:53:26'),
(34, 'pham.cuong@thpt-thd.edu.vn', 1, 'success', '127.0.0.1', '2026-01-10 18:54:18'),
(35, 'pham.cuong@thpt-thd.edu.vn', 1, 'success', '127.0.0.1', '2026-01-10 18:54:21'),
(36, 'pham.cuong@thpt-thd.edu.vn', 1, 'success', '127.0.0.1', '2026-01-10 18:54:21'),
(37, 'pham.cuong@thpt-thd.edu.vn', 0, 'not_found', '127.0.0.1', '2026-01-10 18:56:12'),
(38, 'pham.cuong@thpt-thd.edu.vn', 1, 'success', '127.0.0.1', '2026-01-10 18:56:17'),
(39, 'ph.cuong@thpt-thd.edu.vn', 0, 'not_found', '127.0.0.1', '2026-01-10 18:56:48'),
(40, 'ph.cuong@thpt-thd.edu.vn', 0, 'not_found', '127.0.0.1', '2026-01-10 18:56:57'),
(41, 'hs.giang@thpt-thd.edu.vn', 1, 'success', '127.0.0.1', '2026-01-10 18:57:06'),
(42, 'hs.giang@thpt-thd.edu.vn', 1, 'success', '127.0.0.1', '2026-01-10 18:57:12'),
(43, 'hs.giang@thpt-thd.edu.vn', 1, 'success', '127.0.0.1', '2026-01-10 18:57:28'),
(44, 'hs.giang@thpt-thd.edu.vn', 1, 'success', '127.0.0.1', '2026-01-10 18:57:34'),
(45, 'admin', 1, 'success', '127.0.0.1', '2026-01-10 20:00:44'),
(46, 'admin', 0, 'wrong_password', '127.0.0.1', '2026-01-10 20:46:51'),
(47, 'admin', 1, 'success', '127.0.0.1', '2026-01-10 20:47:37'),
(48, 'admin', 0, 'wrong_password', '127.0.0.1', '2026-01-10 21:20:41'),
(49, 'admin', 0, 'wrong_password', '127.0.0.1', '2026-01-10 21:20:49'),
(50, 'admin', 1, 'success', '127.0.0.1', '2026-01-10 21:21:02'),
(51, 'GV-00001', 1, 'success', '127.0.0.1', '2026-01-11 11:17:15');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `content`, `is_read`, `created_at`) VALUES
(1, 2, 3, 'Chuẩn bị bài Toán chương I', 0, '2026-01-08 10:05:10'),
(2, 2, 3, 'Chuẩn bị bài Toán chương I', 0, '2026-01-08 10:36:16'),
(3, 10, 12, 'Ôn tập Hóa bài 5', 0, '2026-01-08 10:36:16'),
(4, 11, 12, 'Chuẩn bị Sinh học', 0, '2026-01-08 10:36:16'),
(5, 12, 10, 'Thầy ơi em thắc mắc bài 3', 0, '2026-01-08 10:36:16'),
(6, 13, 10, 'Xin phép vắng họp tuần này', 0, '2026-01-08 10:36:16'),
(7, 2, 3, 'Chuẩn bị bài Toán chương I', 0, '2026-01-08 10:38:54'),
(8, 2, 3, 'Chuẩn bị bài Toán chương I', 0, '2026-01-08 12:49:58'),
(9, 2, 3, 'Chuẩn bị bài Toán chương I', 0, '2026-01-08 12:50:36'),
(10, 2, 3, 'Chuẩn bị bài Toán chương I', 0, '2026-01-08 12:51:35'),
(11, 2, 3, 'Chuẩn bị bài Toán chương I', 0, '2026-01-08 12:58:35'),
(12, 2, 3, 'Chuẩn bị bài Toán chương I', 0, '2026-01-08 13:00:01'),
(13, 64, 1, 'Yêu cầu quản lý lớp bởi GV-64: 12A1 - Thời gian: 2026-01-09 19:22:07 [REQ:7]', 0, '2026-01-09 18:22:08'),
(14, 64, 9, 'Yêu cầu quản lý lớp bởi GV-64: 12A1 - Thời gian: 2026-01-09 19:22:07 [REQ:7]', 0, '2026-01-09 18:22:08'),
(15, 64, 74, 'Yêu cầu quản lý lớp bởi GV-64: 12A1 - Thời gian: 2026-01-09 19:22:07 [REQ:7]', 0, '2026-01-09 18:22:08'),
(16, 64, 1, 'Yêu cầu quản lý lớp bởi GV-64: KL-01 - Thời gian: 2026-01-09 19:22:08 [REQ:8]', 0, '2026-01-09 18:22:08'),
(17, 64, 9, 'Yêu cầu quản lý lớp bởi GV-64: KL-01 - Thời gian: 2026-01-09 19:22:08 [REQ:8]', 0, '2026-01-09 18:22:08'),
(18, 64, 74, 'Yêu cầu quản lý lớp bởi GV-64: KL-01 - Thời gian: 2026-01-09 19:22:08 [REQ:8]', 0, '2026-01-09 18:22:08'),
(19, 10, 1, 'Yêu cầu quản lý lớp bởi GV-10: 12A1 - Thời gian: 2026-01-10 19:18:52 [REQ:9]', 0, '2026-01-10 18:18:52'),
(20, 10, 9, 'Yêu cầu quản lý lớp bởi GV-10: 12A1 - Thời gian: 2026-01-10 19:18:52 [REQ:9]', 0, '2026-01-10 18:18:52'),
(21, 10, 74, 'Yêu cầu quản lý lớp bởi GV-10: 12A1 - Thời gian: 2026-01-10 19:18:52 [REQ:9]', 0, '2026-01-10 18:18:52');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `summary` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `author_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `summary`, `content`, `image_url`, `author_id`, `created_at`) VALUES
(1, 'Khai giảng năm học mới', 'Lễ khai giảng 05/09', 'Nội dung chi tiết...', 'https://placehold.co/800x400.png?text=1', 2, '2026-01-08 10:05:11'),
(2, 'Kế hoạch thi HK1', 'Lịch thi các môn', 'Nội dung chi tiết...', 'https://placehold.co/800x400.png?text=2', 2, '2026-01-08 10:05:11'),
(3, 'Khai giảng năm học mới', 'Lễ khai giảng 05/09', 'Nội dung chi tiết...', 'https://placehold.co/800x400.png?text=1', 2, '2026-01-08 10:36:16'),
(4, 'Kế hoạch thi HK1', 'Lịch thi các môn', 'Nội dung chi tiết...', 'https://placehold.co/800x400.png?text=2', 2, '2026-01-08 10:36:16'),
(5, 'Sự kiện 3', 'Ngày hội đọc sách', 'Chi tiết...', 'https://placehold.co/800x400.png?text=3', 11, '2026-01-08 10:36:16'),
(6, 'Sự kiện 4', 'Thi KHKT cấp trường', 'Chi tiết...', 'https://placehold.co/800x400.png?text=4', 10, '2026-01-08 10:36:16'),
(7, 'Sự kiện 5', 'Tuần lễ ATGT', 'Chi tiết...', 'https://placehold.co/800x400.png?text=5', 2, '2026-01-08 10:36:16'),
(8, 'Khai giảng năm học mới', 'Lễ khai giảng 05/09', 'Nội dung chi tiết...', 'https://placehold.co/800x400.png?text=1', 2, '2026-01-08 10:38:54'),
(9, 'Kế hoạch thi HK1', 'Lịch thi các môn', 'Nội dung chi tiết...', 'https://placehold.co/800x400.png?text=2', 2, '2026-01-08 10:38:54'),
(10, 'Khai giảng năm học mới', 'Lễ khai giảng 05/09', 'Nội dung chi tiết...', 'https://placehold.co/800x400.png?text=1', 2, '2026-01-08 12:49:58'),
(11, 'Kế hoạch thi HK1', 'Lịch thi các môn', 'Nội dung chi tiết...', 'https://placehold.co/800x400.png?text=2', 2, '2026-01-08 12:49:58'),
(12, 'Khai giảng năm học mới', 'Lễ khai giảng 05/09', 'Nội dung chi tiết...', 'https://placehold.co/800x400.png?text=1', 2, '2026-01-08 12:50:36'),
(13, 'Kế hoạch thi HK1', 'Lịch thi các môn', 'Nội dung chi tiết...', 'https://placehold.co/800x400.png?text=2', 2, '2026-01-08 12:50:36'),
(14, 'Khai giảng năm học mới', 'Lễ khai giảng 05/09', 'Nội dung chi tiết...', 'https://placehold.co/800x400.png?text=1', 2, '2026-01-08 12:51:35'),
(15, 'Kế hoạch thi HK1', 'Lịch thi các môn', 'Nội dung chi tiết...', 'https://placehold.co/800x400.png?text=2', 2, '2026-01-08 12:51:35'),
(16, 'Khai giảng năm học mới', 'Lễ khai giảng 05/09', 'Nội dung chi tiết...', 'https://placehold.co/800x400.png?text=1', 2, '2026-01-08 12:58:35'),
(17, 'Kế hoạch thi HK1', 'Lịch thi các môn', 'Nội dung chi tiết...', 'https://placehold.co/800x400.png?text=2', 2, '2026-01-08 12:58:35'),
(18, 'Khai giảng năm học mới', 'Lễ khai giảng 05/09', 'Nội dung chi tiết...', 'https://placehold.co/800x400.png?text=1', 2, '2026-01-08 13:00:01'),
(19, 'Kế hoạch thi HK1', 'Lịch thi các môn', 'Nội dung chi tiết...', 'https://placehold.co/800x400.png?text=2', 2, '2026-01-08 13:00:01');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `target_role` enum('all','teacher','student','parent') DEFAULT 'all',
  `priority` enum('normal','high','urgent') DEFAULT 'normal',
  `attachment_url` varchar(255) DEFAULT NULL,
  `is_pinned` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `title`, `content`, `sender_id`, `receiver_id`, `target_role`, `priority`, `attachment_url`, `is_pinned`, `created_at`) VALUES
(1, 'Thông báo khai giảng', 'Lễ khai giảng 05/09', 2, NULL, 'all', 'normal', NULL, 0, '2026-01-08 10:05:10'),
(2, 'Thông báo khai giảng', 'Lễ khai giảng 05/09', 2, NULL, 'all', 'normal', NULL, 0, '2026-01-08 10:36:16'),
(3, 'Thông báo số 2', 'Họp hội đồng chiều thứ 4', 10, NULL, 'teacher', 'high', NULL, 0, '2026-01-08 10:36:16'),
(4, 'Thông báo số 3', 'Sinh hoạt lớp sáng thứ 6', 11, NULL, 'student', 'normal', NULL, 0, '2026-01-08 10:36:16'),
(5, 'Thông báo số 4', 'Mời phụ huynh dự họp', 2, NULL, 'parent', 'high', NULL, 0, '2026-01-08 10:36:16'),
(6, 'Thông báo số 5', 'Tạm nghỉ do mưa lớn', 10, NULL, 'all', 'urgent', NULL, 0, '2026-01-08 10:36:16'),
(7, 'Thông báo khai giảng', 'Lễ khai giảng 05/09', 2, NULL, 'all', 'normal', NULL, 0, '2026-01-08 10:38:54'),
(8, 'Thông báo khai giảng', 'Lễ khai giảng 05/09', 2, NULL, 'all', 'normal', NULL, 0, '2026-01-08 12:49:58'),
(9, 'Thông báo khai giảng', 'Lễ khai giảng 05/09', 2, NULL, 'all', 'normal', NULL, 0, '2026-01-08 12:50:36'),
(10, 'Thông báo khai giảng', 'Lễ khai giảng 05/09', 2, NULL, 'all', 'normal', NULL, 0, '2026-01-08 12:51:35'),
(11, 'Thông báo khai giảng', 'Lễ khai giảng 05/09', 2, NULL, 'all', 'normal', NULL, 0, '2026-01-08 12:58:35'),
(12, 'Thông báo khai giảng', 'Lễ khai giảng 05/09', 2, NULL, 'all', 'normal', NULL, 0, '2026-01-08 13:00:01'),
(13, 'Yêu cầu phân lớp', 'Yêu cầu quản lý lớp bởi GV-64: 12A1, KL-01 - Thời gian: 2026-01-09 19:22:08', 64, NULL, '', 'normal', NULL, 0, '2026-01-09 18:22:08'),
(14, 'Kết quả yêu cầu phân lớp', 'Yêu cầu quản lý lớp đã được phê duyệt', 1, NULL, 'teacher', 'normal', NULL, 0, '2026-01-09 18:23:05'),
(15, 'Yêu cầu phân lớp', 'Yêu cầu quản lý lớp bởi GV-10: 12A1 - Thời gian: 2026-01-10 19:18:52', 10, NULL, '', 'normal', NULL, 0, '2026-01-10 18:18:52');

-- --------------------------------------------------------

--
-- Table structure for table `otp_codes`
--

CREATE TABLE `otp_codes` (
  `id` int(11) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `code` varchar(10) NOT NULL,
  `expires_at` datetime NOT NULL,
  `is_used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `otp_codes`
--

INSERT INTO `otp_codes` (`id`, `phone`, `code`, `expires_at`, `is_used`, `created_at`) VALUES
(1, '0912345678', '123456', '2026-01-08 17:15:11', 0, '2026-01-08 10:05:11'),
(2, '0912345678', '123456', '2026-01-08 17:46:16', 0, '2026-01-08 10:36:16'),
(3, '0912345678', '123456', '2026-01-08 17:48:54', 0, '2026-01-08 10:38:54'),
(4, '0912345678', '123456', '2026-01-08 19:59:58', 0, '2026-01-08 12:49:58'),
(5, '0912345678', '123456', '2026-01-08 20:00:36', 0, '2026-01-08 12:50:36'),
(6, '0912345678', '123456', '2026-01-08 20:01:35', 0, '2026-01-08 12:51:35'),
(7, '0912345678', '123456', '2026-01-08 20:08:35', 0, '2026-01-08 12:58:35'),
(8, '0912345678', '123456', '2026-01-08 20:10:01', 0, '2026-01-08 13:00:01');

-- --------------------------------------------------------

--
-- Table structure for table `parent_student_links`
--

CREATE TABLE `parent_student_links` (
  `parent_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parent_student_links`
--

INSERT INTO `parent_student_links` (`parent_id`, `student_id`) VALUES
(4, 3),
(13, 12),
(90, 80),
(91, 81),
(92, 82);

-- --------------------------------------------------------

--
-- Table structure for table `red_committee_members`
--

CREATE TABLE `red_committee_members` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `active` tinyint(1) DEFAULT 1,
  `assigned_by` int(11) NOT NULL,
  `duration_weeks` int(11) DEFAULT 4,
  `start_date` date NOT NULL,
  `expired_at` date NOT NULL,
  `revoked_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE `schedule` (
  `id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `day_of_week` int(11) NOT NULL,
  `period` int(11) NOT NULL,
  `semester` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`id`, `class_id`, `subject_id`, `teacher_id`, `day_of_week`, `period`, `semester`) VALUES
(1, 1, 1, 2, 2, 1, 'HK1-2025'),
(2, 1, 2, 2, 2, 2, 'HK1-2025'),
(3, 1, 3, 2, 3, 1, 'HK1-2025'),
(4, 1, 1, 2, 2, 1, 'HK1-2025'),
(5, 1, 2, 2, 2, 2, 'HK1-2025'),
(6, 1, 3, 2, 3, 1, 'HK1-2025'),
(7, 1, 1, 2, 2, 1, 'HK1-2025'),
(8, 1, 2, 2, 2, 2, 'HK1-2025'),
(9, 1, 3, 2, 3, 1, 'HK1-2025'),
(10, 1, 1, 2, 2, 1, 'HK1-2025'),
(11, 1, 2, 2, 2, 2, 'HK1-2025'),
(12, 1, 3, 2, 3, 1, 'HK1-2025'),
(13, 1, 1, 2, 2, 1, 'HK1-2025'),
(14, 1, 2, 2, 2, 2, 'HK1-2025'),
(15, 1, 3, 2, 3, 1, 'HK1-2025'),
(16, 1, 1, 2, 2, 1, 'HK1-2025'),
(17, 1, 2, 2, 2, 2, 'HK1-2025'),
(18, 1, 3, 2, 3, 1, 'HK1-2025'),
(19, 1, 1, 2, 2, 1, 'HK1-2025'),
(20, 1, 2, 2, 2, 2, 'HK1-2025'),
(21, 1, 3, 2, 3, 1, 'HK1-2025'),
(22, 1, 1, 2, 2, 1, 'HK1-2025'),
(23, 1, 2, 2, 2, 2, 'HK1-2025'),
(24, 1, 3, 2, 3, 1, 'HK1-2025');

-- --------------------------------------------------------

--
-- Table structure for table `scores`
--

CREATE TABLE `scores` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `semester` varchar(10) NOT NULL,
  `score_15m` float DEFAULT NULL,
  `score_45m` float DEFAULT NULL,
  `score_final` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scores`
--

INSERT INTO `scores` (`id`, `student_id`, `subject_id`, `semester`, `score_15m`, `score_45m`, `score_final`) VALUES
(1, 3, 1, 'HK1-2025', 8, 8.5, 8.3),
(2, 3, 1, 'HK1-2025', 8, 8.5, 8.3),
(3, 12, 7, 'HK1-2025', 7.5, 8, 8),
(4, 12, 8, 'HK1-2025', 8, 8.5, 8.3),
(5, 12, 9, 'HK1-2025', 7, 7.5, 7.6),
(6, 12, 10, 'HK1-2025', 8.2, 8.4, 8.3),
(7, 12, 11, 'HK1-2025', 9, 9.2, 9.1),
(8, 3, 1, 'HK1-2025', 8, 8.5, 8.3),
(9, 3, 1, 'HK1-2025', 8, 8.5, 8.3),
(10, 3, 1, 'HK1-2025', 8, 8.5, 8.3),
(11, 3, 1, 'HK1-2025', 8, 8.5, 8.3);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `student_code` varchar(50) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_code`, `full_name`, `avatar`) VALUES
(3, 'HS-00001', 'Trần Văn B', NULL),
(12, 'HS-00002', 'Vũ Hoài Phương', NULL),
(64, 'GV-TEST-01', 'Cô Giáo Test', NULL),
(65, 'HS-TEST-01', 'Em Học Sinh Test', NULL),
(66, 'HS-UNVERIFIED', 'Học Sinh Chưa Verify', NULL),
(67, 'HS-LOCKED', 'Học Sinh Bị Khóa', NULL),
(68, 'HS-NEW-PASS', 'Học Sinh Mới', NULL),
(75, 'GV-ND-02', 'Thầy Nguyễn Văn Toán', NULL),
(76, 'GV-ND-03', 'Cô Lê Thị Văn', NULL),
(77, 'GV-ND-04', 'Thầy Trần Văn Lý', NULL),
(78, 'GV-ND-05', 'Cô Phạm Thị Hóa', NULL),
(79, 'GV-ND-06', 'Thầy Hoàng Văn Thể', NULL),
(80, 'HS-ND-02', 'Nguyễn Văn An', NULL),
(81, 'HS-ND-03', 'Trần Thị Bình', NULL),
(82, 'HS-ND-04', 'Lê Văn Cường', NULL),
(83, 'HS-ND-05', 'Phạm Thị Dung', NULL),
(84, 'HS-ND-06', 'Hoàng Văn Em', NULL),
(85, 'HS-ND-07', 'Đỗ Thị Phương', NULL),
(86, 'HS-ND-08', 'Ngô Văn Giang', NULL),
(87, 'HS-ND-09', 'Bùi Thị Hoa', NULL),
(88, 'HS-ND-10', 'Vũ Văn Hùng', NULL),
(89, 'HS-ND-11', 'Lý Thị Lan', NULL),
(90, 'PH-ND-01', 'Bố Của An', NULL),
(91, 'PH-ND-02', 'Mẹ Của Bình', NULL),
(92, 'PH-ND-03', 'Bố Của Cường', NULL),
(93, 'HS-ND-ERR', 'Học Sinh Lỗi', NULL),
(120, 'HS-10A1-10', 'Học Sinh 10A1 Số 10', NULL),
(121, 'HS-10A1-11', 'Học Sinh 10A1 Số 11', NULL),
(122, 'HS-10A1-12', 'Học Sinh 10A1 Số 12', NULL),
(123, 'HS-10A1-13', 'Học Sinh 10A1 Số 13', NULL),
(124, 'HS-10A1-14', 'Học Sinh 10A1 Số 14', NULL),
(125, 'HS-10A1-15', 'Học Sinh 10A1 Số 15', NULL),
(126, 'HS-10A1-16', 'Học Sinh 10A1 Số 16', NULL),
(127, 'HS-10A1-17', 'Học Sinh 10A1 Số 17', NULL),
(128, 'HS-10A1-18', 'Học Sinh 10A1 Số 18', NULL),
(129, 'HS-10A1-19', 'Học Sinh 10A1 Số 19', NULL),
(130, 'HS-10A1-20', 'Học Sinh 10A1 Số 20', NULL),
(131, 'HS-10A2-01', 'Học Sinh 10A2 Số 1', NULL),
(132, 'HS-10A2-02', 'Học Sinh 10A2 Số 2', NULL),
(133, 'HS-10A2-03', 'Học Sinh 10A2 Số 3', NULL),
(134, 'HS-10A2-04', 'Học Sinh 10A2 Số 4', NULL),
(135, 'HS-10A2-05', 'Học Sinh 10A2 Số 5', NULL),
(136, 'HS-10A2-06', 'Học Sinh 10A2 Số 6', NULL),
(137, 'HS-10A2-07', 'Học Sinh 10A2 Số 7', NULL),
(138, 'HS-10A2-08', 'Học Sinh 10A2 Số 8', NULL),
(139, 'HS-10A2-09', 'Học Sinh 10A2 Số 9', NULL),
(140, 'HS-10A2-10', 'Học Sinh 10A2 Số 10', NULL),
(141, 'HS-10A2-11', 'Học Sinh 10A2 Số 11', NULL),
(142, 'HS-10A2-12', 'Học Sinh 10A2 Số 12', NULL),
(143, 'HS-10A2-13', 'Học Sinh 10A2 Số 13', NULL),
(144, 'HS-10A2-14', 'Học Sinh 10A2 Số 14', NULL),
(145, 'HS-10A2-15', 'Học Sinh 10A2 Số 15', NULL),
(146, 'HS-10A2-16', 'Học Sinh 10A2 Số 16', NULL),
(147, 'HS-10A2-17', 'Học Sinh 10A2 Số 17', NULL),
(148, 'HS-10A2-18', 'Học Sinh 10A2 Số 18', NULL),
(149, 'HS-10A2-19', 'Học Sinh 10A2 Số 19', NULL),
(150, 'HS-10A2-20', 'Học Sinh 10A2 Số 20', NULL),
(151, 'HS-10A3-01', 'Học Sinh 10A3 Số 1', NULL),
(152, 'HS-10A3-02', 'Học Sinh 10A3 Số 2', NULL),
(153, 'HS-10A3-03', 'Học Sinh 10A3 Số 3', NULL),
(154, 'HS-10A3-04', 'Học Sinh 10A3 Số 4', NULL),
(155, 'HS-10A3-05', 'Học Sinh 10A3 Số 5', NULL),
(156, 'HS-10A3-06', 'Học Sinh 10A3 Số 6', NULL),
(157, 'HS-10A3-07', 'Học Sinh 10A3 Số 7', NULL),
(158, 'HS-10A3-08', 'Học Sinh 10A3 Số 8', NULL),
(159, 'HS-10A3-09', 'Học Sinh 10A3 Số 9', NULL),
(160, 'HS-10A3-10', 'Học Sinh 10A3 Số 10', NULL),
(161, 'HS-10A3-11', 'Học Sinh 10A3 Số 11', NULL),
(162, 'HS-10A3-12', 'Học Sinh 10A3 Số 12', NULL),
(163, 'HS-10A3-13', 'Học Sinh 10A3 Số 13', NULL),
(164, 'HS-10A3-14', 'Học Sinh 10A3 Số 14', NULL),
(165, 'HS-10A3-15', 'Học Sinh 10A3 Số 15', NULL),
(166, 'HS-10A3-16', 'Học Sinh 10A3 Số 16', NULL),
(167, 'HS-10A3-17', 'Học Sinh 10A3 Số 17', NULL),
(168, 'HS-10A3-18', 'Học Sinh 10A3 Số 18', NULL),
(169, 'HS-10A3-19', 'Học Sinh 10A3 Số 19', NULL),
(170, 'HS-10A3-20', 'Học Sinh 10A3 Số 20', NULL),
(171, 'HS-11A1-01', 'Học Sinh 11A1 Số 1', NULL),
(172, 'HS-11A1-02', 'Học Sinh 11A1 Số 2', NULL),
(173, 'HS-11A1-03', 'Học Sinh 11A1 Số 3', NULL),
(174, 'HS-11A1-04', 'Học Sinh 11A1 Số 4', NULL),
(175, 'HS-11A1-05', 'Học Sinh 11A1 Số 5', NULL),
(176, 'HS-11A1-06', 'Học Sinh 11A1 Số 6', NULL),
(177, 'HS-11A1-07', 'Học Sinh 11A1 Số 7', NULL),
(178, 'HS-11A1-08', 'Học Sinh 11A1 Số 8', NULL),
(179, 'HS-11A1-09', 'Học Sinh 11A1 Số 9', NULL),
(180, 'HS-11A1-10', 'Học Sinh 11A1 Số 10', NULL),
(181, 'HS-11A1-11', 'Học Sinh 11A1 Số 11', NULL),
(182, 'HS-11A1-12', 'Học Sinh 11A1 Số 12', NULL),
(183, 'HS-11A1-13', 'Học Sinh 11A1 Số 13', NULL),
(184, 'HS-11A1-14', 'Học Sinh 11A1 Số 14', NULL),
(185, 'HS-11A1-15', 'Học Sinh 11A1 Số 15', NULL),
(186, 'HS-11A1-16', 'Học Sinh 11A1 Số 16', NULL),
(187, 'HS-11A1-17', 'Học Sinh 11A1 Số 17', NULL),
(188, 'HS-11A1-18', 'Học Sinh 11A1 Số 18', NULL),
(189, 'HS-11A1-19', 'Học Sinh 11A1 Số 19', NULL),
(190, 'HS-11A1-20', 'Học Sinh 11A1 Số 20', NULL),
(191, 'HS-11A2-01', 'Học Sinh 11A2 Số 1', NULL),
(192, 'HS-11A2-02', 'Học Sinh 11A2 Số 2', NULL),
(193, 'HS-11A2-03', 'Học Sinh 11A2 Số 3', NULL),
(194, 'HS-11A2-04', 'Học Sinh 11A2 Số 4', NULL),
(195, 'HS-11A2-05', 'Học Sinh 11A2 Số 5', NULL),
(196, 'HS-11A2-06', 'Học Sinh 11A2 Số 6', NULL),
(197, 'HS-11A2-07', 'Học Sinh 11A2 Số 7', NULL),
(198, 'HS-11A2-08', 'Học Sinh 11A2 Số 8', NULL),
(199, 'HS-11A2-09', 'Học Sinh 11A2 Số 9', NULL),
(200, 'HS-11A2-10', 'Học Sinh 11A2 Số 10', NULL),
(201, 'HS-11A2-11', 'Học Sinh 11A2 Số 11', NULL),
(202, 'HS-11A2-12', 'Học Sinh 11A2 Số 12', NULL),
(203, 'HS-11A2-13', 'Học Sinh 11A2 Số 13', NULL),
(204, 'HS-11A2-14', 'Học Sinh 11A2 Số 14', NULL),
(205, 'HS-11A2-15', 'Học Sinh 11A2 Số 15', NULL),
(206, 'HS-11A2-16', 'Học Sinh 11A2 Số 16', NULL),
(207, 'HS-11A2-17', 'Học Sinh 11A2 Số 17', NULL),
(208, 'HS-11A2-18', 'Học Sinh 11A2 Số 18', NULL),
(209, 'HS-11A2-19', 'Học Sinh 11A2 Số 19', NULL),
(210, 'HS-11A2-20', 'Học Sinh 11A2 Số 20', NULL),
(211, 'HS-12A1-01', 'Học Sinh 12A1 Số 1', NULL),
(212, 'HS-12A1-02', 'Học Sinh 12A1 Số 2', NULL),
(213, 'HS-12A1-03', 'Học Sinh 12A1 Số 3', NULL),
(214, 'HS-12A1-04', 'Học Sinh 12A1 Số 4', NULL),
(215, 'HS-12A1-05', 'Học Sinh 12A1 Số 5', NULL),
(216, 'HS-12A1-06', 'Học Sinh 12A1 Số 6', NULL),
(217, 'HS-12A1-07', 'Học Sinh 12A1 Số 7', NULL),
(218, 'HS-12A1-08', 'Học Sinh 12A1 Số 8', NULL),
(219, 'HS-12A1-09', 'Học Sinh 12A1 Số 9', NULL),
(220, 'HS-12A1-10', 'Học Sinh 12A1 Số 10', NULL),
(221, 'HS-12A1-11', 'Học Sinh 12A1 Số 11', NULL),
(222, 'HS-12A1-12', 'Học Sinh 12A1 Số 12', NULL),
(223, 'HS-12A1-13', 'Học Sinh 12A1 Số 13', NULL),
(224, 'HS-12A1-14', 'Học Sinh 12A1 Số 14', NULL),
(225, 'HS-12A1-15', 'Học Sinh 12A1 Số 15', NULL),
(226, 'HS-12A1-16', 'Học Sinh 12A1 Số 16', NULL),
(227, 'HS-12A1-17', 'Học Sinh 12A1 Số 17', NULL),
(228, 'HS-12A1-18', 'Học Sinh 12A1 Số 18', NULL),
(229, 'HS-12A1-19', 'Học Sinh 12A1 Số 19', NULL),
(230, 'HS-12A1-20', 'Học Sinh 12A1 Số 20', NULL),
(231, 'HS-KL-01-01', 'Học Sinh KL-01 Số 1', NULL),
(232, 'HS-KL-01-02', 'Học Sinh KL-01 Số 2', NULL),
(233, 'HS-KL-01-03', 'Học Sinh KL-01 Số 3', NULL),
(234, 'HS-KL-01-04', 'Học Sinh KL-01 Số 4', NULL),
(235, 'HS-KL-01-05', 'Học Sinh KL-01 Số 5', NULL),
(236, 'HS-KL-01-06', 'Học Sinh KL-01 Số 6', NULL),
(237, 'HS-KL-01-07', 'Học Sinh KL-01 Số 7', NULL),
(238, 'HS-KL-01-08', 'Học Sinh KL-01 Số 8', NULL),
(239, 'HS-KL-01-09', 'Học Sinh KL-01 Số 9', NULL),
(240, 'HS-KL-01-10', 'Học Sinh KL-01 Số 10', NULL),
(241, 'HS-KL-01-11', 'Học Sinh KL-01 Số 11', NULL),
(242, 'HS-KL-01-12', 'Học Sinh KL-01 Số 12', NULL),
(243, 'HS-KL-01-13', 'Học Sinh KL-01 Số 13', NULL),
(244, 'HS-KL-01-14', 'Học Sinh KL-01 Số 14', NULL),
(245, 'HS-KL-01-15', 'Học Sinh KL-01 Số 15', NULL),
(246, 'HS-KL-01-16', 'Học Sinh KL-01 Số 16', NULL),
(247, 'HS-KL-01-17', 'Học Sinh KL-01 Số 17', NULL),
(248, 'HS-KL-01-18', 'Học Sinh KL-01 Số 18', NULL),
(249, 'HS-KL-01-19', 'Học Sinh KL-01 Số 19', NULL),
(250, 'HS-KL-01-20', 'Học Sinh KL-01 Số 20', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_details`
--

CREATE TABLE `student_details` (
  `user_id` int(11) NOT NULL,
  `class_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_details`
--

INSERT INTO `student_details` (`user_id`, `class_id`) VALUES
(80, NULL),
(81, NULL),
(82, NULL),
(83, NULL),
(84, NULL),
(85, NULL),
(86, NULL),
(87, NULL),
(88, NULL),
(89, NULL),
(93, NULL),
(3, 1),
(64, 1),
(120, 1),
(121, 1),
(122, 1),
(123, 1),
(124, 1),
(125, 1),
(126, 1),
(127, 1),
(128, 1),
(129, 1),
(130, 1),
(12, 3),
(131, 3),
(132, 3),
(133, 3),
(134, 3),
(135, 3),
(136, 3),
(137, 3),
(138, 3),
(139, 3),
(140, 3),
(141, 3),
(142, 3),
(143, 3),
(144, 3),
(145, 3),
(146, 3),
(147, 3),
(148, 3),
(149, 3),
(150, 3),
(151, 4),
(152, 4),
(153, 4),
(154, 4),
(155, 4),
(156, 4),
(157, 4),
(158, 4),
(159, 4),
(160, 4),
(161, 4),
(162, 4),
(163, 4),
(164, 4),
(165, 4),
(166, 4),
(167, 4),
(168, 4),
(169, 4),
(170, 4),
(171, 5),
(172, 5),
(173, 5),
(174, 5),
(175, 5),
(176, 5),
(177, 5),
(178, 5),
(179, 5),
(180, 5),
(181, 5),
(182, 5),
(183, 5),
(184, 5),
(185, 5),
(186, 5),
(187, 5),
(188, 5),
(189, 5),
(190, 5),
(191, 6),
(192, 6),
(193, 6),
(194, 6),
(195, 6),
(196, 6),
(197, 6),
(198, 6),
(199, 6),
(200, 6),
(201, 6),
(202, 6),
(203, 6),
(204, 6),
(205, 6),
(206, 6),
(207, 6),
(208, 6),
(209, 6),
(210, 6),
(211, 7),
(212, 7),
(213, 7),
(214, 7),
(215, 7),
(216, 7),
(217, 7),
(218, 7),
(219, 7),
(220, 7),
(221, 7),
(222, 7),
(223, 7),
(224, 7),
(225, 7),
(226, 7),
(227, 7),
(228, 7),
(229, 7),
(230, 7),
(231, 40),
(232, 40),
(233, 40),
(234, 40),
(235, 40),
(236, 40),
(237, 40),
(238, 40),
(239, 40),
(240, 40),
(241, 40),
(242, 40),
(243, 40),
(244, 40),
(245, 40),
(246, 40),
(247, 40),
(248, 40),
(249, 40),
(250, 40);

-- --------------------------------------------------------

--
-- Table structure for table `student_profiles`
--

CREATE TABLE `student_profiles` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `birth_date` date DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `guardian_name` varchar(255) DEFAULT NULL,
  `guardian_phone` varchar(50) DEFAULT NULL,
  `is_complete` tinyint(1) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_profiles`
--

INSERT INTO `student_profiles` (`user_id`, `full_name`, `birth_date`, `address`, `phone`, `email`, `guardian_name`, `guardian_phone`, `is_complete`, `updated_at`) VALUES
(3, 'Trần Văn B', '2009-01-01', 'Nam Định', '0910000001', 'hs00001@thpt-thd.edu.vn', 'Lê Thị C', '0912345678', 1, '2026-01-08 10:05:10'),
(12, 'Vũ Hoài Phương', '2009-02-02', 'Nam Định', '0910000002', 'hs00002@thpt-thd.edu.vn', 'Hoàng Thị Hoa', '0912345679', 1, '2026-01-08 10:36:16'),
(64, 'Cô Giáo Test', '2010-01-01', 'Test Address', '0900000111', 'gvtest01@thpt-thd.edu.vn', 'Phụ huynh Test', '0911111111', 1, '2026-01-08 13:31:51'),
(75, 'Thầy Nguyễn Văn Toán', '2008-09-05', 'TP. Nam Định', '0900001002', 'gv.toan@thpt-thd.edu.vn', 'Phụ huynh HS', '0912345678', 1, '2026-01-08 13:42:24'),
(76, 'Cô Lê Thị Văn', '2008-09-05', 'TP. Nam Định', '0900001003', 'gv.van@thpt-thd.edu.vn', 'Phụ huynh HS', '0912345678', 1, '2026-01-08 13:42:24'),
(77, 'Thầy Trần Văn Lý', '2008-09-05', 'TP. Nam Định', '0900001004', 'gv.ly@thpt-thd.edu.vn', 'Phụ huynh HS', '0912345678', 1, '2026-01-08 13:42:24'),
(78, 'Cô Phạm Thị Hóa', '2008-09-05', 'TP. Nam Định', '0900001005', 'gv.hoa@thpt-thd.edu.vn', 'Phụ huynh HS', '0912345678', 1, '2026-01-08 13:42:24'),
(79, 'Thầy Hoàng Văn Thể', '2008-09-05', 'TP. Nam Định', '0900001006', 'gv.theduc@thpt-thd.edu.vn', 'Phụ huynh HS', '0912345678', 1, '2026-01-08 13:42:24'),
(80, 'Nguyễn Văn An', '2008-09-05', 'TP. Nam Định', '0900002002', 'hs.an@thpt-thd.edu.vn', 'Phụ huynh HS', '0912345678', 1, '2026-01-08 13:42:24'),
(81, 'Trần Thị Bình', '2008-09-05', 'TP. Nam Định', '0900002003', 'hs.binh@thpt-thd.edu.vn', 'Phụ huynh HS', '0912345678', 1, '2026-01-08 13:42:24'),
(82, 'Lê Văn Cường', '2008-09-05', 'TP. Nam Định', '0900002004', 'hs.cuong@thpt-thd.edu.vn', 'Phụ huynh HS', '0912345678', 1, '2026-01-08 13:42:24'),
(83, 'Phạm Thị Dung', '2008-09-05', 'TP. Nam Định', '0900002005', 'hs.dung@thpt-thd.edu.vn', 'Phụ huynh HS', '0912345678', 1, '2026-01-08 13:42:24'),
(84, 'Hoàng Văn Em', '2008-09-05', 'TP. Nam Định', '0900002006', 'hs.em@thpt-thd.edu.vn', 'Phụ huynh HS', '0912345678', 1, '2026-01-08 13:42:24'),
(85, 'Đỗ Thị Phương', '2008-09-05', 'TP. Nam Định', '0900002007', 'hs.phuong@thpt-thd.edu.vn', 'Phụ huynh HS', '0912345678', 1, '2026-01-08 13:42:24'),
(86, 'Ngô Văn Giang', '2008-09-05', 'TP. Nam Định', '0900002008', 'hs.giang@thpt-thd.edu.vn', 'Phụ huynh HS', '0912345678', 1, '2026-01-08 13:42:24'),
(87, 'Bùi Thị Hoa', '2008-09-05', 'TP. Nam Định', '0900002009', 'hs.hoa@thpt-thd.edu.vn', 'Phụ huynh HS', '0912345678', 1, '2026-01-08 13:42:24'),
(88, 'Vũ Văn Hùng', '2008-09-05', 'TP. Nam Định', '0900002010', 'hs.hung@thpt-thd.edu.vn', 'Phụ huynh HS', '0912345678', 1, '2026-01-08 13:42:24'),
(89, 'Lý Thị Lan', '2008-09-05', 'TP. Nam Định', '0900002011', 'hs.lan@thpt-thd.edu.vn', 'Phụ huynh HS', '0912345678', 1, '2026-01-08 13:42:24'),
(90, 'Bố Của An', '2008-09-05', 'TP. Nam Định', '0900003001', 'ph.an@thpt-thd.edu.vn', 'Phụ huynh HS', '0912345678', 1, '2026-01-08 13:42:24'),
(91, 'Mẹ Của Bình', '2008-09-05', 'TP. Nam Định', '0900003002', 'ph.binh@thpt-thd.edu.vn', 'Phụ huynh HS', '0912345678', 1, '2026-01-08 13:42:24'),
(92, 'Bố Của Cường', '2008-09-05', 'TP. Nam Định', '0900003003', 'ph.cuong@thpt-thd.edu.vn', 'Phụ huynh HS', '0912345678', 1, '2026-01-08 13:42:24'),
(93, 'Học Sinh Lỗi', '2008-09-05', 'TP. Nam Định', '0900004001', 'error-mail', 'Phụ huynh HS', '0912345678', 1, '2026-01-08 13:42:24'),
(120, 'Học Sinh 10A1 Số 10', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(121, 'Học Sinh 10A1 Số 11', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(122, 'Học Sinh 10A1 Số 12', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(123, 'Học Sinh 10A1 Số 13', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(124, 'Học Sinh 10A1 Số 14', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(125, 'Học Sinh 10A1 Số 15', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(126, 'Học Sinh 10A1 Số 16', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(127, 'Học Sinh 10A1 Số 17', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(128, 'Học Sinh 10A1 Số 18', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(129, 'Học Sinh 10A1 Số 19', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(130, 'Học Sinh 10A1 Số 20', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(131, 'Học Sinh 10A2 Số 1', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(132, 'Học Sinh 10A2 Số 2', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(133, 'Học Sinh 10A2 Số 3', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(134, 'Học Sinh 10A2 Số 4', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(135, 'Học Sinh 10A2 Số 5', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(136, 'Học Sinh 10A2 Số 6', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(137, 'Học Sinh 10A2 Số 7', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(138, 'Học Sinh 10A2 Số 8', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(139, 'Học Sinh 10A2 Số 9', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(140, 'Học Sinh 10A2 Số 10', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(141, 'Học Sinh 10A2 Số 11', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(142, 'Học Sinh 10A2 Số 12', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(143, 'Học Sinh 10A2 Số 13', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(144, 'Học Sinh 10A2 Số 14', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(145, 'Học Sinh 10A2 Số 15', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(146, 'Học Sinh 10A2 Số 16', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(147, 'Học Sinh 10A2 Số 17', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(148, 'Học Sinh 10A2 Số 18', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(149, 'Học Sinh 10A2 Số 19', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(150, 'Học Sinh 10A2 Số 20', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(151, 'Học Sinh 10A3 Số 1', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(152, 'Học Sinh 10A3 Số 2', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(153, 'Học Sinh 10A3 Số 3', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(154, 'Học Sinh 10A3 Số 4', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(155, 'Học Sinh 10A3 Số 5', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(156, 'Học Sinh 10A3 Số 6', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(157, 'Học Sinh 10A3 Số 7', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(158, 'Học Sinh 10A3 Số 8', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(159, 'Học Sinh 10A3 Số 9', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(160, 'Học Sinh 10A3 Số 10', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(161, 'Học Sinh 10A3 Số 11', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(162, 'Học Sinh 10A3 Số 12', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(163, 'Học Sinh 10A3 Số 13', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(164, 'Học Sinh 10A3 Số 14', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(165, 'Học Sinh 10A3 Số 15', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(166, 'Học Sinh 10A3 Số 16', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(167, 'Học Sinh 10A3 Số 17', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(168, 'Học Sinh 10A3 Số 18', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(169, 'Học Sinh 10A3 Số 19', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(170, 'Học Sinh 10A3 Số 20', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(171, 'Học Sinh 11A1 Số 1', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(172, 'Học Sinh 11A1 Số 2', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(173, 'Học Sinh 11A1 Số 3', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(174, 'Học Sinh 11A1 Số 4', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(175, 'Học Sinh 11A1 Số 5', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(176, 'Học Sinh 11A1 Số 6', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(177, 'Học Sinh 11A1 Số 7', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(178, 'Học Sinh 11A1 Số 8', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(179, 'Học Sinh 11A1 Số 9', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(180, 'Học Sinh 11A1 Số 10', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(181, 'Học Sinh 11A1 Số 11', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(182, 'Học Sinh 11A1 Số 12', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(183, 'Học Sinh 11A1 Số 13', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(184, 'Học Sinh 11A1 Số 14', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(185, 'Học Sinh 11A1 Số 15', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(186, 'Học Sinh 11A1 Số 16', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(187, 'Học Sinh 11A1 Số 17', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(188, 'Học Sinh 11A1 Số 18', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(189, 'Học Sinh 11A1 Số 19', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(190, 'Học Sinh 11A1 Số 20', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(191, 'Học Sinh 11A2 Số 1', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(192, 'Học Sinh 11A2 Số 2', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(193, 'Học Sinh 11A2 Số 3', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(194, 'Học Sinh 11A2 Số 4', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(195, 'Học Sinh 11A2 Số 5', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(196, 'Học Sinh 11A2 Số 6', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(197, 'Học Sinh 11A2 Số 7', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(198, 'Học Sinh 11A2 Số 8', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(199, 'Học Sinh 11A2 Số 9', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(200, 'Học Sinh 11A2 Số 10', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(201, 'Học Sinh 11A2 Số 11', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(202, 'Học Sinh 11A2 Số 12', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(203, 'Học Sinh 11A2 Số 13', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(204, 'Học Sinh 11A2 Số 14', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(205, 'Học Sinh 11A2 Số 15', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(206, 'Học Sinh 11A2 Số 16', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(207, 'Học Sinh 11A2 Số 17', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(208, 'Học Sinh 11A2 Số 18', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(209, 'Học Sinh 11A2 Số 19', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(210, 'Học Sinh 11A2 Số 20', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(211, 'Học Sinh 12A1 Số 1', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(212, 'Học Sinh 12A1 Số 2', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(213, 'Học Sinh 12A1 Số 3', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(214, 'Học Sinh 12A1 Số 4', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(215, 'Học Sinh 12A1 Số 5', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(216, 'Học Sinh 12A1 Số 6', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(217, 'Học Sinh 12A1 Số 7', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(218, 'Học Sinh 12A1 Số 8', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(219, 'Học Sinh 12A1 Số 9', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(220, 'Học Sinh 12A1 Số 10', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(221, 'Học Sinh 12A1 Số 11', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(222, 'Học Sinh 12A1 Số 12', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(223, 'Học Sinh 12A1 Số 13', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(224, 'Học Sinh 12A1 Số 14', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(225, 'Học Sinh 12A1 Số 15', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(226, 'Học Sinh 12A1 Số 16', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(227, 'Học Sinh 12A1 Số 17', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:19'),
(228, 'Học Sinh 12A1 Số 18', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:20'),
(229, 'Học Sinh 12A1 Số 19', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:20'),
(230, 'Học Sinh 12A1 Số 20', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:20'),
(231, 'Học Sinh KL-01 Số 1', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:20'),
(232, 'Học Sinh KL-01 Số 2', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:20'),
(233, 'Học Sinh KL-01 Số 3', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:20'),
(234, 'Học Sinh KL-01 Số 4', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:20'),
(235, 'Học Sinh KL-01 Số 5', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:20'),
(236, 'Học Sinh KL-01 Số 6', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:20'),
(237, 'Học Sinh KL-01 Số 7', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:20'),
(238, 'Học Sinh KL-01 Số 8', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:20'),
(239, 'Học Sinh KL-01 Số 9', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:20'),
(240, 'Học Sinh KL-01 Số 10', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:20'),
(241, 'Học Sinh KL-01 Số 11', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:20'),
(242, 'Học Sinh KL-01 Số 12', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:20'),
(243, 'Học Sinh KL-01 Số 13', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:20'),
(244, 'Học Sinh KL-01 Số 14', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:20'),
(245, 'Học Sinh KL-01 Số 15', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:20'),
(246, 'Học Sinh KL-01 Số 16', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:20'),
(247, 'Học Sinh KL-01 Số 17', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:20'),
(248, 'Học Sinh KL-01 Số 18', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:20'),
(249, 'Học Sinh KL-01 Số 19', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:20'),
(250, 'Học Sinh KL-01 Số 20', '2008-01-01', 'Nam Định', NULL, NULL, NULL, NULL, 1, '2026-01-08 13:51:20');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `name`) VALUES
(1, 'Toán'),
(2, 'Vật lý'),
(3, 'Ngữ văn'),
(4, 'Toán'),
(5, 'Vật lý'),
(6, 'Ngữ văn'),
(7, 'Hóa học'),
(8, 'Sinh học'),
(9, 'Lịch sử'),
(10, 'Địa lý'),
(11, 'Tin học'),
(12, 'Toán'),
(13, 'Vật lý'),
(14, 'Ngữ văn'),
(15, 'Hóa học'),
(16, 'Sinh học'),
(17, 'Lịch sử'),
(18, 'Địa lý'),
(19, 'Tin học'),
(26, 'Địa lý'),
(27, 'Tin học'),
(28, 'Toán'),
(29, 'Vật lý'),
(30, 'Ngữ văn'),
(31, 'Hóa học'),
(32, 'Sinh học'),
(33, 'Lịch sử'),
(34, 'Địa lý'),
(35, 'Tin học'),
(36, 'Toán'),
(37, 'Vật lý'),
(38, 'Ngữ văn'),
(39, 'Hóa học'),
(40, 'Sinh học'),
(41, 'Lịch sử'),
(42, 'Địa lý'),
(43, 'Tin học'),
(44, 'Toán'),
(45, 'Vật lý'),
(46, 'Ngữ văn'),
(47, 'Hóa học'),
(48, 'Sinh học'),
(49, 'Lịch sử'),
(50, 'Địa lý'),
(51, 'Tin học'),
(52, 'Toán'),
(53, 'Vật lý'),
(54, 'Ngữ văn'),
(55, 'Hóa học'),
(56, 'Sinh học'),
(57, 'Lịch sử'),
(58, 'Địa lý'),
(59, 'Tin học'),
(60, 'Toán'),
(61, 'Vật lý'),
(62, 'Hóa học'),
(63, 'Sinh học'),
(64, 'Ngữ văn'),
(65, 'Lịch sử'),
(66, 'Địa lý'),
(67, 'Tin học'),
(68, 'Tiếng Anh'),
(69, 'GDCD'),
(70, 'Công nghệ'),
(71, 'Thể dục');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `setting_key` varchar(100) NOT NULL,
  `setting_value` varchar(255) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `updated_at`) VALUES
('bg_mobile', 'https://placehold.co/1080x1920.png?text=bg_mobile', '2026-01-08 10:36:16'),
('bg_pc', 'https://placehold.co/1920x1080.png?text=bg_pc', '2026-01-08 10:36:16'),
('bg_sub1', 'https://placehold.co/800x400.png?text=sub1', '2026-01-08 10:36:16'),
('bg_sub2', 'https://placehold.co/800x400.png?text=sub2', '2026-01-08 10:36:16'),
('bg_sub3', 'https://placehold.co/800x400.png?text=sub3', '2026-01-08 10:36:16'),
('discipline_class_name', 'KL-01', '2026-01-08 10:05:11'),
('discipline_threshold_class_change', '0', '2026-01-08 10:05:11'),
('discipline_threshold_conduct', '50', '2026-01-08 10:05:11'),
('discipline_threshold_warn', '20', '2026-01-08 10:05:11');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_class_requests`
--

CREATE TABLE `teacher_class_requests` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approved_at` timestamp NULL DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_class_requests`
--

INSERT INTO `teacher_class_requests` (`id`, `teacher_id`, `class_id`, `status`, `requested_at`, `approved_at`, `admin_id`) VALUES
(1, 2, 1, 'approved', '2026-01-08 10:05:11', NULL, NULL),
(2, 2, 1, 'approved', '2026-01-08 10:36:16', NULL, NULL),
(3, 2, 1, 'approved', '2026-01-08 10:38:54', NULL, NULL),
(4, 2, 1, 'approved', '2026-01-08 12:49:58', NULL, NULL),
(5, 2, 1, 'approved', '2026-01-08 12:50:36', NULL, NULL),
(6, 2, 1, 'approved', '2026-01-08 12:51:35', NULL, NULL),
(7, 64, 7, 'pending', '2026-01-09 18:22:07', NULL, NULL),
(8, 64, 40, 'approved', '2026-01-09 18:22:08', '2026-01-09 18:23:05', 1),
(9, 10, 7, 'pending', '2026-01-10 18:18:52', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `teacher_subjects`
--

CREATE TABLE `teacher_subjects` (
  `teacher_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_subjects`
--

INSERT INTO `teacher_subjects` (`teacher_id`, `subject_id`) VALUES
(2, 1),
(2, 2),
(2, 3),
(79, 71);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('admin','teacher','student','parent') NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `phone_verified` tinyint(1) DEFAULT 0,
  `is_locked` tinyint(1) DEFAULT 0,
  `password_must_change` tinyint(1) DEFAULT 0,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`, `avatar`, `email`, `phone`, `phone_verified`, `is_locked`, `password_must_change`, `last_login`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Quản trị', 'admin', NULL, NULL, NULL, 0, 0, 0, '2026-01-10 21:21:02', '2026-01-08 10:05:10'),
(2, 'GV-00001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Văn A', 'teacher', 'avatar_2_1768066915.jpg', 'gv00001@thpt-thd.edu.vn', '0900000001', 1, 0, 0, '2026-01-11 11:17:15', '2026-01-08 10:05:10'),
(3, 'HS-00001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Văn B', 'student', NULL, 'hs00001@thpt-thd.edu.vn', '0910000001', 0, 0, 1, NULL, '2026-01-08 10:05:10'),
(4, 'PH-00001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lê Thị C', 'parent', NULL, NULL, '0912345678', 1, 0, 0, '2026-01-10 18:16:50', '2026-01-08 10:05:10'),
(9, 'admin2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Quản trị 2', 'admin', NULL, NULL, NULL, 0, 0, 0, NULL, '2026-01-08 10:36:16'),
(10, 'GV-00002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Quang Huy', 'teacher', NULL, 'gv00002@thpt-thd.edu.vn', '0900000002', 1, 0, 0, '2026-01-10 18:20:04', '2026-01-08 10:36:16'),
(11, 'GV-00003', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Phạm Thị Thu', 'teacher', NULL, 'gv00003@thpt-thd.edu.vn', '0900000003', 1, 0, 0, NULL, '2026-01-08 10:36:16'),
(12, 'HS-00002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Vũ Hoài Phương', 'student', NULL, 'hs00002@thpt-thd.edu.vn', '0910000002', 0, 0, 1, NULL, '2026-01-08 10:36:16'),
(13, 'PH-00002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hoàng Thị Hoa', 'parent', NULL, NULL, '0912345679', 1, 0, 0, NULL, '2026-01-08 10:36:16'),
(64, 'GV-TEST-01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Cô Giáo Test', 'teacher', 'avatar_64_1767982934.jpg', 'gvtest01@thpt-thd.edu.vn', '0900000111', 1, 0, 0, '2026-01-09 18:19:39', '2026-01-08 13:31:51'),
(69, 'HS-TEST-01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Em Học Sinh Test', 'student', NULL, 'hstest01@thpt-thd.edu.vn', '0900000222', 1, 0, 0, NULL, '2026-01-08 13:42:24'),
(71, 'HS-UNVERIFIED', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh Chưa Verify', 'student', NULL, 'hsunverified@thpt-thd.edu.vn', '0900000333', 0, 0, 0, NULL, '2026-01-08 13:42:24'),
(72, 'HS-LOCKED', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh Bị Khóa', 'student', NULL, 'hslocked@thpt-thd.edu.vn', '0900000444', 1, 1, 0, NULL, '2026-01-08 13:42:24'),
(73, 'HS-NEW-PASS', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh Mới', 'student', NULL, 'hsnewpass@thpt-thd.edu.vn', '0900000555', 0, 0, 1, NULL, '2026-01-08 13:42:24'),
(74, 'ADMIN-ND', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin Trần Hưng Đạo', 'admin', NULL, 'admin@thpt-thd.edu.vn', '0900000999', 1, 0, 0, NULL, '2026-01-08 13:42:24'),
(75, 'GV-ND-02', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Thầy Nguyễn Văn Toán', 'teacher', NULL, 'gv.toan@thpt-thd.edu.vn', '0900001002', 1, 0, 0, NULL, '2026-01-08 13:42:24'),
(76, 'GV-ND-03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Cô Lê Thị Văn', 'teacher', NULL, 'gv.van@thpt-thd.edu.vn', '0900001003', 1, 0, 0, NULL, '2026-01-08 13:42:24'),
(77, 'GV-ND-04', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Thầy Trần Văn Lý', 'teacher', NULL, 'gv.ly@thpt-thd.edu.vn', '0900001004', 1, 0, 0, NULL, '2026-01-08 13:42:24'),
(78, 'GV-ND-05', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Cô Phạm Thị Hóa', 'teacher', NULL, 'gv.hoa@thpt-thd.edu.vn', '0900001005', 1, 0, 0, NULL, '2026-01-08 13:42:24'),
(79, 'GV-ND-06', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Thầy Hoàng Văn Thể', 'teacher', NULL, 'gv.theduc@thpt-thd.edu.vn', '0900001006', 1, 0, 0, NULL, '2026-01-08 13:42:24'),
(80, 'HS-ND-02', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Văn An', 'student', NULL, 'hs.an@thpt-thd.edu.vn', '0900002002', 1, 0, 0, NULL, '2026-01-08 13:42:24'),
(81, 'HS-ND-03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Thị Bình', 'student', NULL, 'hs.binh@thpt-thd.edu.vn', '0900002003', 1, 0, 0, NULL, '2026-01-08 13:42:24'),
(82, 'HS-ND-04', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lê Văn Cường', 'student', NULL, 'hs.cuong@thpt-thd.edu.vn', '0900002004', 1, 0, 0, NULL, '2026-01-08 13:42:24'),
(83, 'HS-ND-05', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Phạm Thị Dung', 'student', NULL, 'hs.dung@thpt-thd.edu.vn', '0900002005', 1, 0, 0, NULL, '2026-01-08 13:42:24'),
(84, 'HS-ND-06', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hoàng Văn Em', 'student', NULL, 'hs.em@thpt-thd.edu.vn', '0900002006', 1, 0, 0, NULL, '2026-01-08 13:42:24'),
(85, 'HS-ND-07', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Đỗ Thị Phương', 'student', NULL, 'hs.phuong@thpt-thd.edu.vn', '0900002007', 1, 0, 0, NULL, '2026-01-08 13:42:24'),
(86, 'HS-ND-08', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ngô Văn Giang', 'student', NULL, 'hs.giang@thpt-thd.edu.vn', '0900002008', 1, 0, 0, '2026-01-10 18:57:34', '2026-01-08 13:42:24'),
(87, 'HS-ND-09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bùi Thị Hoa', 'student', NULL, 'hs.hoa@thpt-thd.edu.vn', '0900002009', 1, 0, 0, NULL, '2026-01-08 13:42:24'),
(88, 'HS-ND-10', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Vũ Văn Hùng', 'student', NULL, 'hs.hung@thpt-thd.edu.vn', '0900002010', 1, 0, 0, NULL, '2026-01-08 13:42:24'),
(89, 'HS-ND-11', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lý Thị Lan', 'student', NULL, 'hs.lan@thpt-thd.edu.vn', '0900002011', 1, 0, 0, NULL, '2026-01-08 13:42:24'),
(90, 'PH-ND-01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bố Của An', 'parent', NULL, 'ph.an@thpt-thd.edu.vn', '0900003001', 1, 0, 0, NULL, '2026-01-08 13:42:24'),
(91, 'PH-ND-02', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mẹ Của Bình', 'parent', NULL, 'ph.binh@thpt-thd.edu.vn', '0900003002', 1, 0, 0, NULL, '2026-01-08 13:42:24'),
(92, 'PH-ND-03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bố Của Cường', 'parent', NULL, 'ph.cuong@thpt-thd.edu.vn', '0900003003', 1, 0, 0, NULL, '2026-01-08 13:42:24'),
(93, 'HS-ND-ERR', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh Lỗi', 'student', NULL, 'error-mail', '0900004001', 1, 0, 0, NULL, '2026-01-08 13:42:24'),
(119, 'CO-DO-01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Đội Cờ Đỏ Lớp 10A2', 'student', NULL, NULL, NULL, 0, 0, 0, NULL, '2026-01-08 13:48:35'),
(120, 'HS-10A1-10', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A1 Số 10', 'student', NULL, 'pham.cuong@thpt-thd.edu.vn', '', 1, 0, 0, '2026-01-10 18:56:17', '2026-01-08 13:51:19'),
(121, 'HS-10A1-11', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A1 Số 11', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(122, 'HS-10A1-12', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A1 Số 12', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(123, 'HS-10A1-13', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A1 Số 13', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(124, 'HS-10A1-14', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A1 Số 14', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(125, 'HS-10A1-15', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A1 Số 15', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(126, 'HS-10A1-16', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A1 Số 16', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(127, 'HS-10A1-17', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A1 Số 17', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(128, 'HS-10A1-18', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A1 Số 18', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(129, 'HS-10A1-19', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A1 Số 19', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(130, 'HS-10A1-20', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A1 Số 20', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(131, 'HS-10A2-01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A2 Số 1', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(132, 'HS-10A2-02', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A2 Số 2', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(133, 'HS-10A2-03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A2 Số 3', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(134, 'HS-10A2-04', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A2 Số 4', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(135, 'HS-10A2-05', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A2 Số 5', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(136, 'HS-10A2-06', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A2 Số 6', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(137, 'HS-10A2-07', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A2 Số 7', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(138, 'HS-10A2-08', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A2 Số 8', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(139, 'HS-10A2-09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A2 Số 9', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(140, 'HS-10A2-10', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A2 Số 10', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(141, 'HS-10A2-11', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A2 Số 11', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(142, 'HS-10A2-12', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A2 Số 12', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(143, 'HS-10A2-13', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A2 Số 13', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(144, 'HS-10A2-14', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A2 Số 14', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(145, 'HS-10A2-15', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A2 Số 15', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(146, 'HS-10A2-16', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A2 Số 16', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(147, 'HS-10A2-17', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A2 Số 17', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(148, 'HS-10A2-18', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A2 Số 18', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(149, 'HS-10A2-19', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A2 Số 19', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(150, 'HS-10A2-20', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A2 Số 20', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(151, 'HS-10A3-01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A3 Số 1', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(152, 'HS-10A3-02', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A3 Số 2', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(153, 'HS-10A3-03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A3 Số 3', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(154, 'HS-10A3-04', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A3 Số 4', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(155, 'HS-10A3-05', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A3 Số 5', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(156, 'HS-10A3-06', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A3 Số 6', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(157, 'HS-10A3-07', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A3 Số 7', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(158, 'HS-10A3-08', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A3 Số 8', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(159, 'HS-10A3-09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A3 Số 9', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(160, 'HS-10A3-10', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A3 Số 10', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(161, 'HS-10A3-11', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A3 Số 11', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(162, 'HS-10A3-12', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A3 Số 12', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(163, 'HS-10A3-13', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A3 Số 13', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(164, 'HS-10A3-14', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A3 Số 14', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(165, 'HS-10A3-15', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A3 Số 15', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(166, 'HS-10A3-16', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A3 Số 16', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(167, 'HS-10A3-17', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A3 Số 17', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(168, 'HS-10A3-18', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A3 Số 18', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(169, 'HS-10A3-19', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A3 Số 19', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(170, 'HS-10A3-20', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 10A3 Số 20', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(171, 'HS-11A1-01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A1 Số 1', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(172, 'HS-11A1-02', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A1 Số 2', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(173, 'HS-11A1-03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A1 Số 3', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(174, 'HS-11A1-04', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A1 Số 4', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(175, 'HS-11A1-05', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A1 Số 5', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(176, 'HS-11A1-06', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A1 Số 6', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(177, 'HS-11A1-07', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A1 Số 7', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(178, 'HS-11A1-08', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A1 Số 8', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(179, 'HS-11A1-09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A1 Số 9', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(180, 'HS-11A1-10', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A1 Số 10', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(181, 'HS-11A1-11', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A1 Số 11', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(182, 'HS-11A1-12', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A1 Số 12', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(183, 'HS-11A1-13', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A1 Số 13', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(184, 'HS-11A1-14', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A1 Số 14', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(185, 'HS-11A1-15', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A1 Số 15', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(186, 'HS-11A1-16', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A1 Số 16', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(187, 'HS-11A1-17', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A1 Số 17', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(188, 'HS-11A1-18', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A1 Số 18', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(189, 'HS-11A1-19', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A1 Số 19', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(190, 'HS-11A1-20', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A1 Số 20', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(191, 'HS-11A2-01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A2 Số 1', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(192, 'HS-11A2-02', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A2 Số 2', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(193, 'HS-11A2-03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A2 Số 3', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(194, 'HS-11A2-04', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A2 Số 4', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(195, 'HS-11A2-05', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A2 Số 5', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(196, 'HS-11A2-06', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A2 Số 6', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(197, 'HS-11A2-07', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A2 Số 7', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(198, 'HS-11A2-08', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A2 Số 8', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(199, 'HS-11A2-09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A2 Số 9', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(200, 'HS-11A2-10', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A2 Số 10', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(201, 'HS-11A2-11', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A2 Số 11', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(202, 'HS-11A2-12', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A2 Số 12', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(203, 'HS-11A2-13', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A2 Số 13', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(204, 'HS-11A2-14', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A2 Số 14', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(205, 'HS-11A2-15', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A2 Số 15', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(206, 'HS-11A2-16', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A2 Số 16', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(207, 'HS-11A2-17', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A2 Số 17', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(208, 'HS-11A2-18', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A2 Số 18', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(209, 'HS-11A2-19', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A2 Số 19', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(210, 'HS-11A2-20', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 11A2 Số 20', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(211, 'HS-12A1-01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 12A1 Số 1', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(212, 'HS-12A1-02', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 12A1 Số 2', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(213, 'HS-12A1-03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 12A1 Số 3', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(214, 'HS-12A1-04', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 12A1 Số 4', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(215, 'HS-12A1-05', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 12A1 Số 5', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(216, 'HS-12A1-06', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 12A1 Số 6', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(217, 'HS-12A1-07', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 12A1 Số 7', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(218, 'HS-12A1-08', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 12A1 Số 8', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(219, 'HS-12A1-09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 12A1 Số 9', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(220, 'HS-12A1-10', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 12A1 Số 10', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(221, 'HS-12A1-11', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 12A1 Số 11', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(222, 'HS-12A1-12', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 12A1 Số 12', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(223, 'HS-12A1-13', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 12A1 Số 13', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(224, 'HS-12A1-14', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 12A1 Số 14', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(225, 'HS-12A1-15', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 12A1 Số 15', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(226, 'HS-12A1-16', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 12A1 Số 16', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(227, 'HS-12A1-17', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 12A1 Số 17', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(228, 'HS-12A1-18', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 12A1 Số 18', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:19'),
(229, 'HS-12A1-19', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 12A1 Số 19', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:20'),
(230, 'HS-12A1-20', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh 12A1 Số 20', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:20'),
(231, 'HS-KL-01-01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh KL-01 Số 1', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:20'),
(232, 'HS-KL-01-02', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh KL-01 Số 2', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:20'),
(233, 'HS-KL-01-03', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh KL-01 Số 3', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:20'),
(234, 'HS-KL-01-04', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh KL-01 Số 4', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:20'),
(235, 'HS-KL-01-05', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh KL-01 Số 5', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:20'),
(236, 'HS-KL-01-06', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh KL-01 Số 6', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:20'),
(237, 'HS-KL-01-07', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh KL-01 Số 7', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:20'),
(238, 'HS-KL-01-08', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh KL-01 Số 8', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:20'),
(239, 'HS-KL-01-09', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh KL-01 Số 9', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:20'),
(240, 'HS-KL-01-10', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh KL-01 Số 10', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:20'),
(241, 'HS-KL-01-11', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh KL-01 Số 11', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:20'),
(242, 'HS-KL-01-12', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh KL-01 Số 12', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:20'),
(243, 'HS-KL-01-13', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh KL-01 Số 13', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:20'),
(244, 'HS-KL-01-14', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh KL-01 Số 14', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:20'),
(245, 'HS-KL-01-15', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh KL-01 Số 15', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:20'),
(246, 'HS-KL-01-16', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh KL-01 Số 16', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:20'),
(247, 'HS-KL-01-17', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh KL-01 Số 17', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:20'),
(248, 'HS-KL-01-18', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh KL-01 Số 18', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:20'),
(249, 'HS-KL-01-19', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Học Sinh KL-01 Số 19', 'student', NULL, NULL, NULL, 1, 0, 0, NULL, '2026-01-08 13:51:20'),
(250, 'HS-KL-01-20', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'nguyen van han', 'student', NULL, '', '', 1, 0, 0, NULL, '2026-01-08 13:51:20');

-- --------------------------------------------------------

--
-- Table structure for table `violations`
--

CREATE TABLE `violations` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `rule_id` int(11) NOT NULL,
  `reporter_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `note` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `violations`
--

INSERT INTO `violations` (`id`, `student_id`, `rule_id`, `reporter_id`, `created_at`, `note`, `image_url`) VALUES
(1, 3, 1, 2, '2026-01-08 10:05:11', 'Đến muộn 10 phút', NULL),
(2, 3, 1, 2, '2026-01-08 10:36:16', 'Đến muộn 10 phút', NULL),
(3, 3, 1, 2, '2026-01-08 10:38:54', 'Đến muộn 10 phút', NULL),
(4, 3, 1, 1, '2026-01-08 12:45:07', 'sai', NULL),
(5, 3, 1, 2, '2026-01-08 12:49:58', 'Đến muộn 10 phút', NULL),
(6, 3, 1, 2, '2026-01-08 12:50:36', 'Đến muộn 10 phút', NULL),
(7, 3, 1, 2, '2026-01-08 12:51:35', 'Đến muộn 10 phút', NULL),
(8, 80, 1, 75, '2026-01-08 13:48:34', 'Đến muộn 15 phút', NULL),
(9, 80, 2, 75, '2026-01-07 13:48:34', 'Quên thẻ học sinh', NULL),
(10, 81, 27, 76, '2026-01-08 13:48:34', 'Nói chuyện trong giờ Văn', NULL),
(11, 165, 1, 1, '2026-01-09 23:11:45', '', NULL),
(12, 123, 1, 2, '2026-01-11 11:49:12', '', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_attendance` (`student_id`,`date`),
  ADD KEY `fk_attendance_class` (`class_id`),
  ADD KEY `fk_attendance_recorder` (`recorded_by`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_audit_logs_user` (`user_id`);

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `banner_logs`
--
ALTER TABLE `banner_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `behavior_reports`
--
ALTER TABLE `behavior_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_behavior_reports_student` (`student_id`),
  ADD KEY `fk_behavior_reports_creator` (`created_by`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_classes_code` (`code`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `fk_classes_homeroom` (`homeroom_teacher_id`);

--
-- Indexes for table `class_registrations`
--
ALTER TABLE `class_registrations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_class_reg_student` (`student_id`),
  ADD KEY `fk_class_reg_class` (`class_id`);

--
-- Indexes for table `class_teacher_assignments`
--
ALTER TABLE `class_teacher_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_assignment` (`class_id`,`teacher_id`),
  ADD KEY `fk_cta_teacher` (`teacher_id`);

--
-- Indexes for table `conduct`
--
ALTER TABLE `conduct`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_conduct_student` (`student_id`);

--
-- Indexes for table `conduct_results`
--
ALTER TABLE `conduct_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_conduct_results_student` (`student_id`),
  ADD KEY `fk_conduct_results_teacher` (`teacher_id`),
  ADD KEY `fk_conduct_results_class` (`class_id`);

--
-- Indexes for table `conduct_rules`
--
ALTER TABLE `conduct_rules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `discipline_points`
--
ALTER TABLE `discipline_points`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_messages_sender` (`sender_id`),
  ADD KEY `fk_messages_receiver` (`receiver_id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_news_author` (`author_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_notifications_sender` (`sender_id`),
  ADD KEY `fk_notifications_receiver` (`receiver_id`);

--
-- Indexes for table `otp_codes`
--
ALTER TABLE `otp_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `parent_student_links`
--
ALTER TABLE `parent_student_links`
  ADD PRIMARY KEY (`parent_id`,`student_id`),
  ADD KEY `fk_psl_student` (`student_id`);

--
-- Indexes for table `red_committee_members`
--
ALTER TABLE `red_committee_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `assigned_by` (`assigned_by`);

--
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_schedule_class` (`class_id`),
  ADD KEY `fk_schedule_subject` (`subject_id`),
  ADD KEY `fk_schedule_teacher` (`teacher_id`);

--
-- Indexes for table `scores`
--
ALTER TABLE `scores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_scores_student` (`student_id`),
  ADD KEY `fk_scores_subject` (`subject_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_students_code` (`student_code`);

--
-- Indexes for table `student_details`
--
ALTER TABLE `student_details`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `fk_student_details_class` (`class_id`);

--
-- Indexes for table `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `teacher_class_requests`
--
ALTER TABLE `teacher_class_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tcr_teacher` (`teacher_id`),
  ADD KEY `fk_tcr_class` (`class_id`),
  ADD KEY `fk_tcr_admin` (`admin_id`);

--
-- Indexes for table `teacher_subjects`
--
ALTER TABLE `teacher_subjects`
  ADD PRIMARY KEY (`teacher_id`,`subject_id`),
  ADD KEY `fk_teacher_subjects_subject` (`subject_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_username` (`username`);

--
-- Indexes for table `violations`
--
ALTER TABLE `violations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_violations_student` (`student_id`),
  ADD KEY `fk_violations_rule` (`rule_id`),
  ADD KEY `fk_violations_reporter` (`reporter_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `banner_logs`
--
ALTER TABLE `banner_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `behavior_reports`
--
ALTER TABLE `behavior_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `class_registrations`
--
ALTER TABLE `class_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=148;

--
-- AUTO_INCREMENT for table `class_teacher_assignments`
--
ALTER TABLE `class_teacher_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `conduct`
--
ALTER TABLE `conduct`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `conduct_results`
--
ALTER TABLE `conduct_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `conduct_rules`
--
ALTER TABLE `conduct_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `otp_codes`
--
ALTER TABLE `otp_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `red_committee_members`
--
ALTER TABLE `red_committee_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `schedule`
--
ALTER TABLE `schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `scores`
--
ALTER TABLE `scores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `teacher_class_requests`
--
ALTER TABLE `teacher_class_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=251;

--
-- AUTO_INCREMENT for table `violations`
--
ALTER TABLE `violations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `fk_attendance_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `fk_attendance_recorder` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_attendance_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_audit_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `behavior_reports`
--
ALTER TABLE `behavior_reports`
  ADD CONSTRAINT `fk_behavior_reports_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_behavior_reports_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `fk_classes_homeroom` FOREIGN KEY (`homeroom_teacher_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `class_registrations`
--
ALTER TABLE `class_registrations`
  ADD CONSTRAINT `fk_class_reg_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_class_reg_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `class_teacher_assignments`
--
ALTER TABLE `class_teacher_assignments`
  ADD CONSTRAINT `fk_cta_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cta_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `conduct`
--
ALTER TABLE `conduct`
  ADD CONSTRAINT `fk_conduct_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `conduct_results`
--
ALTER TABLE `conduct_results`
  ADD CONSTRAINT `fk_conduct_results_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_conduct_results_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_conduct_results_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `discipline_points`
--
ALTER TABLE `discipline_points`
  ADD CONSTRAINT `fk_discipline_points_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_messages_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_messages_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `fk_news_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_notifications_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `parent_student_links`
--
ALTER TABLE `parent_student_links`
  ADD CONSTRAINT `fk_psl_parent` FOREIGN KEY (`parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_psl_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `red_committee_members`
--
ALTER TABLE `red_committee_members`
  ADD CONSTRAINT `red_committee_members_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `red_committee_members_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `red_committee_members_ibfk_3` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `schedule`
--
ALTER TABLE `schedule`
  ADD CONSTRAINT `fk_schedule_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `fk_schedule_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `fk_schedule_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `scores`
--
ALTER TABLE `scores`
  ADD CONSTRAINT `fk_scores_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_scores_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);

--
-- Constraints for table `student_details`
--
ALTER TABLE `student_details`
  ADD CONSTRAINT `fk_student_details_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `fk_student_details_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD CONSTRAINT `fk_student_profiles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `teacher_class_requests`
--
ALTER TABLE `teacher_class_requests`
  ADD CONSTRAINT `fk_tcr_admin` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tcr_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tcr_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `teacher_subjects`
--
ALTER TABLE `teacher_subjects`
  ADD CONSTRAINT `fk_teacher_subjects_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_teacher_subjects_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `violations`
--
ALTER TABLE `violations`
  ADD CONSTRAINT `fk_violations_reporter` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_violations_rule` FOREIGN KEY (`rule_id`) REFERENCES `conduct_rules` (`id`),
  ADD CONSTRAINT `fk_violations_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
