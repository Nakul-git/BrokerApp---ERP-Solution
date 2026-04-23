-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Apr 08, 2026 at 12:14 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `company`
--

-- --------------------------------------------------------

--
-- Table structure for table `company_master`
--

CREATE TABLE `company_master` (
  `company_id` int(11) NOT NULL,
  `company_name` varchar(150) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `file_name` varchar(100) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `code` varchar(11) NOT NULL,
  `ask_division` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_master`
--

INSERT INTO `company_master` (`company_id`, `company_name`, `start_date`, `end_date`, `file_name`, `is_default`, `is_active`, `created_by`, `created_at`, `code`, `ask_division`) VALUES
(1, 'ABCD', '2025-04-01', '2026-03-31', NULL, 0, 1, NULL, '2026-03-02 14:36:17', 'BRK25', 1),
(2, 'xyz', '2026-04-01', '2027-03-31', NULL, 1, 1, NULL, '2026-03-07 15:12:43', 'BRK26', 0);

-- --------------------------------------------------------

--
-- Table structure for table `division_master`
--

CREATE TABLE `division_master` (
  `division_id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `div_name` varchar(150) NOT NULL,
  `div_code` varchar(50) DEFAULT '',
  `company_name` varchar(200) DEFAULT '',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `address1` varchar(255) DEFAULT '',
  `address2` varchar(255) DEFAULT '',
  `address3` varchar(255) DEFAULT '',
  `address4` varchar(255) DEFAULT '',
  `place_name` varchar(120) DEFAULT '',
  `state_name` varchar(120) DEFAULT '',
  `pin_code` varchar(20) DEFAULT '',
  `proprietor` varchar(150) DEFAULT '',
  `pan_no` varchar(30) DEFAULT '',
  `phone_office` varchar(30) DEFAULT '',
  `mobile_no` varchar(30) DEFAULT '',
  `phone_fax` varchar(30) DEFAULT '',
  `email_id` varchar(150) DEFAULT '',
  `website` varchar(200) DEFAULT '',
  `tin_no` varchar(40) DEFAULT '',
  `tan_no` varchar(40) DEFAULT '',
  `gst_no` varchar(40) DEFAULT '',
  `bank_name` varchar(150) DEFAULT '',
  `bank1` varchar(150) DEFAULT '',
  `bank2` varchar(150) DEFAULT '',
  `bank3` varchar(150) DEFAULT '',
  `bank4` varchar(150) DEFAULT '',
  `jurisdiction` varchar(255) DEFAULT '',
  `top_line_header` varchar(255) DEFAULT '',
  `middle_line` varchar(255) DEFAULT '',
  `bottom_footer` varchar(255) DEFAULT '',
  `fixed_terms` text DEFAULT NULL,
  `sms_domain` varchar(255) DEFAULT '',
  `sms_user` varchar(120) DEFAULT '',
  `sms_password` varchar(120) DEFAULT '',
  `sms_port` varchar(30) DEFAULT '',
  `smtp_client` varchar(255) DEFAULT '',
  `email_user` varchar(150) DEFAULT '',
  `email_pwd` varchar(150) DEFAULT '',
  `email_port` varchar(30) DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `division_master`
--

INSERT INTO `division_master` (`division_id`, `company_id`, `user_id`, `div_name`, `div_code`, `company_name`, `is_active`, `is_default`, `address1`, `address2`, `address3`, `address4`, `place_name`, `state_name`, `pin_code`, `proprietor`, `pan_no`, `phone_office`, `mobile_no`, `phone_fax`, `email_id`, `website`, `tin_no`, `tan_no`, `gst_no`, `bank_name`, `bank1`, `bank2`, `bank3`, `bank4`, `jurisdiction`, `top_line_header`, `middle_line`, `bottom_footer`, `fixed_terms`, `sms_domain`, `sms_user`, `sms_password`, `sms_port`, `smtp_client`, `email_user`, `email_pwd`, `email_port`, `created_at`, `updated_at`) VALUES
(3, NULL, 1, 'ABC', 'DV', '', 1, 1, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '2026-03-03 20:44:48', '2026-03-15 17:45:38'),
(5, NULL, 1, 'XYZ', 'XY', '', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '2026-03-09 10:52:32', '2026-03-12 13:01:23'),
(9, NULL, 1, 'IHF', '', '', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '2026-03-21 11:35:08', '2026-03-21 11:35:08'),
(10, NULL, 1, 'KSJHDF', '', '', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '2026-03-21 11:35:12', '2026-03-21 11:35:12'),
(11, NULL, 1, 'KJDHF', '', '', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '2026-03-21 11:35:15', '2026-03-21 11:35:15'),
(12, NULL, 1, 'DISHF', '', '', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '2026-03-21 11:35:17', '2026-03-21 11:35:17'),
(13, NULL, 1, 'KJDSHF', '', '', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '2026-03-21 11:35:19', '2026-03-21 11:35:19'),
(14, NULL, 1, 'OIDHGF', '', '', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '2026-03-21 11:35:20', '2026-03-21 11:35:20'),
(15, NULL, 1, 'KJDHG', '', '', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '2026-03-21 11:35:22', '2026-03-21 11:35:22'),
(16, NULL, 1, 'KJSDHJGF', '', '', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '2026-03-21 11:35:23', '2026-03-21 11:35:23'),
(17, NULL, 1, 'KJDFHGF', '', '', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '2026-03-21 11:35:25', '2026-03-21 11:35:25'),
(18, NULL, 1, 'LKSDNG', '', '', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '2026-03-21 11:35:26', '2026-03-21 11:35:26'),
(19, NULL, 1, 'LSKDJG', '', '', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '2026-03-21 11:35:29', '2026-03-21 11:35:29'),
(20, NULL, 1, 'OSDIJG', '', '', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '2026-03-21 11:35:31', '2026-03-21 11:35:31'),
(21, NULL, 1, 'SDJHGF', '', '', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '2026-03-21 11:35:32', '2026-03-21 11:35:32'),
(22, NULL, 1, 'DKFJG', '', '', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '2026-03-21 11:35:40', '2026-03-21 11:35:40'),
(23, NULL, 1, 'OIDFKJG', '', '', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '2026-03-21 11:35:42', '2026-03-21 11:35:42'),
(24, NULL, 1, 'ODFIJG', '', '', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '2026-03-21 11:35:43', '2026-03-21 11:35:43'),
(25, NULL, 1, 'DFKIKJG', '', '', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '2026-03-21 11:35:44', '2026-03-21 11:35:44'),
(26, NULL, 1, 'ODFIG', '', '', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '2026-03-21 11:35:46', '2026-03-21 11:35:46'),
(27, NULL, 1, 'OSDIG', '', '', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '2026-03-21 11:35:47', '2026-03-21 11:35:47'),
(28, NULL, 1, 'OSDKJG', '', '', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '2026-03-21 11:35:48', '2026-03-21 11:35:48'),
(29, NULL, 1, 'PSFOJG', '', '', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '2026-03-21 11:35:50', '2026-03-21 11:35:50'),
(30, NULL, 1, 'IJHF', '', '', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '2026-03-26 17:24:24', '2026-03-26 17:24:24'),
(31, NULL, 1, 'HBG', '', '', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '2026-03-26 17:27:57', '2026-03-26 17:27:57'),
(32, NULL, 1, 'AD', '', '', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'ASFF', '2026-03-27 00:12:28', '2026-03-27 00:12:28'),
(33, NULL, 1, 'IJHG', '', '', 1, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '2026-04-03 21:17:03', '2026-04-03 21:17:03');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `description`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'DEFAULT', 'System default role', 1, 0, '2026-03-05 09:44:57', '2026-03-05 09:44:57'),
(32, 'BASIC', '', 1, 2, '2026-03-05 10:21:07', '2026-03-05 10:21:07'),
(73, 'MASTER', '', 1, 2, '2026-03-05 13:27:19', '2026-03-05 13:27:19'),
(80, 'OIHF', '', 1, 2, '2026-03-24 18:12:35', '2026-03-24 18:12:35'),
(81, 'KSDJNV', '', 1, 2, '2026-03-24 18:12:36', '2026-03-24 18:12:36'),
(82, 'LKFNV', '', 1, 2, '2026-03-24 18:12:37', '2026-03-24 18:12:37'),
(83, 'LDKFJB', '', 1, 2, '2026-03-24 18:12:38', '2026-03-24 18:12:38'),
(84, 'LKDFJVB', '', 1, 2, '2026-03-24 18:12:39', '2026-03-24 18:12:39'),
(85, 'KJVB', '', 1, 2, '2026-03-24 18:12:40', '2026-03-24 18:12:40'),
(86, 'FJB', '', 1, 2, '2026-03-24 18:12:41', '2026-03-24 18:12:41'),
(87, 'VB]', '', 1, 2, '2026-03-24 18:12:42', '2026-03-24 18:12:42'),
(88, 'VB', '', 1, 2, '2026-03-24 18:12:43', '2026-03-24 18:12:43'),
(89, 'BJ', '', 1, 2, '2026-03-24 18:12:44', '2026-03-24 18:12:44'),
(90, 'KJGV', '', 1, 2, '2026-03-24 18:12:46', '2026-03-24 18:12:46'),
(91, 'KBJ4', '', 1, 2, '2026-03-24 18:12:47', '2026-03-24 18:12:47'),
(92, 'KJFHBV', '', 1, 2, '2026-03-24 18:12:48', '2026-03-24 18:12:48'),
(93, 'KLJB\'', '', 1, 2, '2026-03-24 18:12:50', '2026-03-24 18:12:50'),
(94, 'KHFVLK', '', 1, 2, '2026-03-24 18:12:52', '2026-03-24 18:12:52'),
(95, 'LIDFB]', '', 1, 2, '2026-03-24 18:12:55', '2026-03-24 18:12:55'),
(96, 'KSDHV', '', 1, 2, '2026-03-24 18:12:57', '2026-03-24 18:12:57');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` int(11) NOT NULL,
  `role_name` varchar(100) NOT NULL,
  `module_name` varchar(191) NOT NULL,
  `can_view` tinyint(1) NOT NULL DEFAULT 0,
  `can_add` tinyint(1) NOT NULL DEFAULT 0,
  `can_edit` tinyint(1) NOT NULL DEFAULT 0,
  `can_delete` tinyint(1) NOT NULL DEFAULT 0,
  `can_print` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role_name`, `module_name`, `can_view`, `can_add`, `can_edit`, `can_delete`, `can_print`, `created_at`, `updated_at`) VALUES
(7, 'DEFAULT', 'MASTERS', 1, 0, 0, 0, 0, '2026-03-05 11:34:58', '2026-03-05 11:34:58'),
(8, 'MASTER', 'MASTERS', 1, 0, 0, 0, 0, '2026-03-05 13:27:19', '2026-03-05 13:27:19'),
(9, 'MASTER', 'PAGE:PARTY_HTML', 1, 0, 0, 0, 0, '2026-03-05 13:27:19', '2026-03-05 13:27:19'),
(10, 'MASTER', 'PAGE:PRODUCT_HTML', 1, 0, 0, 0, 0, '2026-03-05 13:27:19', '2026-03-05 13:27:19'),
(11, 'MASTER', 'PAGE:BRAND_MASTER_HTML', 1, 0, 0, 0, 0, '2026-03-05 13:27:19', '2026-03-05 13:27:19'),
(117, 'basic', 'MASTERS', 1, 0, 0, 0, 0, '2026-03-15 18:22:56', '2026-03-15 18:22:56'),
(118, 'basic', 'PAGE:PARTY_HTML', 1, 1, 1, 1, 1, '2026-03-15 18:22:56', '2026-03-15 18:22:56'),
(119, 'basic', 'PAGE:PRODUCT_HTML', 1, 1, 1, 1, 1, '2026-03-15 18:22:56', '2026-03-15 18:22:56'),
(120, 'basic', 'PAGE:BRAND_MASTER_HTML', 1, 1, 1, 1, 1, '2026-03-15 18:22:56', '2026-03-15 18:22:56'),
(121, 'basic', 'PAGE:PARTY_WISE_BROKERAGE_RATE_SETUP_HTML', 1, 1, 1, 1, 1, '2026-03-15 18:22:56', '2026-03-15 18:22:56'),
(122, 'basic', 'PAGE:TRANSPORT_MASTER_HTML', 1, 1, 1, 1, 1, '2026-03-15 18:22:56', '2026-03-15 18:22:56'),
(123, 'basic', 'PAGE:STATE_HTML', 1, 1, 1, 1, 1, '2026-03-15 18:22:56', '2026-03-15 18:22:56'),
(124, 'basic', 'PAGE:CITY_HTML', 1, 1, 1, 1, 1, '2026-03-15 18:22:56', '2026-03-15 18:22:56'),
(125, 'basic', 'PAGE:DISTRICT_MASTER_HTML', 1, 1, 1, 1, 1, '2026-03-15 18:22:56', '2026-03-15 18:22:56'),
(126, 'basic', 'PAGE:DIVISION_MASTER_HTML', 1, 1, 1, 1, 1, '2026-03-15 18:22:56', '2026-03-15 18:22:56'),
(127, 'basic', 'PAGE:USER_MASTER_HTML', 1, 1, 1, 1, 1, '2026-03-15 18:22:56', '2026-03-15 18:22:56');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role_name` varchar(60) DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `allowed_divisions` text DEFAULT NULL,
  `allowed_companies` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`, `role_name`, `is_admin`, `is_active`, `created_by`, `allowed_divisions`, `allowed_companies`) VALUES
(2, 'admin', 'nakuljoshi112003@gmail.com', '$2y$10$X9to6vEJPqX9SdhPaTRB9u6dPdCetZBQcXF0F4tFdSxHGhn5w2sp6', '2026-02-17 15:38:08', '', 1, 1, NULL, '', ''),
(3, 'Test', 'nakuljoshi1990@gmail.com', '$2y$10$Y8Ltc0hgdyw6M4GTuWph6OrGAInLGW2.6R65ZXWzzBHKUfcDhZDsy', '2026-02-17 16:55:21', 'BASIC', 0, 0, NULL, 'DV', 'BRK25,BRK26'),
(9, 'Main', NULL, '$2y$10$aLwtsbXEKfK0fkg923yxjuJ0FD8YTneUFSikWKEpG83tQYgDU/itG', '2026-03-03 11:35:46', '', 0, 1, 2, 'DV', 'BRK25'),
(18, 'ASFF', NULL, '$2y$10$gOuH3bf1SGa3qz3v/OENqey46YtjjTq.9pNWb6orT5t.jWl0STkWe', '2026-03-24 18:11:03', '', 1, 1, 2, '', ''),
(19, 'SDGE', NULL, '$2y$10$PywKp.InjczyXestC5YdNupLdmfbQMn84YoRi6ajbsvR76FwGQL8u', '2026-03-24 18:11:08', '', 1, 1, 2, '', ''),
(20, 'DSG', NULL, '$2y$10$XoU2cCP0f3gKorphzQLhqOB/bu0WiOL27.6s8dFCVPXAA5zHT3szy', '2026-03-24 18:11:12', '', 1, 1, 2, '', ''),
(21, 'SF', NULL, '$2y$10$vS.Zr6/8HqvTBkCbRkcKh.syv5AGdMhTbKdA0BtUgzq3l7H2k.m1i', '2026-03-24 18:11:19', '', 1, 1, 2, '', ''),
(22, 'JSGF', NULL, '$2y$10$8r2xyXAcaKipzUzUO1lxOO.l0cp2ComtTYItAnB32HFCn8cekG5cW', '2026-03-24 18:11:23', '', 1, 1, 2, '', ''),
(23, 'AJSBF', NULL, '$2y$10$Z0pcDh5B6e95RvLJD24jreufN8LwrmwUqS0zEUY2WynBMbasv6Jvu', '2026-03-24 18:11:27', '', 1, 1, 2, '', ''),
(24, ';DHV', NULL, '$2y$10$9J9NfVsCa0HnAJXtjosy8ee7fAuXoVmFXrvdpxX9oD5XlrNGIid4W', '2026-03-24 18:11:31', '', 0, 1, 2, '', ''),
(25, 'KA', NULL, '$2y$10$4xXxYY.sOEO8EiNdSAB3k.Ha3bgQ.S7NIYBHvvhTMC4OAOlQwqwau', '2026-03-24 18:11:37', '', 1, 1, 2, '', ''),
(26, 'OIHF', NULL, '$2y$10$sQT3M.j1fUE0abazPRqfHuNPZnOLPaAQaVJh5PbYHNHyfNt6zPvs6', '2026-03-24 18:11:41', '', 1, 1, 2, '', ''),
(27, 'OISHF1', NULL, '$2y$10$sn8dHFLSstAvz0ARCotHFeTJRFh8LJwsuCSC/IzY/XIYe50WmNj4a', '2026-03-24 18:11:44', '', 1, 1, 2, '', ''),
(28, 'DOIHJFVOI', NULL, '$2y$10$nYFWfuBOht2nVlDGz9v1xuQjYcOpaIQNovkBgpKSx1RKOBkodah.O', '2026-03-24 18:11:46', '', 1, 1, 2, '', ''),
(29, 'OIJF1', NULL, '$2y$10$h0YldInZ1PvYR9Akcbhs/ee9fHpcfE/mB1Hrt4vmRpSZVFz94eFdu', '2026-03-24 18:11:51', '', 1, 1, 2, '', ''),
(30, 'SOIDHF1', NULL, '$2y$10$nxGdQv5gh945aK1q5CcpPOWPKAB4eqcXbQDSJbEXzlBc.hkhMLhJ2', '2026-03-24 18:11:55', '', 1, 1, 2, '', ''),
(31, 'OIJV', NULL, '$2y$10$11jDNhPJkA.7I8WbjyrUu.LQxawhexG5Q6lAepsEjoJ8CN98QZGd2', '2026-03-24 18:11:57', '', 1, 1, 2, '', ''),
(32, 'OFVJ', NULL, '$2y$10$HRfBWnn2aBtIQpuCBFXsNul6zyo5qGCfdh7ASFUaMI2tYFQjgkbJW', '2026-03-24 18:12:00', '', 1, 1, 2, '', ''),
(33, 'VLKVBJ', NULL, '$2y$10$OzsmaWOTIt6uztnJ3CepoeWh.eP0MqiDuhIneuUJHvYPJlRtJfjXq', '2026-03-24 18:12:02', '', 1, 1, 2, '', ''),
(34, 'LKVM', NULL, '$2y$10$gRSW3PdyTku7RXDh2rPWHOjWdVe5lCPCQTu/SFSHkbfxAm6to/p0K', '2026-03-24 18:12:04', '', 1, 1, 2, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

CREATE TABLE `user_permissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `module_name` varchar(100) NOT NULL,
  `can_view` tinyint(1) DEFAULT 0,
  `can_add` tinyint(1) DEFAULT 0,
  `can_edit` tinyint(1) DEFAULT 0,
  `can_delete` tinyint(1) DEFAULT 0,
  `can_print` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_permissions`
--

INSERT INTO `user_permissions` (`id`, `user_id`, `module_name`, `can_view`, `can_add`, `can_edit`, `can_delete`, `can_print`) VALUES
(451, 3, 'MASTERS', 1, 0, 0, 0, 0),
(452, 3, 'MISC. MASTERS', 1, 0, 0, 0, 0),
(453, 3, 'PAGE:PARTY_HTML', 1, 1, 1, 0, 0),
(454, 3, 'PAGE:PRODUCT_HTML', 1, 1, 1, 0, 0),
(455, 3, 'PAGE:BRAND_MASTER_HTML', 1, 1, 1, 0, 0),
(456, 3, 'PAGE:PARTY_WISE_BROKERAGE_RATE_SETUP_HTML', 1, 1, 1, 0, 0),
(457, 3, 'PAGE:TRANSPORT_MASTER_HTML', 1, 1, 1, 0, 0),
(458, 3, 'PAGE:STATE_HTML', 1, 1, 1, 0, 0),
(459, 3, 'PAGE:CITY_HTML', 1, 1, 1, 0, 0),
(460, 3, 'PAGE:DISTRICT_MASTER_HTML', 1, 1, 1, 0, 0),
(461, 3, 'PAGE:AREA_HTML', 1, 1, 1, 0, 0),
(462, 3, 'PAGE:LINE_MASTER_HTML', 1, 1, 1, 0, 0),
(463, 3, 'PAGE:LENGTH_MASTER_HTML', 1, 1, 1, 0, 0),
(464, 3, 'PAGE:DEALS_IN_MASTER_HTML', 1, 1, 1, 0, 0),
(465, 3, 'PAGE:TERM_TYPE_MASTER_HTML', 1, 1, 1, 0, 0),
(466, 3, 'PAGE:COURIER_MASTER_HTML', 1, 1, 1, 0, 0),
(467, 3, 'PAGE:NARRATION_MASTER_HTML', 1, 1, 1, 0, 0),
(468, 3, 'PAGE:SKU_UNIT_MASTER_HTML', 1, 1, 1, 0, 0),
(469, 3, 'PAGE:PARTY_TYPE_MASTER_HTML', 1, 1, 1, 0, 0),
(470, 3, 'PAGE:PARTY_CATEGORY_MASTER_HTML', 1, 1, 1, 0, 0),
(471, 3, 'PAGE:BANK_MASTER_HTML', 1, 1, 1, 0, 0),
(472, 3, 'PAGE:CONDITION_MASTER_HTML', 1, 1, 1, 0, 0),
(473, 3, 'PAGE:NOTE_MASTER_HTML', 1, 1, 1, 0, 0),
(474, 3, 'PAGE:COMPANY_GROUP_MASTER_HTML', 1, 1, 1, 0, 0),
(475, 3, 'PAGE:PRODUCT_TYPE_MASTER_HTML', 1, 1, 1, 0, 0),
(476, 3, 'PAGE:PRODUCT_GROUP_MASTER_HTML', 1, 1, 1, 0, 0),
(477, 3, 'PAGE:FORM_WISE_BOOK_SETUP_HTML', 1, 1, 1, 0, 0),
(478, 3, 'PAGE:ADD_LESS_PARAMETER_HTML', 1, 1, 1, 0, 0),
(479, 3, 'PAGE:DIVISION_MASTER_HTML', 1, 1, 1, 0, 0),
(480, 3, 'PAGE:USER_MASTER_HTML', 1, 1, 1, 1, 1),
(486, 9, 'MASTERS', 1, 0, 0, 0, 0),
(487, 9, 'PAGE:PARTY_HTML', 1, 0, 0, 0, 0),
(488, 9, 'PAGE:PRODUCT_HTML', 1, 0, 0, 0, 0),
(489, 9, 'PAGE:BRAND_MASTER_HTML', 1, 0, 0, 0, 0),
(490, 9, 'PAGE:PARTY_WISE_BROKERAGE_RATE_SETUP_HTML', 1, 0, 0, 0, 0),
(491, 9, 'PAGE:TRANSPORT_MASTER_HTML', 1, 0, 0, 0, 0),
(492, 9, 'PAGE:FORM_WISE_BOOK_SETUP_HTML', 1, 1, 1, 0, 0),
(493, 9, 'PAGE:ADD_LESS_PARAMETER_HTML', 1, 1, 1, 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `company_master`
--
ALTER TABLE `company_master`
  ADD PRIMARY KEY (`company_id`);

--
-- Indexes for table `division_master`
--
ALTER TABLE `division_master`
  ADD PRIMARY KEY (`division_id`),
  ADD UNIQUE KEY `uq_division_user_name` (`user_id`,`div_name`),
  ADD KEY `idx_division_user_default` (`user_id`,`is_default`),
  ADD KEY `idx_division_user_company` (`user_id`,`company_name`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_role_name` (`role_name`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_role_module` (`role_name`,`module_name`),
  ADD KEY `idx_role_name` (`role_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_module` (`user_id`,`module_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `company_master`
--
ALTER TABLE `company_master`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `division_master`
--
ALTER TABLE `division_master`
  MODIFY `division_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `user_permissions`
--
ALTER TABLE `user_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=494;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD CONSTRAINT `user_permissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
