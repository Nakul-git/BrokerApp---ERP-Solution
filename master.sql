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
-- Database: `master`
--

-- --------------------------------------------------------

--
-- Table structure for table `account_bank_details`
--

CREATE TABLE `account_bank_details` (
  `bank_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `ac_holder` varchar(120) DEFAULT '',
  `ac_number` varchar(60) DEFAULT '',
  `bank_name` varchar(120) DEFAULT '',
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `account_division_balance`
--

CREATE TABLE `account_division_balance` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `division_id` int(11) NOT NULL,
  `opening_balance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `dc` char(1) NOT NULL DEFAULT 'D',
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `account_master`
--

CREATE TABLE `account_master` (
  `account_id` int(11) NOT NULL,
  `group_name` varchar(100) NOT NULL,
  `account_name` varchar(150) NOT NULL,
  `account_name_hi` varchar(150) DEFAULT '',
  `address1` varchar(150) DEFAULT '',
  `address2` varchar(150) DEFAULT '',
  `address3` varchar(150) DEFAULT '',
  `other_details_address` varchar(150) DEFAULT '',
  `broker` varchar(150) DEFAULT '',
  `trans` varchar(150) DEFAULT '',
  `prop` varchar(150) DEFAULT '',
  `city_name` varchar(120) DEFAULT '',
  `state_name` varchar(120) DEFAULT '',
  `pin_code` varchar(12) DEFAULT '',
  `category` varchar(120) DEFAULT '',
  `tin` varchar(50) DEFAULT '',
  `cst` varchar(50) DEFAULT '',
  `gst` varchar(50) DEFAULT '',
  `pan` varchar(20) DEFAULT '',
  `email_id` varchar(150) DEFAULT '',
  `acc_type` varchar(50) DEFAULT '',
  `credit_d` decimal(12,2) NOT NULL DEFAULT 0.00,
  `credit_limit` decimal(12,2) NOT NULL DEFAULT 0.00,
  `contact_person` varchar(150) DEFAULT '',
  `office_phone` varchar(30) DEFAULT '',
  `fax` varchar(30) DEFAULT '',
  `mobile` varchar(30) DEFAULT '',
  `sms` varchar(30) DEFAULT '',
  `lock_date` date DEFAULT NULL,
  `other_info` varchar(255) DEFAULT '',
  `is_active` char(1) NOT NULL DEFAULT 'Y',
  `is_default` char(1) NOT NULL DEFAULT 'N',
  `hand_book_ac` char(1) NOT NULL DEFAULT 'N',
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account_master`
--

INSERT INTO `account_master` (`account_id`, `group_name`, `account_name`, `account_name_hi`, `address1`, `address2`, `address3`, `other_details_address`, `broker`, `trans`, `prop`, `city_name`, `state_name`, `pin_code`, `category`, `tin`, `cst`, `gst`, `pan`, `email_id`, `acc_type`, `credit_d`, `credit_limit`, `contact_person`, `office_phone`, `fax`, `mobile`, `sms`, `lock_date`, `other_info`, `is_active`, `is_default`, `hand_book_ac`, `user_id`) VALUES
(1, 'Test', 'ASFSAF', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0.00, 0.00, '', '', '', '', '', NULL, '', 'Y', 'N', 'N', 1);

-- --------------------------------------------------------

--
-- Table structure for table `add_less_entry_module`
--

CREATE TABLE `add_less_entry_module` (
  `module_id` int(11) NOT NULL,
  `module_name` varchar(120) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `active` char(1) NOT NULL DEFAULT 'Y',
  `user_id` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `add_less_entry_module`
--

INSERT INTO `add_less_entry_module` (`module_id`, `module_name`, `sort_order`, `active`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'Brokerage Bill', 1, 'Y', 1, '2026-03-15 20:24:22', '2026-03-15 20:24:22'),
(2, 'Cash / Bank (Creditors)', 2, 'Y', 1, '2026-03-15 20:24:22', '2026-03-15 20:24:22'),
(3, 'Cash / Bank (Debtors)', 3, 'Y', 1, '2026-03-15 20:24:22', '2026-03-15 20:24:22'),
(4, 'Loading (Buyer)', 4, 'Y', 1, '2026-03-15 20:24:22', '2026-03-15 20:24:22'),
(5, 'Loading (Seller)', 5, 'Y', 1, '2026-03-15 20:24:22', '2026-03-15 20:24:22'),
(6, 'Payment Entry (Buyer)', 6, 'Y', 1, '2026-03-15 20:24:22', '2026-03-15 20:24:22');

-- --------------------------------------------------------

--
-- Table structure for table `add_less_parameter_setup`
--

CREATE TABLE `add_less_parameter_setup` (
  `setup_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `description` varchar(140) NOT NULL,
  `parameter_type` varchar(10) NOT NULL DEFAULT 'Add',
  `order_no` int(11) DEFAULT NULL,
  `percent_value` decimal(12,4) DEFAULT NULL,
  `calculation` varchar(20) NOT NULL DEFAULT 'Percent',
  `active` char(1) NOT NULL DEFAULT 'Y',
  `applicable_on` varchar(80) DEFAULT NULL,
  `posting_ac` varchar(120) DEFAULT NULL,
  `outer_column` varchar(120) DEFAULT NULL,
  `cst_vat_other` varchar(120) DEFAULT NULL,
  `si_flag` varchar(10) DEFAULT NULL,
  `from_value` decimal(12,4) DEFAULT NULL,
  `end_value` decimal(12,4) DEFAULT NULL,
  `rate_edt` char(1) NOT NULL DEFAULT 'Y',
  `amt_edt` char(1) NOT NULL DEFAULT 'Y',
  `amt_round` char(1) NOT NULL DEFAULT 'Y',
  `division_name` varchar(120) DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `add_less_parameter_setup`
--

INSERT INTO `add_less_parameter_setup` (`setup_id`, `module_id`, `description`, `parameter_type`, `order_no`, `percent_value`, `calculation`, `active`, `applicable_on`, `posting_ac`, `outer_column`, `cst_vat_other`, `si_flag`, `from_value`, `end_value`, `rate_edt`, `amt_edt`, `amt_round`, `division_name`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'HG', 'Less', NULL, NULL, 'Amount', 'Y', '', '', '', '', '', NULL, NULL, 'Y', 'Y', 'Y', '', 1, '2026-03-15 20:25:00', '2026-03-23 15:57:21'),
(2, 1, 'AUJIHF', 'Add', NULL, NULL, 'Percent', 'Y', '', '', '', '', '', NULL, NULL, 'Y', 'Y', 'Y', '', 1, '2026-03-15 20:31:48', '2026-03-15 20:31:48'),
(3, 1, 'ILUAHF', 'Add', NULL, NULL, 'Percent', 'Y', '', '', '', '', '', NULL, NULL, 'Y', 'Y', 'Y', '', 1, '2026-03-15 20:31:56', '2026-03-15 20:31:56'),
(4, 1, 'IDHF', 'Add', NULL, NULL, 'Percent', 'Y', '', '', '', '', '', NULL, NULL, 'Y', 'Y', 'Y', '', 1, '2026-03-15 20:38:16', '2026-03-15 20:38:16'),
(5, 1, 'IHDF', 'Add', 13, NULL, 'Percent', 'Y', '', '', 'SF', '', '', NULL, NULL, 'Y', 'Y', 'Y', '', 1, '2026-03-15 20:41:19', '2026-03-15 20:41:19'),
(6, 1, 'KJDBF', 'Add', NULL, NULL, 'Percent', 'Y', '', '', '', '', '', NULL, NULL, 'Y', 'Y', 'Y', '', 1, '2026-03-15 20:46:23', '2026-03-15 20:46:23'),
(7, 3, 'DJHBF]', 'Add', NULL, NULL, 'Percent', 'Y', '', '', '', '', '', NULL, NULL, 'Y', 'Y', 'Y', '', 1, '2026-03-15 20:51:59', '2026-03-15 20:51:59'),
(8, 3, 'ASF', 'Add', NULL, NULL, 'Percent', 'Y', '', '', '', '', '', NULL, NULL, 'Y', 'Y', 'Y', '', 1, '2026-03-15 20:52:02', '2026-03-15 20:52:02'),
(9, 1, 'UJDHSF', 'Add', NULL, NULL, 'Percent', 'Y', '', '', '', '', '', NULL, NULL, 'Y', 'Y', 'Y', '', 1, '2026-03-15 20:55:44', '2026-03-15 20:55:44'),
(10, 1, 'ASF', 'Add', NULL, NULL, 'Percent', 'Y', '', '', '', '', '', NULL, NULL, 'Y', 'Y', 'Y', '', 1, '2026-03-15 20:55:46', '2026-03-15 20:55:46'),
(11, 1, 'AF', 'Add', NULL, NULL, 'Percent', 'Y', '', '', '', '', '', NULL, NULL, 'Y', 'Y', 'Y', '', 1, '2026-03-15 20:55:48', '2026-03-15 20:55:48'),
(12, 1, 'FAF', 'Add', NULL, NULL, 'Percent', 'Y', '', '', '', '', '', NULL, NULL, 'Y', 'Y', 'Y', '', 1, '2026-03-15 20:55:51', '2026-03-15 20:55:51'),
(13, 1, 'ASFGF', 'Add', NULL, NULL, 'Percent', 'Y', '', '', '', '', '', NULL, NULL, 'Y', 'Y', 'Y', '', 1, '2026-03-15 20:55:54', '2026-03-15 20:55:54'),
(14, 1, 'KJDHSVJ', 'Add', NULL, NULL, 'Percent', 'Y', '', '', '', '', '', NULL, NULL, 'Y', 'Y', 'Y', '', 1, '2026-03-15 20:56:02', '2026-03-15 20:56:02'),
(15, 1, '2DVG', 'Add', NULL, NULL, 'Percent', 'Y', '', '', '', '', '', NULL, NULL, 'Y', 'Y', 'Y', '', 1, '2026-03-15 20:56:04', '2026-03-15 20:56:04'),
(16, 1, '2B', 'Add', NULL, NULL, 'Percent', 'Y', '', '', '', '', '', NULL, NULL, 'Y', 'Y', 'Y', '', 1, '2026-03-15 20:56:08', '2026-03-15 20:56:08');

-- --------------------------------------------------------

--
-- Table structure for table `area`
--

CREATE TABLE `area` (
  `name` varchar(50) NOT NULL,
  `area_id` int(11) NOT NULL,
  `city_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `area`
--

INSERT INTO `area` (`name`, `area_id`, `city_id`, `user_id`) VALUES
('LOKMANYA', 27, NULL, 1),
('DHAR', 28, NULL, 1),
('ADFA', 29, NULL, 1),
('B', 30, NULL, 1),
('C', 31, NULL, 1),
('D', 32, NULL, 1),
('E', 33, NULL, 1),
('F', 34, NULL, 1),
('I', 35, NULL, 1),
('J', 36, NULL, 1),
('K', 37, NULL, 1),
('L', 38, NULL, 1),
('M', 39, NULL, 1),
('N', 40, NULL, 1),
('O', 41, NULL, 1),
('P', 42, NULL, 1),
('Q', 43, NULL, 1),
('R', 44, NULL, 1),
('ADFF', 45, NULL, 1),
('ADF', 46, NULL, 1),
('AF', 47, NULL, 1),
('ASF', 48, NULL, 1),
('IUHDF', 49, NULL, 1),
('ASGF', 50, NULL, 1),
('LOUJSDHF', 51, NULL, 1),
('FASF', 52, NULL, 1),
('SAFG', 53, NULL, 1),
('AFF', 54, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `bank`
--

CREATE TABLE `bank` (
  `bank_id` int(10) UNSIGNED NOT NULL,
  `bank_name` varchar(180) NOT NULL,
  `branch` varchar(180) DEFAULT NULL,
  `ifsc_code` varchar(20) DEFAULT NULL,
  `pin` varchar(10) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bank`
--

INSERT INTO `bank` (`bank_id`, `bank_name`, `branch`, `ifsc_code`, `pin`, `user_id`, `created_at`, `updated_at`) VALUES
(3, 'SBI', '', '', '', 1, '2026-03-01 13:39:14', '2026-03-03 20:42:54'),
(5, 'ASF', '', '', '', 1, '2026-03-15 14:00:29', '2026-03-15 14:00:29'),
(7, 'SAJHVBD', '', '', '', 1, '2026-03-15 14:08:25', '2026-03-15 14:08:25'),
(8, 'SF', '', '', '', 1, '2026-03-15 14:08:42', '2026-03-15 14:08:42'),
(9, 'ASKJBDF', '', '', '', 1, '2026-03-15 14:08:49', '2026-03-15 14:08:49'),
(10, 'JSD', '', '', '', 1, '2026-03-15 14:11:18', '2026-03-15 14:11:18'),
(11, 'DGF', '', '', '', 1, '2026-03-15 14:11:21', '2026-03-15 14:11:21'),
(12, 'DF', '', '', '', 1, '2026-03-15 14:11:25', '2026-03-15 14:11:25'),
(13, 'EFEF', '', '', '', 1, '2026-03-15 14:11:47', '2026-03-15 14:11:47'),
(14, 'EF', '', '', '', 1, '2026-03-15 14:13:03', '2026-03-15 14:13:03'),
(15, 'SFGF', '', '', '', 1, '2026-03-15 14:15:33', '2026-03-15 14:15:33'),
(16, 'HSBF', '', '', '', 1, '2026-03-15 14:18:10', '2026-03-15 14:18:10'),
(17, 'KJNF', '', '', '', 1, '2026-03-16 18:12:27', '2026-03-16 18:12:27'),
(18, 'KJSBAFKJB', 'ADSFDF', '1546846', '32135435', 1, '2026-03-16 18:44:52', '2026-03-18 20:06:55'),
(19, 'KJJHF', '', '', '', 1, '2026-03-18 20:07:00', '2026-03-18 20:07:00');

-- --------------------------------------------------------

--
-- Table structure for table `brand`
--

CREATE TABLE `brand` (
  `brand_id` int(10) UNSIGNED NOT NULL,
  `brand_name` varchar(120) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `user_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brand`
--

INSERT INTO `brand` (`brand_id`, `brand_name`, `sort_order`, `user_id`, `created_at`, `updated_at`) VALUES
(4, 'HBSF', 0, 1, '2026-03-16 10:00:54', '2026-03-16 10:00:54'),
(6, 'ASF', 0, 1, '2026-03-16 10:00:57', '2026-03-16 10:00:57'),
(8, 'JDHNF', 0, 1, '2026-03-21 11:57:45', '2026-03-21 11:57:45'),
(9, 'AOJNF', 0, 1, '2026-03-21 11:57:47', '2026-03-21 11:57:47'),
(10, 'DKJNVG', 0, 1, '2026-03-21 11:57:49', '2026-03-21 11:57:49'),
(11, 'IJSDHGF', 0, 1, '2026-03-21 11:57:50', '2026-03-21 11:57:50'),
(12, 'OKSDNF', 0, 1, '2026-03-21 11:57:51', '2026-03-21 11:57:51'),
(13, 'OSDNJF', 0, 1, '2026-03-21 11:57:52', '2026-03-21 11:57:52'),
(14, 'OSDNF', 0, 1, '2026-03-21 11:57:54', '2026-03-21 11:57:54'),
(15, 'OISDNF', 0, 1, '2026-03-21 11:57:54', '2026-03-21 11:57:54'),
(16, 'DSOIJFO', 0, 1, '2026-03-21 11:57:57', '2026-03-21 11:57:57'),
(17, 'LSDKNF', 0, 1, '2026-03-21 11:57:58', '2026-03-21 11:57:58'),
(18, 'ODKNF', 0, 1, '2026-03-21 11:58:00', '2026-03-21 11:58:00'),
(19, 'ODSNF', 0, 1, '2026-03-21 11:58:01', '2026-03-21 11:58:01'),
(20, 'SDNFV', 0, 1, '2026-03-21 11:58:02', '2026-03-21 11:58:02'),
(21, 'LKSDNF', 0, 1, '2026-03-21 11:58:03', '2026-03-21 11:58:03'),
(22, 'ODSJFV', 0, 1, '2026-03-21 11:58:04', '2026-03-21 11:58:04'),
(23, 'OISDNJFV', 0, 1, '2026-03-21 11:58:06', '2026-03-21 11:58:06'),
(24, 'ODKSNFVOLN', 0, 1, '2026-03-21 11:58:07', '2026-03-21 11:58:07');

-- --------------------------------------------------------

--
-- Table structure for table `city`
--

CREATE TABLE `city` (
  `city_name` varchar(50) NOT NULL,
  `district_id` int(11) DEFAULT NULL,
  `city_id` int(11) NOT NULL,
  `state_id` int(11) DEFAULT NULL,
  `pin_code` varchar(10) DEFAULT NULL,
  `std_code` varchar(10) DEFAULT NULL,
  `party_type` varchar(20) NOT NULL DEFAULT 'INTER-STATE',
  `distance_kms` decimal(10,2) NOT NULL DEFAULT 0.00,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `city`
--

INSERT INTO `city` (`city_name`, `district_id`, `city_id`, `state_id`, `pin_code`, `std_code`, `party_type`, `distance_kms`, `user_id`) VALUES
('UJJAIN', 4, 77, 96, '', '', 'INTER-STATE', 0.00, 1),
('DHAR', 19, 86, 96, '', '', 'INTER-STATE', 0.00, 1),
('SAGAR', 21, 89, 96, '', '', 'INTER-STATE', 0.00, 1),
('INDORE', NULL, 91, 96, '', '', 'INTER-STATE', 0.00, 1),
('SDA', NULL, 92, 106, '', '', 'INTER-STATE', 0.00, 1),
('A', NULL, 93, 96, '', '', 'INTER-STATE', 0.00, 1),
('B', NULL, 94, 106, '', '', 'INTER-STATE', 0.00, 1),
('C', NULL, 95, 101, '', '', 'INTER-STATE', 0.00, 1),
('D', NULL, 96, 142, '', '', 'INTER-STATE', 0.00, 1),
('E', NULL, 97, 101, '', '', 'INTER-STATE', 0.00, 1),
('F', NULL, 98, 101, '', '', 'INTER-STATE', 0.00, 1),
('J', NULL, 99, 101, '', '', 'INTER-STATE', 0.00, 1),
('K', NULL, 100, 101, '', '', 'INTER-STATE', 0.00, 1),
('N', NULL, 102, 142, '', '', 'INTER-STATE', 0.00, 1),
('P', NULL, 103, 101, '', '', 'INTER-STATE', 0.00, 1),
('Q', NULL, 104, 101, '', '', 'INTER-STATE', 0.00, 1),
('R', NULL, 105, 101, '', '', 'INTER-STATE', 0.00, 1),
('M', 33, 107, 101, '', '', 'INTER-STATE', 0.00, 1),
('ASF', 22, 109, 142, '-1', '', 'INTER-STATE', 0.00, 1),
('ADFF', NULL, 111, 142, '', '', 'INTER-STATE', 0.00, 1),
('SAF', 38, 112, 101, '', '', 'INTER-STATE', 0.00, 1),
('TEST', 39, 114, 96, '', '', 'INTER-STATE', 0.00, 1),
('ASDF', NULL, 115, 108, '', '', 'INTER-STATE', 0.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `company_group`
--

CREATE TABLE `company_group` (
  `company_group_id` int(10) UNSIGNED NOT NULL,
  `acc_name` varchar(180) NOT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `address3` varchar(255) DEFAULT NULL,
  `address4` varchar(255) DEFAULT NULL,
  `station` varchar(120) DEFAULT NULL,
  `state_name` varchar(120) DEFAULT NULL,
  `pin_code` varchar(12) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `phone_no` varchar(30) DEFAULT NULL,
  `contact_person` varchar(120) DEFAULT NULL,
  `pan_no` varchar(20) DEFAULT NULL,
  `applicable_divisions` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `company_group`
--

INSERT INTO `company_group` (`company_group_id`, `acc_name`, `address1`, `address2`, `address3`, `address4`, `station`, `state_name`, `pin_code`, `is_active`, `phone_no`, `contact_person`, `pan_no`, `applicable_divisions`, `user_id`, `created_at`, `updated_at`) VALUES
(3, 'ASD', '', '', '', '', 'B', 'DELHI', '', 1, '', '', '', '', 1, '2026-03-14 20:31:56', '2026-03-23 15:21:10'),
(4, 'ASUDFGH', '', '', '', '', 'A', 'MADHYA PRADESH', '', 1, '', '', '', '', 1, '2026-03-23 15:19:46', '2026-03-23 15:19:46');

-- --------------------------------------------------------

--
-- Table structure for table `condition_master`
--

CREATE TABLE `condition_master` (
  `condition_id` int(10) UNSIGNED NOT NULL,
  `term_description` varchar(255) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `packing_condition` tinyint(1) NOT NULL DEFAULT 0,
  `loading_condition` tinyint(1) NOT NULL DEFAULT 0,
  `payment_condition` tinyint(1) NOT NULL DEFAULT 0,
  `application_items_json` longtext DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `condition_master`
--

INSERT INTO `condition_master` (`condition_id`, `term_description`, `is_default`, `packing_condition`, `loading_condition`, `payment_condition`, `application_items_json`, `user_id`, `created_at`, `updated_at`) VALUES
(2, 'TEST', 1, 1, 1, 1, '[]', 1, '2026-03-01 13:28:03', '2026-03-17 12:51:33'),
(3, 'MAIN', 0, 1, 1, 1, '[]', 1, '2026-03-13 20:48:37', '2026-03-13 20:48:37'),
(5, 'SAF', 0, 1, 1, 1, '[]', 1, '2026-03-15 14:16:00', '2026-03-15 14:16:00'),
(6, 'SFFF', 0, 1, 1, 1, '[]', 1, '2026-03-15 14:16:03', '2026-03-15 18:33:18'),
(7, 'SF', 0, 1, 1, 1, '[]', 1, '2026-03-15 18:33:10', '2026-03-15 18:33:10'),
(8, 'KJHSDBFV', 0, 1, 1, 1, '[]', 1, '2026-03-26 20:16:14', '2026-03-26 20:16:14'),
(9, 'OADUIHF', 0, 1, 1, 1, '[]', 1, '2026-03-26 20:16:15', '2026-03-26 20:16:15'),
(10, 'OLJADHF', 0, 1, 1, 1, '[]', 1, '2026-03-26 20:16:16', '2026-03-26 20:16:16'),
(11, 'OSDIJF', 0, 1, 1, 1, '[]', 1, '2026-03-26 20:16:17', '2026-03-26 20:16:17'),
(12, ';OSDIJFV', 0, 1, 1, 1, '[]', 1, '2026-03-26 20:16:18', '2026-03-26 20:16:18'),
(13, 'ODSIJF', 0, 1, 1, 1, '[]', 1, '2026-03-26 20:16:18', '2026-03-26 20:16:18'),
(14, 'O;ISDJV', 0, 1, 1, 1, '[]', 1, '2026-03-26 20:16:19', '2026-03-26 20:16:19'),
(15, 'POIJV', 0, 1, 1, 1, '[]', 1, '2026-03-26 20:16:20', '2026-03-26 20:16:20'),
(16, 'KIJJDFV', 0, 1, 1, 1, '[]', 1, '2026-03-26 20:16:20', '2026-03-26 20:16:20'),
(17, 'OIHJDSFV', 0, 1, 1, 1, '[]', 1, '2026-03-26 20:16:21', '2026-03-26 20:16:21'),
(18, 'OIHSD', 0, 1, 1, 1, '[]', 1, '2026-03-26 20:16:22', '2026-03-26 20:16:22'),
(19, 'OIHFV', 0, 1, 1, 1, '[]', 1, '2026-03-26 20:16:22', '2026-03-26 20:16:22'),
(20, 'OIHFS', 0, 1, 1, 1, '[]', 1, '2026-03-26 20:16:23', '2026-03-26 20:16:23'),
(21, 'OIUJV', 0, 1, 1, 1, '[]', 1, '2026-03-26 20:16:23', '2026-03-26 20:16:23'),
(22, 'IOJDFSHV', 0, 1, 1, 1, '[]', 1, '2026-03-26 20:16:24', '2026-03-26 20:16:24'),
(23, 'O;IDHBV', 0, 1, 1, 1, '[]', 1, '2026-03-26 20:16:25', '2026-03-26 20:16:25');

-- --------------------------------------------------------

--
-- Table structure for table `courier`
--

CREATE TABLE `courier` (
  `courier_id` int(11) NOT NULL,
  `courier_name` varchar(150) NOT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `address3` varchar(255) DEFAULT NULL,
  `address4` varchar(255) DEFAULT NULL,
  `station` varchar(120) DEFAULT NULL,
  `state_name` varchar(120) DEFAULT NULL,
  `pin_code` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `contact_person` varchar(120) DEFAULT NULL,
  `pan_no` varchar(30) DEFAULT NULL,
  `applicable_divisions` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courier`
--

INSERT INTO `courier` (`courier_id`, `courier_name`, `address1`, `address2`, `address3`, `address4`, `station`, `state_name`, `pin_code`, `is_active`, `contact_person`, `pan_no`, `applicable_divisions`, `user_id`) VALUES
(3, 'TEST', '', '', '', '', 'INDORE', 'MADHYA PRADESH', '', 1, '', '', '', 1),
(5, 'ASF', '', '', '', '', 'SAGAR', 'MADHYA PRADESH', '', 1, '', '', '', 1),
(6, 'KJHSD', '', '', '', '', 'ASDF', 'HARYANA', '', 1, '', '', '', 1),
(7, 'KJHEF', '', '', '', '', '', '', '', 1, '', '', '', 1),
(8, 'ASFAF', '', '', '', '', '', '', '', 1, '', '', '', 1),
(9, 'ASGFADG', '', '', '', '', '', '', '', 1, '', '', '', 1),
(10, 'ASFAFFAFDSFDF', '', '', '', 'ADGAD', '', '', '', 1, '', '', '', 1),
(11, 'DFHGGDHGF', '', '', '', '', '', '', '', 1, '', '', '', 1),
(12, 'FGJGFJ', '', '', '', '', '', '', '', 1, '', '', '', 1),
(13, 'JFGJF', '', '', '', '', '', '', '', 1, '', '', '', 1),
(14, 'GJFGJ', '', '', '', '', '', '', '', 1, '', '', '', 1),
(15, 'GF', '', '', '', '', '', '', '', 1, '', '', '', 1),
(16, 'FGJFGJ', '', '', '', '', '', '', '', 1, '', '', '', 1),
(17, 'FGJFGJF', '', '', '', '', '', '', '', 1, '', '', '', 1),
(18, 'GFJGFJJJ', '', '', '', '', '', '', '', 1, '', '', '', 1),
(19, 'DGSFSADAD', '', '', '', '', '', '', '', 1, '', '', '', 1),
(20, 'ASGGESGG', '', '', '', '', '', '', '', 1, '', '', '', 1),
(21, 'ASDASDADADADAD', '', '', '', '', '', '', '', 1, '', '', '', 1),
(22, 'ASFASFASDA', '', '', '', '', '', '', '', 1, '', '', '', 1),
(23, 'KJBFKJNG', '', '', '', '', '', '', '', 1, '', '', '', 1),
(24, '.KJNFKJBKJF', '', '', '', '', '', '', '', 1, '', '', '', 1),
(25, 'KLJHGFKJN', '', '', '', '', '', '', '', 1, '', '', '', 1),
(26, 'LKFNGKNM', '', '', '', '', '', '', '', 1, '', '', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `deals_in_master`
--

CREATE TABLE `deals_in_master` (
  `deals_id` int(11) NOT NULL,
  `deals_name` varchar(150) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deals_in_master`
--

INSERT INTO `deals_in_master` (`deals_id`, `deals_name`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'DEAL', 1, '2026-03-01 11:20:39', '2026-03-03 20:42:54'),
(2, 'SD', 1, '2026-03-13 12:55:19', '2026-03-14 14:02:07'),
(5, 'D', 1, '2026-03-13 12:55:25', '2026-03-13 12:55:25'),
(6, 'E', 1, '2026-03-13 12:55:26', '2026-03-13 12:55:26'),
(7, 'F', 1, '2026-03-13 12:55:27', '2026-03-13 12:55:27'),
(8, 'G', 1, '2026-03-13 12:55:28', '2026-03-13 12:55:28'),
(9, 'H', 1, '2026-03-13 12:55:29', '2026-03-13 12:55:29'),
(10, 'I', 1, '2026-03-13 12:55:30', '2026-03-13 12:55:30'),
(11, 'J', 1, '2026-03-13 12:55:31', '2026-03-13 12:55:31'),
(12, 'K', 1, '2026-03-13 12:55:32', '2026-03-13 12:55:32'),
(13, 'L', 1, '2026-03-13 12:55:33', '2026-03-13 12:55:33'),
(14, 'M', 1, '2026-03-13 12:55:34', '2026-03-13 12:55:34'),
(15, 'N', 1, '2026-03-13 12:55:35', '2026-03-13 12:55:35'),
(16, 'O', 1, '2026-03-13 12:55:36', '2026-03-13 12:55:36'),
(17, 'P', 1, '2026-03-13 12:55:37', '2026-03-13 12:55:37'),
(18, 'Q', 1, '2026-03-13 12:55:38', '2026-03-13 12:55:38'),
(19, 'DD', 1, '2026-03-13 22:08:38', '2026-03-13 22:08:38'),
(20, 'DSF', 1, '2026-03-14 14:02:13', '2026-03-14 14:02:13'),
(21, 'DDD', 1, '2026-03-14 14:19:47', '2026-03-14 14:19:47'),
(22, 'FF', 1, '2026-03-14 14:25:16', '2026-03-14 14:25:16'),
(23, 'DSD', 1, '2026-03-14 14:25:52', '2026-03-14 14:25:52'),
(24, 'SDF', 1, '2026-03-14 14:30:09', '2026-03-14 14:30:09');

-- --------------------------------------------------------

--
-- Table structure for table `district`
--

CREATE TABLE `district` (
  `district_id` int(11) NOT NULL,
  `district_name` varchar(150) NOT NULL,
  `state_id` int(11) DEFAULT NULL,
  `population` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `area_sq_kms` decimal(12,2) NOT NULL DEFAULT 0.00,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `district`
--

INSERT INTO `district` (`district_id`, `district_name`, `state_id`, `population`, `area_sq_kms`, `user_id`) VALUES
(4, 'UJJAIN', 96, 0, 0.00, 1),
(19, 'DHAR', 96, 0, 0.00, 1),
(21, 'SAGAR', 96, 0, 0.00, 1),
(22, 'ABAS', 142, 0, 0.00, 1),
(24, 'C', 142, 0, 0.00, 1),
(25, 'D', 101, 0, 0.00, 1),
(26, 'E', 142, 0, 0.00, 1),
(27, 'F', 101, 0, 0.00, 1),
(28, 'G', 101, 0, 0.00, 1),
(29, 'H', 101, 0, 0.00, 1),
(30, 'I', 101, 0, 0.00, 1),
(31, 'J', 110, 0, 0.00, 1),
(32, 'K', 101, 0, 0.00, 1),
(33, 'M', 142, 0, 0.00, 1),
(34, 'N', 101, 0, 0.00, 1),
(35, 'O', 101, 0, 0.00, 1),
(36, 'Q', 142, 0, 0.00, 1),
(37, 'AB', 142, 0, 0.00, 1),
(38, 'SAF', 101, 0, 0.00, 1),
(39, 'TEST', 101, 0, 0.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `form_wise_book_setup`
--

CREATE TABLE `form_wise_book_setup` (
  `setup_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `description` varchar(120) NOT NULL,
  `book` varchar(60) NOT NULL,
  `numbering_type` varchar(20) NOT NULL DEFAULT 'Auto',
  `starting_no` int(11) DEFAULT NULL,
  `end_no` int(11) DEFAULT NULL,
  `restart_numbering` varchar(20) NOT NULL DEFAULT 'Yearly',
  `lock_date` date DEFAULT NULL,
  `active` char(1) NOT NULL DEFAULT 'Y',
  `division_name` varchar(120) DEFAULT NULL,
  `item_list` varchar(180) DEFAULT NULL,
  `cash_credit` varchar(20) NOT NULL DEFAULT 'Both',
  `user_id` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `form_wise_book_setup`
--

INSERT INTO `form_wise_book_setup` (`setup_id`, `module_id`, `description`, `book`, `numbering_type`, `starting_no`, `end_no`, `restart_numbering`, `lock_date`, `active`, `division_name`, `item_list`, `cash_credit`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'ASD', 'ASDF', 'Auto', NULL, NULL, 'Yearly', NULL, 'Y', '', '', 'Both', 1, '2026-03-15 20:00:45', '2026-03-15 20:00:45'),
(4, 1, 'KJDBF', 'SSF', 'Auto', NULL, NULL, 'Yearly', NULL, 'Y', '', '', 'Both', 1, '2026-03-15 20:42:49', '2026-03-15 20:42:49'),
(5, 1, 'KJNF', 'SAF', 'Auto', 13, 22, 'Yearly', '2026-03-24', 'Y', '', 'ASFG', 'Both', 1, '2026-03-15 20:43:02', '2026-03-23 15:51:50'),
(6, 1, 'IUHF', 'SF', 'Auto', NULL, NULL, 'Yearly', NULL, 'Y', '', '', 'Both', 1, '2026-03-15 20:46:34', '2026-03-15 20:46:34'),
(7, 1, 'KJDBF', 'SAF', 'Auto', NULL, NULL, 'Yearly', NULL, 'Y', '', '', 'Both', 1, '2026-03-15 20:47:45', '2026-03-15 20:47:45'),
(8, 3, 'AF', 'ASF', 'Auto', NULL, NULL, 'Yearly', NULL, 'Y', '', '', 'Both', 1, '2026-03-16 18:03:36', '2026-03-16 18:03:36'),
(9, 1, 'DKJF', 'FAF', 'Auto', NULL, NULL, 'Yearly', NULL, 'Y', '', '', 'Both', 1, '2026-03-23 15:45:27', '2026-03-23 15:45:27'),
(10, 1, 'IJDHF', 'KDJNF', 'Auto', NULL, NULL, 'Yearly', NULL, 'Y', '', '', 'Both', 1, '2026-03-23 15:51:35', '2026-03-23 15:51:35');

-- --------------------------------------------------------

--
-- Table structure for table `form_wise_entry_module`
--

CREATE TABLE `form_wise_entry_module` (
  `module_id` int(11) NOT NULL,
  `module_name` varchar(120) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `active` char(1) NOT NULL DEFAULT 'Y',
  `user_id` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `form_wise_entry_module`
--

INSERT INTO `form_wise_entry_module` (`module_id`, `module_name`, `sort_order`, `active`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'Brokerage Bill', 1, 'Y', 1, '2026-03-15 19:59:50', '2026-03-15 19:59:50'),
(2, 'Cash / Bank Entry', 2, 'Y', 1, '2026-03-15 19:59:50', '2026-03-15 19:59:50'),
(3, 'Claim / Settlement Voucher', 3, 'Y', 1, '2026-03-15 19:59:50', '2026-03-15 19:59:50'),
(4, 'Daily Sauda', 4, 'Y', 1, '2026-03-15 19:59:50', '2026-03-15 19:59:50'),
(5, 'Journal Entry', 5, 'Y', 1, '2026-03-15 19:59:50', '2026-03-15 19:59:50'),
(6, 'Loading', 6, 'Y', 1, '2026-03-15 19:59:50', '2026-03-15 19:59:50'),
(7, 'Payment Entry (Buyer)', 7, 'Y', 1, '2026-03-15 19:59:50', '2026-03-15 19:59:50');

-- --------------------------------------------------------

--
-- Table structure for table `group_master`
--

CREATE TABLE `group_master` (
  `group_id` int(10) UNSIGNED NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `main_group_name` varchar(180) DEFAULT NULL,
  `group_name` varchar(180) NOT NULL,
  `group_type` enum('Income','Expenditure','Liabilities','Assets') NOT NULL DEFAULT 'Liabilities',
  `maintain_bill_outstanding` enum('Y','N') NOT NULL DEFAULT 'N',
  `suppress_trial_balance` enum('Y','N') NOT NULL DEFAULT 'N',
  `address_details_req` enum('Y','N') NOT NULL DEFAULT 'N',
  `general_ledger` enum('Y','N') NOT NULL DEFAULT 'N',
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `group_primary` char(1) NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `group_master`
--

INSERT INTO `group_master` (`group_id`, `sort_order`, `main_group_name`, `group_name`, `group_type`, `maintain_bill_outstanding`, `suppress_trial_balance`, `address_details_req`, `general_ledger`, `user_id`, `created_at`, `updated_at`, `group_primary`) VALUES
(1, 0, 'PRIMARY', 'Test', 'Income', 'N', 'N', 'Y', 'N', 1, '2026-02-18 17:49:46', '2026-03-19 14:25:30', 'N'),
(4, 10, '', 'ASDF', 'Liabilities', 'N', 'Y', 'N', 'N', 1, '2026-03-16 19:20:17', '2026-03-18 19:58:10', 'N'),
(5, 0, '', 'SAF', 'Liabilities', 'N', 'N', 'N', 'N', 1, '2026-03-17 13:01:30', '2026-03-17 13:01:30', 'N'),
(6, 0, 'PRIMARY', 'JHBF', 'Assets', 'N', 'N', 'N', 'N', 1, '2026-03-17 13:08:46', '2026-03-18 21:03:11', 'N'),
(7, 1, '', 'PRIMARY', 'Assets', 'N', 'N', 'N', 'N', 1, '2026-03-18 20:58:26', '2026-03-19 14:24:39', 'Y'),
(8, 0, '', 'FISJAFOIJ', 'Liabilities', 'N', 'N', 'N', 'N', 1, '2026-03-18 21:14:55', '2026-03-18 21:14:55', 'N'),
(9, 0, '', 'ASFASF', 'Income', 'N', 'N', 'N', 'N', 1, '2026-03-18 21:14:57', '2026-03-18 21:42:50', 'N'),
(10, 0, '', 'IUHF', 'Liabilities', 'N', 'N', 'N', 'N', 1, '2026-03-18 21:15:07', '2026-03-18 21:15:07', 'N'),
(11, 0, '', 'UDGHSF', 'Liabilities', 'N', 'N', 'N', 'N', 1, '2026-03-18 21:15:10', '2026-03-18 21:15:32', 'N'),
(12, 0, '', 'JHDSKJVNKSDJNF', 'Expenditure', 'N', 'N', 'N', 'N', 1, '2026-03-18 21:15:15', '2026-03-18 21:42:56', 'N'),
(13, 0, '', 'KAJHBFIKJBSAKF', 'Liabilities', 'N', 'N', 'N', 'N', 1, '2026-03-18 21:15:17', '2026-03-18 21:15:17', 'N'),
(14, 0, '', 'KJAHFIBAJSBF', 'Liabilities', 'N', 'N', 'N', 'N', 1, '2026-03-18 21:15:19', '2026-03-18 21:15:19', 'N'),
(15, 0, '', 'KJDNSF', 'Liabilities', 'N', 'N', 'N', 'N', 1, '2026-03-18 21:27:58', '2026-03-18 21:27:58', 'N'),
(16, 0, '', 'SDGDSG', 'Liabilities', 'N', 'N', 'N', 'N', 1, '2026-03-18 21:27:59', '2026-03-18 21:27:59', 'N'),
(17, 0, '', 'ASG', 'Liabilities', 'N', 'N', 'N', 'N', 1, '2026-03-18 21:28:01', '2026-03-18 21:28:01', 'N'),
(18, 0, '', 'KASNSO;FJN', 'Liabilities', 'N', 'N', 'N', 'N', 1, '2026-03-19 15:02:04', '2026-03-19 15:02:04', 'N'),
(19, 0, '', 'AKJNFKJ', 'Liabilities', 'N', 'N', 'N', 'N', 1, '2026-03-19 15:02:06', '2026-03-19 15:02:06', 'N'),
(20, 0, '', 'ASKIJSBFKJBNF', 'Liabilities', 'N', 'N', 'N', 'N', 1, '2026-03-19 15:02:08', '2026-03-19 15:02:08', 'N'),
(21, 0, '', 'KJBFKJB', 'Liabilities', 'N', 'N', 'N', 'N', 1, '2026-03-19 15:02:10', '2026-03-19 15:02:10', 'N'),
(22, 0, '', 'ASF', 'Liabilities', 'N', 'N', 'N', 'N', 1, '2026-03-19 15:31:50', '2026-03-19 15:31:50', 'N');

-- --------------------------------------------------------

--
-- Table structure for table `group_setup`
--

CREATE TABLE `group_setup` (
  `setup_id` int(10) UNSIGNED NOT NULL,
  `group_fix_id` int(10) UNSIGNED NOT NULL,
  `allowed_group_ids_json` longtext DEFAULT NULL,
  `user_id` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `group_setup`
--

INSERT INTO `group_setup` (`setup_id`, `group_fix_id`, `allowed_group_ids_json`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 3, '[19,9,17,20,8,10,6,12,13,18,14,21,15,5,16,1,11,7,4]', 1, '2026-03-19 15:16:32', '2026-03-19 15:16:32'),
(2, 1, '[19,22,9,17,20,8,10,6,12,13,18,14,21,15,5,16,1,11,7,4]', 1, '2026-03-27 18:02:29', '2026-03-27 18:02:29');

-- --------------------------------------------------------

--
-- Table structure for table `length_master`
--

CREATE TABLE `length_master` (
  `length_id` int(11) NOT NULL,
  `length_name` varchar(150) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `length_master`
--

INSERT INTO `length_master` (`length_id`, `length_name`, `user_id`, `created_at`, `updated_at`) VALUES
(2, 'TEST', 1, '2026-03-01 18:30:08', '2026-03-03 20:42:54'),
(3, 'A', 1, '2026-03-13 12:54:02', '2026-03-13 12:54:02'),
(4, 'B', 1, '2026-03-13 12:54:03', '2026-03-13 12:54:03'),
(5, 'C', 1, '2026-03-13 12:54:04', '2026-03-13 12:54:04'),
(6, 'D', 1, '2026-03-13 12:54:05', '2026-03-13 12:54:05'),
(7, 'E', 1, '2026-03-13 12:54:06', '2026-03-13 12:54:06'),
(8, 'F', 1, '2026-03-13 12:54:08', '2026-03-13 12:54:08'),
(9, 'G', 1, '2026-03-13 12:54:09', '2026-03-13 12:54:09'),
(10, 'H', 1, '2026-03-13 12:54:10', '2026-03-13 12:54:10'),
(11, 'J', 1, '2026-03-13 12:54:12', '2026-03-13 12:54:12'),
(12, 'K', 1, '2026-03-13 12:54:13', '2026-03-13 12:54:13'),
(13, 'L', 1, '2026-03-13 12:54:14', '2026-03-13 12:54:14'),
(14, 'M', 1, '2026-03-13 12:54:15', '2026-03-13 12:54:15'),
(15, 'N', 1, '2026-03-13 12:54:16', '2026-03-13 12:54:16'),
(16, 'O', 1, '2026-03-13 12:54:18', '2026-03-13 12:54:18'),
(17, 'P', 1, '2026-03-13 12:54:19', '2026-03-13 12:54:19'),
(18, 'Q', 1, '2026-03-13 12:54:25', '2026-03-13 12:54:25'),
(19, 'W', 1, '2026-03-13 12:54:26', '2026-03-13 12:54:26'),
(20, 'CC', 1, '2026-03-13 22:02:12', '2026-03-13 22:02:12'),
(21, 'IUHSD', 1, '2026-03-16 10:25:36', '2026-03-16 10:25:36'),
(22, 'DSAG', 1, '2026-03-16 10:25:37', '2026-03-16 10:25:37'),
(23, 'SDG', 1, '2026-03-16 10:25:38', '2026-03-16 10:25:38'),
(24, 'LJDF', 1, '2026-03-16 10:26:53', '2026-03-16 10:26:53'),
(25, 'DSGGH', 1, '2026-03-16 10:26:55', '2026-03-16 10:26:55'),
(26, 'FASG', 1, '2026-03-16 10:26:55', '2026-03-16 10:26:55');

-- --------------------------------------------------------

--
-- Table structure for table `line_master`
--

CREATE TABLE `line_master` (
  `line_id` int(11) NOT NULL,
  `line_name` varchar(150) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `line_master`
--

INSERT INTO `line_master` (`line_id`, `line_name`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'TEST', 1, '2026-03-01 11:07:53', '2026-03-03 20:42:54'),
(2, 'MAIN', 1, '2026-03-03 10:32:40', '2026-03-03 20:42:54'),
(3, 'LINE', 1, '2026-03-03 20:25:26', '2026-03-03 20:42:54'),
(4, 'AB', 1, '2026-03-13 12:48:08', '2026-03-13 12:50:39'),
(5, 'BB', 1, '2026-03-13 12:48:10', '2026-03-13 12:50:56'),
(6, 'C', 1, '2026-03-13 12:48:11', '2026-03-13 12:48:11'),
(7, 'D', 1, '2026-03-13 12:48:12', '2026-03-13 12:48:12'),
(8, 'E', 1, '2026-03-13 12:48:13', '2026-03-13 12:48:13'),
(9, 'FA', 1, '2026-03-13 12:48:14', '2026-03-13 12:48:58'),
(10, 'J', 1, '2026-03-13 12:48:18', '2026-03-13 12:48:18'),
(11, 'K', 1, '2026-03-13 12:48:20', '2026-03-13 12:48:20'),
(12, 'L', 1, '2026-03-13 12:48:21', '2026-03-13 12:48:21'),
(13, 'M', 1, '2026-03-13 12:48:23', '2026-03-13 12:48:23'),
(14, 'N', 1, '2026-03-13 12:48:24', '2026-03-13 12:48:24'),
(15, 'O', 1, '2026-03-13 12:48:25', '2026-03-13 12:48:25'),
(16, 'P', 1, '2026-03-13 12:48:26', '2026-03-13 12:48:26'),
(17, 'Q', 1, '2026-03-13 12:48:28', '2026-03-13 12:48:28'),
(18, 'R', 1, '2026-03-13 12:48:30', '2026-03-13 12:48:30'),
(19, 'S', 1, '2026-03-13 12:48:31', '2026-03-13 12:48:31'),
(20, 'AFFD', 1, '2026-03-14 13:40:10', '2026-03-14 13:40:10'),
(21, 'IUHF', 1, '2026-03-16 10:22:18', '2026-03-16 10:22:18'),
(22, 'DG', 1, '2026-03-16 10:22:19', '2026-03-16 10:22:19'),
(23, 'DSG', 1, '2026-03-16 10:22:20', '2026-03-16 10:22:20'),
(24, 'LJSF', 1, '2026-03-16 10:24:00', '2026-03-16 10:24:00'),
(25, 'DSFGGF', 1, '2026-03-16 10:24:01', '2026-03-16 10:24:01'),
(26, 'DGGF', 1, '2026-03-16 10:24:02', '2026-03-16 10:24:02'),
(27, 'GG', 1, '2026-03-16 10:24:10', '2026-03-16 10:24:10'),
(28, 'KJSNDF', 1, '2026-03-16 10:26:46', '2026-03-16 10:26:46'),
(30, 'SG', 1, '2026-03-16 10:26:48', '2026-03-16 10:26:48'),
(31, 'KJDF', 1, '2026-03-16 13:04:41', '2026-03-16 13:04:41'),
(32, 'ASFG', 1, '2026-03-16 13:04:42', '2026-03-16 13:04:42'),
(33, 'SDGFSG', 1, '2026-03-16 13:04:43', '2026-03-16 13:04:43'),
(34, 'DS', 1, '2026-03-16 13:04:44', '2026-03-16 13:04:44');

-- --------------------------------------------------------

--
-- Table structure for table `master_data_entry`
--

CREATE TABLE `master_data_entry` (
  `entry_id` int(10) UNSIGNED NOT NULL,
  `field_key` varchar(120) NOT NULL,
  `group_id` int(10) UNSIGNED DEFAULT NULL,
  `zone_area` varchar(180) DEFAULT NULL,
  `fill_flag` enum('ALL','EMPTY','FILL') NOT NULL DEFAULT 'ALL',
  `party_flag` enum('BYR','SLR','BOTH') NOT NULL DEFAULT 'BOTH',
  `user_id` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `master_data_print`
--

CREATE TABLE `master_data_print` (
  `print_id` int(10) UNSIGNED NOT NULL,
  `print_mode` enum('PARTY','STATION','STATE','GROUP') NOT NULL DEFAULT 'PARTY',
  `selected_ids_json` longtext DEFAULT NULL,
  `deal_ids_json` longtext DEFAULT NULL,
  `deal_type` enum('BUYER','SELLER','BOTH') NOT NULL DEFAULT 'BUYER',
  `format_type` enum('FORMAT1','FORMAT2','FORMAT3') NOT NULL DEFAULT 'FORMAT1',
  `user_id` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `narration`
--

CREATE TABLE `narration` (
  `narration_id` int(10) UNSIGNED NOT NULL,
  `description` varchar(255) NOT NULL,
  `narration_type` varchar(64) NOT NULL,
  `receipt_payment` enum('Receipt','Payment','Both') NOT NULL DEFAULT 'Both',
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `narration`
--

INSERT INTO `narration` (`narration_id`, `description`, `narration_type`, `receipt_payment`, `user_id`, `created_at`, `updated_at`) VALUES
(4, 'TEST', 'Cash Bank Line - 1', 'Receipt', 1, '2026-03-13 13:51:51', '2026-03-13 13:51:51'),
(6, 'SF', 'Cash Bank Line - 1', 'Receipt', 1, '2026-03-14 13:50:21', '2026-03-14 13:50:21'),
(7, 'UYGF', 'All', 'Both', 1, '2026-03-23 14:51:40', '2026-03-23 14:51:40'),
(8, 'SAFDH', 'Cash Bank Line - 1', 'Receipt', 1, '2026-03-23 14:51:45', '2026-03-23 14:51:45'),
(9, 'KJFB', 'Cash Bank Line - 1', 'Payment', 1, '2026-03-23 14:52:07', '2026-03-23 14:52:07'),
(10, 'JHDF', 'Cash Receipt Line - 1', 'Payment', 1, '2026-03-23 14:54:06', '2026-03-23 14:54:06');

-- --------------------------------------------------------

--
-- Table structure for table `note_master`
--

CREATE TABLE `note_master` (
  `note_id` int(10) UNSIGNED NOT NULL,
  `note_description` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `applicable_sauda` tinyint(1) NOT NULL DEFAULT 1,
  `applicable_unloading` tinyint(1) NOT NULL DEFAULT 1,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `note_master`
--

INSERT INTO `note_master` (`note_id`, `note_description`, `sort_order`, `applicable_sauda`, `applicable_unloading`, `user_id`, `created_at`, `updated_at`) VALUES
(3, 'NOTE', 0, 1, 1, 1, '2026-03-01 13:19:06', '2026-03-03 20:42:54'),
(4, 'TEST', 0, 1, 1, 1, '2026-03-13 13:46:02', '2026-03-13 13:46:02'),
(6, 'SDGV', 0, 1, 1, 1, '2026-03-13 21:15:50', '2026-03-15 18:32:03'),
(8, 'SF', 0, 1, 1, 1, '2026-03-15 13:16:59', '2026-03-15 13:16:59');

-- --------------------------------------------------------

--
-- Table structure for table `party`
--

CREATE TABLE `party` (
  `party_name` varchar(120) NOT NULL,
  `area` varchar(120) DEFAULT NULL,
  `city` varchar(120) NOT NULL,
  `state` varchar(120) DEFAULT NULL,
  `pin_code` varchar(20) DEFAULT NULL,
  `contact_no` varchar(30) DEFAULT NULL,
  `gst_no` varchar(30) DEFAULT NULL,
  `pan_no` varchar(20) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `party_id` int(11) NOT NULL,
  `opening_balance` decimal(15,2) DEFAULT 0.00,
  `balance_type` enum('DB','CR') DEFAULT 'DB',
  `user_id` int(11) NOT NULL,
  `party_role_byr` tinyint(1) NOT NULL DEFAULT 1,
  `party_role_slr` tinyint(1) NOT NULL DEFAULT 1,
  `party_role_sb` tinyint(1) NOT NULL DEFAULT 0,
  `party_role_bb` tinyint(1) NOT NULL DEFAULT 0,
  `address1` varchar(200) DEFAULT NULL,
  `address2` varchar(200) DEFAULT NULL,
  `address3` varchar(200) DEFAULT NULL,
  `address4` varchar(200) DEFAULT NULL,
  `group_name` varchar(120) DEFAULT NULL,
  `category` varchar(120) DEFAULT NULL,
  `zone_area` varchar(120) DEFAULT NULL,
  `sms_ac` varchar(30) DEFAULT NULL,
  `mobile_no` varchar(30) DEFAULT NULL,
  `sms_ow` varchar(30) DEFAULT NULL,
  `proprietor` varchar(120) DEFAULT NULL,
  `fssai_no` varchar(40) DEFAULT NULL,
  `lock_date` date DEFAULT NULL,
  `party_type` varchar(30) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `multiple_sms_session` tinyint(1) NOT NULL DEFAULT 0,
  `cr_day` int(11) NOT NULL DEFAULT 0,
  `comp_group` varchar(120) DEFAULT NULL,
  `co_name` varchar(120) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `office_address1` varchar(200) DEFAULT NULL,
  `office_address2` varchar(200) DEFAULT NULL,
  `office_address3` varchar(200) DEFAULT NULL,
  `office_city` varchar(120) DEFAULT NULL,
  `office_state` varchar(120) DEFAULT NULL,
  `office_pin` varchar(20) DEFAULT NULL,
  `office_phone` varchar(30) DEFAULT NULL,
  `office_mobile` varchar(30) DEFAULT NULL,
  `wp1` varchar(30) DEFAULT NULL,
  `wp2` varchar(30) DEFAULT NULL,
  `wp3` varchar(30) DEFAULT NULL,
  `wp4` varchar(30) DEFAULT NULL,
  `sms_reg` tinyint(1) NOT NULL DEFAULT 0,
  `wp_reg` tinyint(1) NOT NULL DEFAULT 0,
  `email_reg` tinyint(1) NOT NULL DEFAULT 0,
  `default_product_id` int(11) DEFAULT NULL,
  `default_brand_id` int(11) DEFAULT NULL,
  `deals_ids` varchar(500) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `party`
--

INSERT INTO `party` (`party_name`, `area`, `city`, `state`, `pin_code`, `contact_no`, `gst_no`, `pan_no`, `email`, `party_id`, `opening_balance`, `balance_type`, `user_id`, `party_role_byr`, `party_role_slr`, `party_role_sb`, `party_role_bb`, `address1`, `address2`, `address3`, `address4`, `group_name`, `category`, `zone_area`, `sms_ac`, `mobile_no`, `sms_ow`, `proprietor`, `fssai_no`, `lock_date`, `party_type`, `is_active`, `multiple_sms_session`, `cr_day`, `comp_group`, `co_name`, `remarks`, `office_address1`, `office_address2`, `office_address3`, `office_city`, `office_state`, `office_pin`, `office_phone`, `office_mobile`, `wp1`, `wp2`, `wp3`, `wp4`, `sms_reg`, `wp_reg`, `email_reg`, `default_product_id`, `default_brand_id`, `deals_ids`) VALUES
('NAKUL JOSHI', 'Annapurna', 'INDORE', 'MADHYA PRADESH', '452009', '9755424477', '123456789', '123456789', 'nakuljoshi1990@gmail.com', 24, 100000.00, 'DB', 1, 1, 1, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '0000-00-00', '', 1, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0, 0, ''),
('SOFTTRACK', 'Annapurna', 'INDORE', 'MADHYA PRADESH', '452009', '9755424477', '123456789', '123456789', 'nakuljoshi1990@gmail.com', 25, 1000000.00, 'DB', 1, 1, 1, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '0000-00-00', '', 1, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0, 0, ''),
('TEST', 'Lokmanya', 'INDORE', 'MADHYA PRADESH', '452009', '9755424477', '123456789', '123456789', 'nakuljoshi1990@gmail.com', 30, 150000.00, 'CR', 1, 1, 1, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '0000-00-00', '', 1, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0, 0, '5,19,21'),
('MAIN', 'Lokmanya', 'INDORE', 'MADHYA PRADESH', '452009', '9755424477', '23456789', '123456789', 'nakuljoshi112003@gmail.com', 32, 15000.00, 'CR', 1, 1, 1, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '0000-00-00', '', 1, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0, 0, ''),
('ASF', '', 'K', 'BIHAR', '', '', '', '', '', 38, 0.00, 'DB', 1, 1, 1, 0, 0, '', '', '', '', 'ASF', '', '', '', '', '', '', '', '0000-00-00', '', 1, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0, 0, ''),
('DIHFO', '', 'DHAR', 'MADHYA PRADESH', '', '', '', '', '', 39, 0.00, 'DB', 1, 1, 1, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '0000-00-00', '', 1, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0, 0, ''),
('AFGDSGVDS', '', 'SAGAR', 'MADHYA PRADESH', '', '', '', '', '', 40, 0.00, 'DB', 1, 1, 1, 0, 0, '', '', '', '', 'AKJNFKJ', '', '', '', '', '', '', '', '2025-06-05', '', 1, 0, 0, '', '', '', '', '', '', 'SAGAR', 'MADHYA PRADESH', '', '', '', '', '', '', '', 0, 0, 0, 0, 0, ''),
('DSFDS', '', 'M', 'BIHAR', '', '', '', '', '', 42, 0.00, 'DB', 1, 1, 1, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '0000-00-00', '', 1, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0, 0, ''),
('SDAGDG', '', 'A', 'MADHYA PRADESH', '', '', '', '', '', 43, 0.00, 'DB', 1, 1, 1, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '0000-00-00', '', 1, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0, 0, ''),
('DSGG', '', 'ADFF', 'ANDAMAN AND NICOBAR', '', '', '', '', '', 44, 0.00, 'DB', 1, 1, 1, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '0000-00-00', '', 1, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0, 0, ''),
('DSGDG', '', 'D', 'ANDAMAN AND NICOBAR', '', '', '', '', '', 45, 0.00, 'DB', 1, 1, 1, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '0000-00-00', '', 1, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0, 0, ''),
('KJDFJ', '', 'P', 'BIHAR', '', '', '', '', '', 46, 0.00, 'DB', 1, 1, 1, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '0000-00-00', '', 1, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0, 0, ''),
('SDGFSDG', '', 'R', 'BIHAR', '', '', '', '', '', 47, 0.00, 'DB', 1, 1, 1, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '0000-00-00', '', 1, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0, 0, ''),
('SDGSDG', '', 'INDORE', 'MADHYA PRADESH', '', '', '', '', '', 48, 0.00, 'DB', 1, 1, 1, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '0000-00-00', '', 1, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0, 0, ''),
('IJBGF', '', 'A', 'MADHYA PRADESH', '', '', '', '', '', 49, 0.00, 'DB', 1, 1, 1, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '0000-00-00', '', 1, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `party_bank_detail`
--

CREATE TABLE `party_bank_detail` (
  `bank_id` int(11) NOT NULL,
  `party_id` int(11) NOT NULL,
  `row_no` int(11) NOT NULL DEFAULT 1,
  `ac_holder` varchar(150) DEFAULT NULL,
  `ac_number` varchar(60) DEFAULT NULL,
  `bank_name` varchar(150) DEFAULT NULL,
  `ifsc_code` varchar(30) DEFAULT NULL,
  `pin_code` varchar(20) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `party_bank_detail`
--

INSERT INTO `party_bank_detail` (`bank_id`, `party_id`, `row_no`, `ac_holder`, `ac_number`, `bank_name`, `ifsc_code`, `pin_code`, `user_id`, `created_at`) VALUES
(3, 32, 1, 'SAFASFDF', 'ASD', 'AD', '1546846', '32135435', 1, '2026-03-17 17:36:33'),
(4, 40, 1, '', 'ASFFF', '', '', '', 1, '2026-04-05 16:19:02');

-- --------------------------------------------------------

--
-- Table structure for table `party_brokerage_packing_rate`
--

CREATE TABLE `party_brokerage_packing_rate` (
  `pack_rate_id` int(10) UNSIGNED NOT NULL,
  `party_id` int(11) NOT NULL,
  `packing` varchar(120) NOT NULL,
  `slr_rt` decimal(12,4) DEFAULT NULL,
  `byr_rt` decimal(12,4) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `party_brokerage_packing_rate`
--

INSERT INTO `party_brokerage_packing_rate` (`pack_rate_id`, `party_id`, `packing`, `slr_rt`, `byr_rt`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 32, 'SADDSAF', 0.0000, 0.0000, 1, '2026-03-16 15:00:36', '2026-03-16 15:00:36');

-- --------------------------------------------------------

--
-- Table structure for table `party_brokerage_rate`
--

CREATE TABLE `party_brokerage_rate` (
  `rate_id` int(10) UNSIGNED NOT NULL,
  `party_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `slr_type` enum('PERCENT','MANUAL','PACK','QUINTAL') NOT NULL DEFAULT 'PERCENT',
  `slr_rt` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `byr_type` enum('PERCENT','MANUAL','PACK','QUINTAL') NOT NULL DEFAULT 'PERCENT',
  `byr_rt` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `party_brokerage_rate`
--

INSERT INTO `party_brokerage_rate` (`rate_id`, `party_id`, `product_id`, `slr_type`, `slr_rt`, `byr_type`, `byr_rt`, `user_id`, `created_at`, `updated_at`) VALUES
(21, 25, 12, 'MANUAL', 1.0000, 'MANUAL', 1.0000, 1, '2026-03-16 14:50:36', '2026-03-16 14:50:36'),
(25, 32, 10, 'MANUAL', 0.0001, 'MANUAL', 0.0000, 1, '2026-03-16 15:00:36', '2026-03-16 15:00:36'),
(27, 24, 10, 'PERCENT', 0.0000, 'PERCENT', 0.0000, 1, '2026-03-16 18:01:31', '2026-03-16 18:01:31'),
(30, 39, 16, 'PERCENT', 0.0000, 'PERCENT', 0.0000, 1, '2026-03-21 11:59:11', '2026-03-21 11:59:11'),
(33, 30, 16, 'PERCENT', 0.0000, 'PERCENT', 0.0000, 1, '2026-03-21 11:59:46', '2026-03-21 11:59:46'),
(34, 47, 18, 'PERCENT', 0.0000, 'PERCENT', 0.0000, 1, '2026-03-21 12:02:08', '2026-03-21 12:02:08'),
(35, 41, 16, 'PERCENT', 0.0000, 'PERCENT', 0.0000, 1, '2026-03-23 16:18:49', '2026-03-23 16:18:49'),
(37, 38, 16, 'PERCENT', 0.0000, 'PERCENT', 0.0000, 1, '2026-03-27 11:59:45', '2026-03-27 11:59:45'),
(38, 40, 16, 'PERCENT', 0.0000, 'PERCENT', 0.0000, 1, '2026-04-05 15:47:24', '2026-04-05 15:47:24'),
(39, 49, 35, 'PERCENT', 0.0000, 'PERCENT', 0.0000, 1, '2026-04-05 15:47:47', '2026-04-05 15:47:47');

-- --------------------------------------------------------

--
-- Table structure for table `party_category_master`
--

CREATE TABLE `party_category_master` (
  `party_category_id` int(11) NOT NULL,
  `party_category_name` varchar(120) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `party_category_master`
--

INSERT INTO `party_category_master` (`party_category_id`, `party_category_name`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'TEST', 1, '2026-03-01 13:48:20', '2026-03-03 20:42:54'),
(2, 'MAIN', 1, '2026-03-01 13:48:25', '2026-03-03 20:42:54');

-- --------------------------------------------------------

--
-- Table structure for table `party_condition_map`
--

CREATE TABLE `party_condition_map` (
  `map_id` int(11) NOT NULL,
  `party_id` int(11) NOT NULL,
  `side_type` enum('PAYMENT','PACKING') NOT NULL,
  `condition_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `party_condition_map`
--

INSERT INTO `party_condition_map` (`map_id`, `party_id`, `side_type`, `condition_id`, `user_id`, `created_at`) VALUES
(5, 32, 'PAYMENT', 3, 1, '2026-03-17 17:36:33'),
(6, 32, 'PAYMENT', 5, 1, '2026-03-17 17:36:33'),
(7, 32, 'PACKING', 3, 1, '2026-03-17 17:36:33');

-- --------------------------------------------------------

--
-- Table structure for table `party_deals_map`
--

CREATE TABLE `party_deals_map` (
  `map_id` int(11) NOT NULL,
  `party_id` int(11) NOT NULL,
  `narration_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `party_deals_map`
--

INSERT INTO `party_deals_map` (`map_id`, `party_id`, `narration_id`, `user_id`, `created_at`) VALUES
(5, 30, 19, 1, '2026-03-26 18:31:03'),
(6, 30, 21, 1, '2026-03-26 18:31:03');

-- --------------------------------------------------------

--
-- Table structure for table `party_product_brand_setup`
--

CREATE TABLE `party_product_brand_setup` (
  `row_id` int(11) NOT NULL,
  `party_id` int(11) NOT NULL,
  `side_type` enum('SELLER','BUYER') NOT NULL,
  `row_no` int(11) NOT NULL DEFAULT 1,
  `product_id` int(11) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `pack` varchar(60) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `party_type_master`
--

CREATE TABLE `party_type_master` (
  `party_type_id` int(11) NOT NULL,
  `party_type_name` varchar(120) NOT NULL,
  `party_type_code` varchar(10) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `party_type_master`
--

INSERT INTO `party_type_master` (`party_type_id`, `party_type_name`, `party_type_code`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'STATE', 'ST', 1, '2026-03-01 13:55:40', '2026-03-03 20:42:54'),
(2, 'LOCAL', 'L', 1, '2026-03-01 13:55:47', '2026-03-03 20:42:54');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_name` varchar(120) NOT NULL,
  `sales_rate` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `rate` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_code` varchar(40) DEFAULT NULL,
  `material_type` varchar(40) NOT NULL DEFAULT 'FINISHED GOODS',
  `product_group` varchar(100) DEFAULT NULL,
  `default_item` tinyint(1) NOT NULL DEFAULT 0,
  `std_pack` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `pack_unit` varchar(20) DEFAULT NULL,
  `rate_type` varchar(10) NOT NULL DEFAULT 'W',
  `div_factor` decimal(12,4) NOT NULL DEFAULT 1.0000,
  `rate_range_from` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `rate_range_to` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `qty_range_from` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `qty_range_to` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `weight_range_from` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `weight_range_to` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `cursor_brand` tinyint(1) NOT NULL DEFAULT 1,
  `cursor_weight` tinyint(1) NOT NULL DEFAULT 1,
  `cursor_unit` tinyint(1) NOT NULL DEFAULT 0,
  `cursor_qty` tinyint(1) NOT NULL DEFAULT 1,
  `cursor_pack` tinyint(1) NOT NULL DEFAULT 0,
  `cursor_sp_pack` tinyint(1) NOT NULL DEFAULT 0,
  `kasar_rate` tinyint(1) NOT NULL DEFAULT 0,
  `term_type_flag` tinyint(1) NOT NULL DEFAULT 0,
  `amt_ro` char(1) NOT NULL DEFAULT 'N',
  `edit_flag` char(1) NOT NULL DEFAULT 'Y',
  `brok_byr_type` varchar(10) NOT NULL DEFAULT 'PERCENT',
  `brok_byr_rate` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `brok_slr_type` varchar(10) NOT NULL DEFAULT 'PERCENT',
  `brok_slr_rate` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `packing_compulsory` tinyint(1) NOT NULL DEFAULT 0,
  `link_with_master` tinyint(1) NOT NULL DEFAULT 0,
  `igst` decimal(7,4) NOT NULL DEFAULT 0.0000,
  `cgst` decimal(7,4) NOT NULL DEFAULT 0.0000,
  `sgst` decimal(7,4) NOT NULL DEFAULT 0.0000,
  `ord_no` int(11) NOT NULL DEFAULT 0,
  `tax_pack_max` decimal(12,4) NOT NULL DEFAULT 25.0000,
  `remarks` varchar(255) DEFAULT NULL,
  `def_loading_pend` varchar(10) NOT NULL DEFAULT 'W',
  `freight_type` varchar(10) NOT NULL DEFAULT 'W'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_name`, `sales_rate`, `rate`, `product_id`, `user_id`, `item_code`, `material_type`, `product_group`, `default_item`, `std_pack`, `pack_unit`, `rate_type`, `div_factor`, `rate_range_from`, `rate_range_to`, `qty_range_from`, `qty_range_to`, `weight_range_from`, `weight_range_to`, `cursor_brand`, `cursor_weight`, `cursor_unit`, `cursor_qty`, `cursor_pack`, `cursor_sp_pack`, `kasar_rate`, `term_type_flag`, `amt_ro`, `edit_flag`, `brok_byr_type`, `brok_byr_rate`, `brok_slr_type`, `brok_slr_rate`, `packing_compulsory`, `link_with_master`, `igst`, `cgst`, `sgst`, `ord_no`, `tax_pack_max`, `remarks`, `def_loading_pend`, `freight_type`) VALUES
('RICE', 200.0000, 20.0000, 2, 1, '', 'FINISHED GOODS', '', 0, 0.0000, '', 'W', 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 1, 1, 0, 1, 0, 0, 0, 0, 'N', 'Y', 'PERCENT', 0.0000, 'PERCENT', 0.0000, 0, 0, 0.0000, 0.0000, 0.0000, 0, 25.0000, '', 'W', 'W'),
('SOYA BEAN', 500.0000, 50.0000, 5, 1, '', 'FINISHED GOODS', '', 0, 0.0000, '', 'W', 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 1, 1, 0, 1, 0, 0, 0, 0, 'N', 'Y', 'PERCENT', 0.0000, 'PERCENT', 0.0000, 0, 0, 0.0000, 0.0000, 0.0000, 0, 25.0000, '', 'W', 'W'),
('LAPTOP', 50000.0000, 50000.0000, 10, 1, '', 'FINISHED GOODS', '', 0, 0.0000, '', 'W', 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 1, 1, 0, 1, 0, 0, 0, 0, 'N', 'Y', 'PERCENT', 0.0000, 'PERCENT', 0.0000, 0, 0, 0.0000, 0.0000, 0.0000, 0, 25.0000, '', 'W', 'W'),
('MOBILE', 15000.0000, 20000.0000, 12, 1, '', 'FINISHED GOODS', '', 0, 0.0000, '', 'W', 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 1, 1, 0, 1, 0, 0, 0, 0, 'N', 'Y', 'PERCENT', 0.0000, 'PERCENT', 0.0000, 0, 0, 0.0000, 0.0000, 0.0000, 0, 25.0000, '', 'W', 'W'),
('ASF', 0.0000, 0.0000, 16, 1, '', 'FINISHED GOODS', '', 0, 0.0000, '', 'W', 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 1, 1, 0, 1, 0, 0, 0, 0, 'N', 'Y', 'PERCENT', 0.0000, 'PERCENT', 0.0000, 0, 0, 0.0000, 0.0000, 0.0000, 0, 25.0000, '', 'W', 'W'),
('ODIJF', 0.0000, 0.0000, 17, 1, '', 'FINISHED GOODS', '', 0, 0.0000, '', 'W', 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 1, 1, 0, 1, 0, 0, 0, 0, 'N', 'Y', 'PERCENT', 0.0000, 'PERCENT', 0.0000, 0, 0, 0.0000, 0.0000, 0.0000, 0, 25.0000, '', 'W', 'W'),
('ASFF', 0.0000, 0.0000, 18, 1, '', 'FINISHED GOODS', '', 0, 0.0000, '', 'W', 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 1, 1, 0, 1, 0, 0, 0, 0, 'N', 'Y', 'PERCENT', 0.0000, 'PERCENT', 0.0000, 0, 0, 0.0000, 0.0000, 0.0000, 0, 25.0000, '', 'W', 'W'),
('ASFSAF', 0.0000, 0.0000, 19, 1, '', 'FINISHED GOODS', '', 0, 0.0000, '', 'W', 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 1, 1, 0, 1, 0, 0, 0, 0, 'N', 'Y', 'PERCENT', 0.0000, 'PERCENT', 0.0000, 0, 0, 0.0000, 0.0000, 0.0000, 0, 25.0000, '', 'W', 'W'),
('SAFSAF', 0.0000, 0.0000, 20, 1, '', 'FINISHED GOODS', '', 0, 0.0000, '', 'W', 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 1, 1, 0, 1, 0, 0, 0, 0, 'N', 'Y', 'PERCENT', 0.0000, 'PERCENT', 0.0000, 0, 0, 0.0000, 0.0000, 0.0000, 0, 25.0000, '', 'W', 'W'),
('ASFSAFSAF', 0.0000, 0.0000, 21, 1, '', 'FINISHED GOODS', '', 0, 0.0000, '', 'W', 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 1, 1, 0, 1, 0, 0, 0, 0, 'N', 'Y', 'PERCENT', 0.0000, 'PERCENT', 0.0000, 0, 0, 0.0000, 0.0000, 0.0000, 0, 25.0000, '', 'W', 'W'),
('DSGDFGFDG', 0.0000, 0.0000, 22, 1, '', 'FINISHED GOODS', '', 0, 0.0000, '', 'W', 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 1, 1, 0, 1, 0, 0, 0, 0, 'N', 'Y', 'PERCENT', 0.0000, 'PERCENT', 0.0000, 0, 0, 0.0000, 0.0000, 0.0000, 0, 25.0000, '', 'W', 'W'),
('FZVFDVZFD', 0.0000, 0.0000, 23, 1, '', 'FINISHED GOODS', '', 0, 0.0000, '', 'W', 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 1, 1, 0, 1, 0, 0, 0, 0, 'N', 'Y', 'PERCENT', 0.0000, 'PERCENT', 0.0000, 0, 0, 0.0000, 0.0000, 0.0000, 0, 25.0000, '', 'W', 'W'),
('IUDHFHI', 0.0000, 0.0000, 24, 1, '', 'FINISHED GOODS', '', 0, 0.0000, '', 'W', 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 1, 1, 0, 1, 0, 0, 0, 0, 'N', 'Y', 'PERCENT', 0.0000, 'PERCENT', 0.0000, 0, 0, 0.0000, 0.0000, 0.0000, 0, 25.0000, '', 'W', 'W'),
('DASFSDGF', 0.0000, 0.0000, 25, 1, '', 'FINISHED GOODS', '', 0, 0.0000, '', 'W', 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 1, 1, 0, 1, 0, 0, 0, 0, 'N', 'Y', 'PERCENT', 0.0000, 'PERCENT', 0.0000, 0, 0, 0.0000, 0.0000, 0.0000, 0, 25.0000, '', 'W', 'W'),
('AHGVFUH', 0.0000, 0.0000, 26, 1, '', 'FINISHED GOODS', '', 0, 0.0000, '', 'W', 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 1, 1, 0, 1, 0, 0, 0, 0, 'N', 'Y', 'PERCENT', 0.0000, 'PERCENT', 0.0000, 1, 0, 0.0000, 0.0000, 0.0000, 0, 25.0000, '', 'W', 'W'),
('JSDBFIB', 0.0000, 0.0000, 27, 1, '', 'FINISHED GOODS', '', 0, 0.0000, '', 'W', 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 1, 1, 0, 1, 0, 0, 0, 0, 'N', 'Y', 'PERCENT', 0.0000, 'PERCENT', 0.0000, 0, 0, 0.0000, 0.0000, 0.0000, 0, 25.0000, '', 'W', 'W'),
('IJHDSIKFB', 0.0000, 0.0000, 28, 1, '', 'FINISHED GOODS', '', 0, 0.0000, '', 'W', 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 1, 1, 0, 1, 0, 0, 0, 0, 'N', 'Y', 'PERCENT', 0.0000, 'PERCENT', 0.0000, 0, 0, 0.0000, 0.0000, 0.0000, 0, 25.0000, '', 'W', 'W'),
('IHSDIFU', 0.0000, 0.0000, 29, 1, '', 'FINISHED GOODS', '', 0, 0.0000, '', 'W', 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 1, 1, 0, 1, 0, 0, 0, 0, 'N', 'Y', 'PERCENT', 0.0000, 'PERCENT', 0.0000, 0, 0, 0.0000, 0.0000, 0.0000, 0, 25.0000, '', 'W', 'W'),
('SAFF', 0.0000, 0.0000, 30, 1, 'KJF', 'FINISHED GOODS', '', 0, 0.0000, '', 'W', 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 1, 1, 0, 1, 0, 0, 0, 0, 'N', 'Y', 'PERCENT', 0.0000, 'PERCENT', 0.0000, 0, 0, 0.0000, 0.0000, 0.0000, 0, 25.0000, '', 'W', 'W'),
('UJADHBF', 0.0000, 0.0000, 31, 1, 'SAF', 'FINISHED GOODS', '', 0, 0.0000, '', 'W', 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 1, 1, 0, 1, 0, 0, 0, 0, 'N', 'Y', 'PERCENT', 0.0000, 'PERCENT', 0.0000, 0, 0, 0.0000, 0.0000, 0.0000, 0, 25.0000, '', 'W', 'W'),
('SDF', 0.0000, 0.0000, 32, 1, 'KHJFB', 'FINISHED GOODS', '', 0, 0.0000, '', 'W', 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 1, 1, 0, 1, 0, 0, 0, 0, 'N', 'Y', 'PERCENT', 0.0000, 'PERCENT', 0.0000, 0, 0, 0.0000, 0.0000, 0.0000, 0, 25.0000, '', 'W', 'W'),
('ASFASF', 0.0000, 0.0000, 33, 1, 'UF', 'FINISHED GOODS', '', 0, 0.0000, '', 'W', 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 1, 1, 0, 1, 0, 0, 0, 0, 'N', 'Y', 'PERCENT', 0.0000, 'PERCENT', 0.0000, 0, 0, 0.0000, 0.0000, 0.0000, 0, 25.0000, '', 'W', 'W'),
('SAF', 0.0000, 0.0000, 34, 1, 'DSIJUBV', 'FINISHED GOODS', '', 0, 0.0000, '', 'W', 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 1, 1, 0, 1, 0, 0, 0, 0, 'N', 'Y', 'PERCENT', 0.0000, 'PERCENT', 0.0000, 0, 0, 0.0000, 0.0000, 0.0000, 0, 25.0000, '', 'W', 'W'),
('ADF', 0.0000, 0.0000, 35, 1, 'IUHG', 'FINISHED GOODS', 'DAF', 0, 0.0000, 'DAF', 'W', 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 1, 1, 0, 1, 0, 0, 0, 0, 'N', 'Y', 'PERCENT', 0.0000, 'PERCENT', 0.0000, 0, 0, 0.0000, 0.0000, 0.0000, 0, 25.0000, '', 'W', 'W'),
('JGF', 0.0000, 0.0000, 36, 1, 'OISDJF', 'FINISHED GOODS', '', 0, 0.0000, '', 'W', 1.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 0.0000, 1, 1, 0, 1, 0, 0, 0, 0, 'N', 'Y', 'PERCENT', 0.0000, 'PERCENT', 0.0000, 0, 0, 0.0000, 0.0000, 0.0000, 0, 25.0000, '', 'W', 'W');

-- --------------------------------------------------------

--
-- Table structure for table `product_brand`
--

CREATE TABLE `product_brand` (
  `map_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_brand`
--

INSERT INTO `product_brand` (`map_id`, `product_id`, `brand_id`, `user_id`, `created_at`) VALUES
(2, 35, 9, 1, '2026-04-02 12:53:56'),
(3, 35, 6, 1, '2026-04-02 12:53:56'),
(4, 35, 10, 1, '2026-04-02 12:53:56'),
(5, 35, 16, 1, '2026-04-02 12:53:56'),
(6, 35, 4, 1, '2026-04-02 12:53:56');

-- --------------------------------------------------------

--
-- Table structure for table `product_group_master`
--

CREATE TABLE `product_group_master` (
  `product_group_id` int(11) NOT NULL,
  `product_group_name` varchar(150) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_group_master`
--

INSERT INTO `product_group_master` (`product_group_id`, `product_group_name`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'TEST', 1, '2026-03-01 12:51:16', '2026-03-03 20:42:54'),
(2, 'OIHF', 1, '2026-03-21 11:33:43', '2026-03-21 11:33:43'),
(3, 'ASFADF', 1, '2026-03-21 11:33:44', '2026-03-21 11:33:44'),
(4, 'JSDHBF', 1, '2026-03-21 11:33:52', '2026-03-21 11:33:52'),
(5, 'IUSDHF', 1, '2026-03-21 11:33:54', '2026-03-21 11:33:54'),
(6, 'KJDBF', 1, '2026-03-21 11:33:58', '2026-03-21 11:33:58'),
(7, 'DF', 1, '2026-03-21 11:34:00', '2026-03-21 11:34:00'),
(8, 'KJF', 1, '2026-03-21 11:34:04', '2026-03-21 11:34:04'),
(9, 'JHSDF', 1, '2026-03-21 11:34:07', '2026-03-21 11:34:07'),
(10, 'JHD', 1, '2026-03-21 11:34:11', '2026-03-21 11:34:11'),
(11, 'KHJDSBF', 1, '2026-03-21 11:34:12', '2026-03-21 11:34:12'),
(12, 'LDKJF', 1, '2026-03-21 11:34:13', '2026-03-21 11:34:13'),
(13, 'LDKSNF', 1, '2026-03-21 11:34:14', '2026-03-21 11:34:14'),
(14, 'KJSDNF', 1, '2026-03-21 11:34:16', '2026-03-21 11:34:16'),
(15, 'OSDKHF', 1, '2026-03-21 11:34:17', '2026-03-21 11:34:17'),
(16, 'KSDFO', 1, '2026-03-21 11:34:18', '2026-03-21 11:34:18'),
(17, 'OLDSJF', 1, '2026-03-21 11:34:19', '2026-03-21 11:34:19'),
(18, 'OADJFOI', 1, '2026-03-21 11:34:20', '2026-03-21 11:34:20'),
(19, 'OSDKJF', 1, '2026-03-21 11:34:22', '2026-03-21 11:34:22'),
(20, 'OSDKF', 1, '2026-03-21 11:34:23', '2026-03-21 11:34:23'),
(21, 'ODSIJF', 1, '2026-03-21 11:34:24', '2026-03-21 11:34:24');

-- --------------------------------------------------------

--
-- Table structure for table `product_packing_detail`
--

CREATE TABLE `product_packing_detail` (
  `pack_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `row_no` int(11) NOT NULL DEFAULT 1,
  `packing` varchar(60) NOT NULL,
  `byr_rt` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `slr_rt` decimal(12,4) NOT NULL DEFAULT 0.0000,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_type_master`
--

CREATE TABLE `product_type_master` (
  `product_type_id` int(11) NOT NULL,
  `description` varchar(150) NOT NULL,
  `material_type` varchar(60) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_type_master`
--

INSERT INTO `product_type_master` (`product_type_id`, `description`, `material_type`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'TEST', 'Raw Material', 1, '2026-03-01 13:00:37', '2026-03-03 20:42:54'),
(2, 'AF', 'Consumable Material', 1, '2026-03-23 15:26:48', '2026-03-23 15:26:48'),
(3, 'ASFUHF', 'Finished Goods', 1, '2026-03-23 15:26:53', '2026-03-23 15:26:53');

-- --------------------------------------------------------

--
-- Table structure for table `sales_items`
--

CREATE TABLE `sales_items` (
  `id` int(11) NOT NULL,
  `entry_no` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `rate` decimal(10,2) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_items`
--

INSERT INTO `sales_items` (`id`, `entry_no`, `product_id`, `product_name`, `quantity`, `rate`, `amount`, `user_id`) VALUES
(0, 1, 0, 'Mobile', 2.00, 1500.00, 3000.00, 1),
(0, 1, 0, 'Laptop', 3.00, 50000.00, 150000.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sales_master`
--

CREATE TABLE `sales_master` (
  `entry_no` int(11) NOT NULL,
  `entry_date` date NOT NULL,
  `party_id` int(11) NOT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `grand_total` decimal(10,2) NOT NULL,
  `payment_mode` enum('Cash','Credit') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_master`
--

INSERT INTO `sales_master` (`entry_no`, `entry_date`, `party_id`, `remark`, `grand_total`, `payment_mode`, `created_at`, `user_id`) VALUES
(1, '2026-02-17', 30, 'Sold', 3000.00, 'Credit', '2026-02-17 16:54:00', 2),
(1, '2026-02-17', 32, '', 150000.00, 'Credit', '2026-02-17 16:57:03', 3);

-- --------------------------------------------------------

--
-- Table structure for table `sku_unit`
--

CREATE TABLE `sku_unit` (
  `sku_id` int(10) UNSIGNED NOT NULL,
  `sku_name` varchar(150) NOT NULL,
  `sku_symbol` varchar(30) NOT NULL,
  `no_of_decimals` tinyint(3) UNSIGNED NOT NULL DEFAULT 2,
  `conversion_unit` varchar(150) DEFAULT NULL,
  `unit_symbol` varchar(30) DEFAULT NULL,
  `conv_type` enum('*','/') NOT NULL DEFAULT '*',
  `conv_value` decimal(18,4) NOT NULL DEFAULT 1.0000,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sku_unit`
--

INSERT INTO `sku_unit` (`sku_id`, `sku_name`, `sku_symbol`, `no_of_decimals`, `conversion_unit`, `unit_symbol`, `conv_type`, `conv_value`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'QUINTILS', 'QTL', 2, '', '', '*', 1.0000, 1, '2026-02-18 16:55:15', '2026-03-03 20:42:54'),
(2, 'KG', 'K', 2, '', '', '*', 1.0000, 1, '2026-03-01 14:02:35', '2026-03-03 20:42:54');

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

CREATE TABLE `states` (
  `state_name` varchar(50) NOT NULL,
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `state_capital` varchar(100) DEFAULT NULL,
  `state_area` decimal(10,2) DEFAULT 0.00,
  `state_type` varchar(20) DEFAULT NULL,
  `state_code_char` varchar(10) DEFAULT NULL,
  `state_code_digit` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `states`
--

INSERT INTO `states` (`state_name`, `id`, `user_id`, `state_capital`, `state_area`, `state_type`, `state_code_char`, `state_code_digit`) VALUES
('MADHYA PRADESH', 96, 1, '', 0.00, 'INTER-STATE', '', 0),
('GUJRAT', 97, 1, '1', 0.00, 'LOCAL', '1', 1),
('WEST BENGAL', 98, 1, '', 0.00, 'STATE', '', 0),
('BIHAR', 101, 1, '', 0.00, 'INTER-STATE', '', 0),
('CHANDIGARH', 105, 1, '', 0.00, 'INTER-STATE', '', 0),
('DELHI', 106, 1, '', 0.00, 'STATE', '', 0),
('HARYANA', 108, 1, '', 0.00, 'STATE', '', 0),
('HIMACHAL PRADESH', 109, 1, '', 0.00, 'LOCAL', '', 0),
('JAMMU AND KASHMIR', 110, 1, '', 0.00, 'STATE', '', 0),
('KERELA', 111, 1, '', 0.00, 'STATE', '', 0),
('LAKSHADWEEP', 112, 1, '', 0.00, 'STATE', '', 0),
('PUNJAB', 113, 1, '', 0.00, 'STATE', '', 0),
('NAGALAND', 114, 1, '', 0.00, 'STATE', '', 0),
('UTTARAKHAND', 115, 1, '', 0.00, 'STATE', '', 0),
('UTTAR PRADESH', 116, 1, '', 0.00, 'STATE', '', 0),
('ANDAMAN AND NICOBAR', 142, 1, '1', 0.00, 'INTER-STATE', '', 0),
('ORRISA', 147, 1, '', 0.00, 'LOCAL', '', 0),
('OR', 148, 1, '', 0.00, 'STATE', '', 0),
('ORRI', 149, 1, '', 0.00, 'STATE', '', 0),
('SSF', 151, 1, '', 0.00, 'INTER-STATE', '', 0),
('SAAF', 152, 1, '', 0.00, 'INTER-STATE', '', 0),
('ASD', 153, 1, '', 0.00, 'LOCAL', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `term_type`
--

CREATE TABLE `term_type` (
  `term_type_id` int(11) NOT NULL,
  `description` varchar(150) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `term_type`
--

INSERT INTO `term_type` (`term_type_id`, `description`, `is_default`, `user_id`) VALUES
(1, 'SPOT', 1, 1),
(3, 'SD', 0, 1),
(4, 'A', 0, 1),
(5, 'B', 0, 1),
(6, 'C', 0, 1),
(7, 'D', 0, 1),
(8, 'F', 0, 1),
(9, 'G', 0, 1),
(10, 'H', 0, 1),
(11, 'J', 0, 1),
(12, 'K', 0, 1),
(13, 'L', 0, 1),
(14, 'Q', 0, 1),
(15, 'W', 0, 1),
(16, 'E', 0, 1),
(17, 'R', 0, 1),
(18, 'T', 0, 1),
(19, 'Y', 0, 1),
(20, 'U', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `transport`
--

CREATE TABLE `transport` (
  `transport_id` int(10) UNSIGNED NOT NULL,
  `transport_name` varchar(180) NOT NULL,
  `line_name` varchar(180) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `station` varchar(120) DEFAULT NULL,
  `state_name` varchar(120) DEFAULT NULL,
  `pin_code` varchar(12) DEFAULT NULL,
  `contact_person` varchar(120) DEFAULT NULL,
  `phone_office` varchar(30) DEFAULT NULL,
  `mobile` varchar(30) DEFAULT NULL,
  `email` varchar(180) DEFAULT NULL,
  `pan` varchar(20) DEFAULT NULL,
  `other_info` varchar(255) DEFAULT NULL,
  `applicable_divisions` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transport`
--

INSERT INTO `transport` (`transport_id`, `transport_name`, `line_name`, `address`, `address1`, `city_id`, `station`, `state_name`, `pin_code`, `contact_person`, `phone_office`, `mobile`, `email`, `pan`, `other_info`, `applicable_divisions`, `user_id`, `created_at`, `updated_at`) VALUES
(3, 'ASF', 'AB, AFFD, ASFG, BB, C, D, DG, DGGF, DS, DSFGGF, DSG, E, FA, GG, IUHF, J, K, KJDF, KJSNDF, L, LINE, LJSF, M, MAIN, N, O, P, Q, R, S, SDGFSG, SG, TEST', '', '', 94, 'C, E, F, J, K, M, P, Q, R, SAF', 'BIHAR', '', '', '', '', '', '', '', '', 1, '2026-03-16 08:27:29', '2026-03-18 14:41:47'),
(4, 'ASFF', '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', 1, '2026-03-16 18:59:35', '2026-03-16 18:59:35'),
(5, 'ASFFF', '', '', '', 93, 'A, ADFF, ASDF, ASF, B, C, D, DHAR, E, F, INDORE, J, K, M, N, P, Q, R, SAF, SAGAR, SDA, TEST, UJJAIN', 'MADHYA PRADESH, ANDAMAN AND NICOBAR, HARYANA, DELHI, BIHAR', '', '', '', '', '', '', '', '', 1, '2026-03-16 19:02:23', '2026-03-16 19:02:23'),
(6, 'KJSBF', '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', 1, '2026-03-18 21:34:30', '2026-03-18 21:34:30'),
(7, 'DSGG', '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', 1, '2026-03-18 21:34:32', '2026-03-18 21:34:32'),
(8, 'SDGSDG', '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', 1, '2026-03-18 21:34:34', '2026-03-18 21:34:34'),
(9, 'SDGDSGSDGDSG', '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', 1, '2026-03-18 21:34:36', '2026-03-18 21:34:36'),
(10, 'SDGDSGSDG', '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', 1, '2026-03-18 21:34:37', '2026-03-18 21:34:37'),
(11, 'GDSGSDG', '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', 1, '2026-03-18 21:34:38', '2026-03-18 21:34:38'),
(12, 'DGSDGSDG', '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', 1, '2026-03-18 21:34:40', '2026-03-18 21:34:40'),
(13, 'SDG', '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', 1, '2026-03-18 21:34:43', '2026-03-18 21:34:43'),
(14, 'ASDGVSDG', 'AB, AFFD, ASFG, BB, C, D, DG, DGGF, DS, DSFGGF, DSG, E, FA, GG, IUHF, J, K, KJDF, KJSNDF, L, LINE, LJSF, M, MAIN, N, O, P, Q, R, S, SDGFSG, SG, TEST', '', '', 115, 'ASDF', 'HARYANA', '', '', '', '', '', '', '', '', 1, '2026-03-18 21:34:45', '2026-03-27 00:18:14'),
(15, 'DASGDSG', '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', 1, '2026-03-18 21:34:47', '2026-03-18 21:34:47'),
(16, 'SDGSDGSDG', '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', 1, '2026-03-18 21:34:49', '2026-03-18 21:34:49'),
(17, 'SDGDSB', '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', 1, '2026-03-18 21:34:51', '2026-03-18 21:34:51'),
(18, 'GDBFDB', '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', 1, '2026-03-18 21:34:53', '2026-03-18 21:34:53'),
(19, 'DFBFDB', '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', 1, '2026-03-18 21:34:55', '2026-03-18 21:34:55'),
(20, 'DZFBDFZBD', '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', 1, '2026-03-18 21:34:56', '2026-03-18 21:34:56'),
(21, 'DFBZFDBZDFB', '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', 1, '2026-03-18 21:34:58', '2026-03-18 21:34:58'),
(22, 'DFBFZDBFDZB', '', '', '', NULL, '', '', '', '', '', '', '', '', '', '', 1, '2026-03-18 21:34:59', '2026-03-18 21:34:59'),
(23, 'UIHGF', '', '\\', '', 93, '', '', '', '', '', '', '', '', '', '', 1, '2026-03-23 16:12:13', '2026-03-23 16:12:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account_bank_details`
--
ALTER TABLE `account_bank_details`
  ADD PRIMARY KEY (`bank_id`);

--
-- Indexes for table `account_division_balance`
--
ALTER TABLE `account_division_balance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_account_division` (`account_id`,`division_id`,`user_id`);

--
-- Indexes for table `account_master`
--
ALTER TABLE `account_master`
  ADD PRIMARY KEY (`account_id`),
  ADD UNIQUE KEY `uniq_account_user_name` (`user_id`,`account_name`);

--
-- Indexes for table `add_less_entry_module`
--
ALTER TABLE `add_less_entry_module`
  ADD PRIMARY KEY (`module_id`),
  ADD UNIQUE KEY `uq_add_less_module` (`user_id`,`module_name`);

--
-- Indexes for table `add_less_parameter_setup`
--
ALTER TABLE `add_less_parameter_setup`
  ADD PRIMARY KEY (`setup_id`),
  ADD KEY `idx_add_less_module_user` (`module_id`,`user_id`);

--
-- Indexes for table `area`
--
ALTER TABLE `area`
  ADD PRIMARY KEY (`area_id`);

--
-- Indexes for table `bank`
--
ALTER TABLE `bank`
  ADD PRIMARY KEY (`bank_id`),
  ADD KEY `idx_bank_user` (`user_id`),
  ADD KEY `idx_bank_name_user` (`user_id`,`bank_name`);

--
-- Indexes for table `brand`
--
ALTER TABLE `brand`
  ADD PRIMARY KEY (`brand_id`),
  ADD UNIQUE KEY `uq_brand_user_name` (`user_id`,`brand_name`),
  ADD KEY `idx_brand_user_order` (`user_id`,`sort_order`,`brand_name`);

--
-- Indexes for table `city`
--
ALTER TABLE `city`
  ADD PRIMARY KEY (`city_id`),
  ADD KEY `idx_city_user_state_name` (`user_id`,`state_id`,`city_name`),
  ADD KEY `idx_city_state` (`state_id`),
  ADD KEY `fk_city_district` (`district_id`);

--
-- Indexes for table `company_group`
--
ALTER TABLE `company_group`
  ADD PRIMARY KEY (`company_group_id`),
  ADD KEY `idx_company_group_user` (`user_id`),
  ADD KEY `idx_company_group_name_user` (`user_id`,`acc_name`),
  ADD KEY `idx_company_group_station_user` (`user_id`,`station`);

--
-- Indexes for table `condition_master`
--
ALTER TABLE `condition_master`
  ADD PRIMARY KEY (`condition_id`),
  ADD KEY `idx_condition_user` (`user_id`),
  ADD KEY `idx_condition_desc_user` (`user_id`,`term_description`),
  ADD KEY `idx_condition_default_user` (`user_id`,`is_default`);

--
-- Indexes for table `courier`
--
ALTER TABLE `courier`
  ADD PRIMARY KEY (`courier_id`),
  ADD KEY `idx_courier_user_name` (`user_id`,`courier_name`),
  ADD KEY `idx_courier_user_active` (`user_id`,`is_active`);

--
-- Indexes for table `deals_in_master`
--
ALTER TABLE `deals_in_master`
  ADD PRIMARY KEY (`deals_id`),
  ADD UNIQUE KEY `uq_deals_user_name` (`user_id`,`deals_name`),
  ADD KEY `idx_deals_user_name` (`user_id`,`deals_name`);

--
-- Indexes for table `district`
--
ALTER TABLE `district`
  ADD PRIMARY KEY (`district_id`),
  ADD KEY `idx_district_user_state_name` (`user_id`,`state_id`,`district_name`),
  ADD KEY `idx_district_state` (`state_id`);

--
-- Indexes for table `form_wise_book_setup`
--
ALTER TABLE `form_wise_book_setup`
  ADD PRIMARY KEY (`setup_id`),
  ADD KEY `idx_fwbs_module_user` (`module_id`,`user_id`);

--
-- Indexes for table `form_wise_entry_module`
--
ALTER TABLE `form_wise_entry_module`
  ADD PRIMARY KEY (`module_id`),
  ADD UNIQUE KEY `uq_form_wise_module` (`user_id`,`module_name`);

--
-- Indexes for table `group_master`
--
ALTER TABLE `group_master`
  ADD PRIMARY KEY (`group_id`),
  ADD KEY `idx_group_user` (`user_id`),
  ADD KEY `idx_group_name_user` (`user_id`,`group_name`),
  ADD KEY `idx_group_type_user` (`user_id`,`group_type`),
  ADD KEY `idx_group_order_user` (`user_id`,`sort_order`);

--
-- Indexes for table `group_setup`
--
ALTER TABLE `group_setup`
  ADD PRIMARY KEY (`setup_id`),
  ADD KEY `idx_group_setup_user` (`user_id`),
  ADD KEY `idx_group_setup_fix` (`user_id`,`group_fix_id`);

--
-- Indexes for table `length_master`
--
ALTER TABLE `length_master`
  ADD PRIMARY KEY (`length_id`),
  ADD UNIQUE KEY `uq_length_user_name` (`user_id`,`length_name`),
  ADD KEY `idx_length_user_name` (`user_id`,`length_name`);

--
-- Indexes for table `line_master`
--
ALTER TABLE `line_master`
  ADD PRIMARY KEY (`line_id`),
  ADD UNIQUE KEY `uq_line_user_name` (`user_id`,`line_name`),
  ADD KEY `idx_line_user_name` (`user_id`,`line_name`);

--
-- Indexes for table `master_data_entry`
--
ALTER TABLE `master_data_entry`
  ADD PRIMARY KEY (`entry_id`),
  ADD KEY `idx_mde_user` (`user_id`),
  ADD KEY `idx_mde_field_user` (`user_id`,`field_key`),
  ADD KEY `idx_mde_group_user` (`user_id`,`group_id`);

--
-- Indexes for table `master_data_print`
--
ALTER TABLE `master_data_print`
  ADD PRIMARY KEY (`print_id`),
  ADD KEY `idx_mdp_user` (`user_id`),
  ADD KEY `idx_mdp_mode_user` (`user_id`,`print_mode`);

--
-- Indexes for table `narration`
--
ALTER TABLE `narration`
  ADD PRIMARY KEY (`narration_id`),
  ADD KEY `idx_narration_user` (`user_id`),
  ADD KEY `idx_narration_desc_user` (`user_id`,`description`);

--
-- Indexes for table `note_master`
--
ALTER TABLE `note_master`
  ADD PRIMARY KEY (`note_id`),
  ADD KEY `idx_note_user` (`user_id`),
  ADD KEY `idx_note_desc_user` (`user_id`,`note_description`),
  ADD KEY `idx_note_sort_user` (`user_id`,`sort_order`);

--
-- Indexes for table `party`
--
ALTER TABLE `party`
  ADD PRIMARY KEY (`party_id`),
  ADD UNIQUE KEY `uq_party_user_name_city` (`user_id`,`party_name`,`city`),
  ADD KEY `idx_party_user` (`user_id`),
  ADD KEY `idx_party_city` (`city`);

--
-- Indexes for table `party_bank_detail`
--
ALTER TABLE `party_bank_detail`
  ADD PRIMARY KEY (`bank_id`),
  ADD KEY `idx_pbd_user_party` (`user_id`,`party_id`),
  ADD KEY `idx_pbd_party_row` (`party_id`,`row_no`);

--
-- Indexes for table `party_brokerage_packing_rate`
--
ALTER TABLE `party_brokerage_packing_rate`
  ADD PRIMARY KEY (`pack_rate_id`),
  ADD KEY `idx_pbp_user_party` (`user_id`,`party_id`),
  ADD KEY `idx_pbp_user_pack` (`user_id`,`packing`);

--
-- Indexes for table `party_brokerage_rate`
--
ALTER TABLE `party_brokerage_rate`
  ADD PRIMARY KEY (`rate_id`),
  ADD KEY `idx_pbr_user_party` (`user_id`,`party_id`),
  ADD KEY `idx_pbr_user_product` (`user_id`,`product_id`),
  ADD KEY `idx_pbr_user_party_product` (`user_id`,`party_id`,`product_id`);

--
-- Indexes for table `party_category_master`
--
ALTER TABLE `party_category_master`
  ADD PRIMARY KEY (`party_category_id`),
  ADD UNIQUE KEY `uk_party_category_user_name` (`user_id`,`party_category_name`),
  ADD KEY `idx_party_category_user` (`user_id`);

--
-- Indexes for table `party_condition_map`
--
ALTER TABLE `party_condition_map`
  ADD PRIMARY KEY (`map_id`),
  ADD UNIQUE KEY `uq_party_condition` (`party_id`,`side_type`,`condition_id`,`user_id`),
  ADD KEY `idx_pcm_user_party` (`user_id`,`party_id`);

--
-- Indexes for table `party_deals_map`
--
ALTER TABLE `party_deals_map`
  ADD PRIMARY KEY (`map_id`),
  ADD UNIQUE KEY `uq_party_deals` (`party_id`,`narration_id`,`user_id`),
  ADD KEY `idx_pdm_user_party` (`user_id`,`party_id`);

--
-- Indexes for table `party_product_brand_setup`
--
ALTER TABLE `party_product_brand_setup`
  ADD PRIMARY KEY (`row_id`),
  ADD KEY `idx_ppbs_user_party` (`user_id`,`party_id`),
  ADD KEY `idx_ppbs_party_side` (`party_id`,`side_type`,`row_no`);

--
-- Indexes for table `party_type_master`
--
ALTER TABLE `party_type_master`
  ADD PRIMARY KEY (`party_type_id`),
  ADD UNIQUE KEY `uk_party_type_user_name` (`user_id`,`party_type_name`),
  ADD UNIQUE KEY `uk_party_type_user_code` (`user_id`,`party_type_code`),
  ADD KEY `idx_party_type_user` (`user_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`),
  ADD UNIQUE KEY `uq_product_user_name` (`user_id`,`product_name`),
  ADD KEY `idx_product_user` (`user_id`);

--
-- Indexes for table `product_brand`
--
ALTER TABLE `product_brand`
  ADD PRIMARY KEY (`map_id`),
  ADD UNIQUE KEY `uq_product_brand_user` (`product_id`,`brand_id`,`user_id`),
  ADD KEY `idx_pb_user` (`user_id`),
  ADD KEY `idx_pb_product` (`product_id`),
  ADD KEY `idx_pb_brand` (`brand_id`);

--
-- Indexes for table `product_group_master`
--
ALTER TABLE `product_group_master`
  ADD PRIMARY KEY (`product_group_id`),
  ADD UNIQUE KEY `uq_product_group_user_name` (`user_id`,`product_group_name`),
  ADD KEY `idx_product_group_user` (`user_id`);

--
-- Indexes for table `product_packing_detail`
--
ALTER TABLE `product_packing_detail`
  ADD PRIMARY KEY (`pack_id`),
  ADD KEY `idx_ppd_user_product` (`user_id`,`product_id`),
  ADD KEY `idx_ppd_product_row` (`product_id`,`row_no`);

--
-- Indexes for table `product_type_master`
--
ALTER TABLE `product_type_master`
  ADD PRIMARY KEY (`product_type_id`),
  ADD UNIQUE KEY `uq_product_type_user_description` (`user_id`,`description`),
  ADD KEY `idx_product_type_user` (`user_id`),
  ADD KEY `idx_product_type_material` (`material_type`);

--
-- Indexes for table `sales_items`
--
ALTER TABLE `sales_items`
  ADD KEY `idx_sales_items` (`user_id`,`entry_no`),
  ADD KEY `user_entry_idx` (`user_id`,`entry_no`);

--
-- Indexes for table `sales_master`
--
ALTER TABLE `sales_master`
  ADD UNIQUE KEY `user_entry_unique` (`user_id`,`entry_no`);

--
-- Indexes for table `sku_unit`
--
ALTER TABLE `sku_unit`
  ADD PRIMARY KEY (`sku_id`),
  ADD KEY `idx_sku_unit_user` (`user_id`),
  ADD KEY `idx_sku_unit_name_user` (`user_id`,`sku_name`);

--
-- Indexes for table `states`
--
ALTER TABLE `states`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `term_type`
--
ALTER TABLE `term_type`
  ADD PRIMARY KEY (`term_type_id`),
  ADD KEY `idx_term_type_user_desc` (`user_id`,`description`),
  ADD KEY `idx_term_type_user_default` (`user_id`,`is_default`);

--
-- Indexes for table `transport`
--
ALTER TABLE `transport`
  ADD PRIMARY KEY (`transport_id`),
  ADD KEY `idx_transport_user` (`user_id`),
  ADD KEY `idx_transport_name_user` (`user_id`,`transport_name`),
  ADD KEY `idx_transport_station_user` (`user_id`,`station`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account_bank_details`
--
ALTER TABLE `account_bank_details`
  MODIFY `bank_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `account_division_balance`
--
ALTER TABLE `account_division_balance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `account_master`
--
ALTER TABLE `account_master`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `add_less_entry_module`
--
ALTER TABLE `add_less_entry_module`
  MODIFY `module_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `add_less_parameter_setup`
--
ALTER TABLE `add_less_parameter_setup`
  MODIFY `setup_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `area`
--
ALTER TABLE `area`
  MODIFY `area_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `bank`
--
ALTER TABLE `bank`
  MODIFY `bank_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `brand`
--
ALTER TABLE `brand`
  MODIFY `brand_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `city`
--
ALTER TABLE `city`
  MODIFY `city_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT for table `company_group`
--
ALTER TABLE `company_group`
  MODIFY `company_group_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `condition_master`
--
ALTER TABLE `condition_master`
  MODIFY `condition_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `courier`
--
ALTER TABLE `courier`
  MODIFY `courier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `deals_in_master`
--
ALTER TABLE `deals_in_master`
  MODIFY `deals_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `district`
--
ALTER TABLE `district`
  MODIFY `district_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `form_wise_book_setup`
--
ALTER TABLE `form_wise_book_setup`
  MODIFY `setup_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `form_wise_entry_module`
--
ALTER TABLE `form_wise_entry_module`
  MODIFY `module_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `group_master`
--
ALTER TABLE `group_master`
  MODIFY `group_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `group_setup`
--
ALTER TABLE `group_setup`
  MODIFY `setup_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `length_master`
--
ALTER TABLE `length_master`
  MODIFY `length_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `line_master`
--
ALTER TABLE `line_master`
  MODIFY `line_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `master_data_entry`
--
ALTER TABLE `master_data_entry`
  MODIFY `entry_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `master_data_print`
--
ALTER TABLE `master_data_print`
  MODIFY `print_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `narration`
--
ALTER TABLE `narration`
  MODIFY `narration_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `note_master`
--
ALTER TABLE `note_master`
  MODIFY `note_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `party`
--
ALTER TABLE `party`
  MODIFY `party_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `party_bank_detail`
--
ALTER TABLE `party_bank_detail`
  MODIFY `bank_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `party_brokerage_packing_rate`
--
ALTER TABLE `party_brokerage_packing_rate`
  MODIFY `pack_rate_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `party_brokerage_rate`
--
ALTER TABLE `party_brokerage_rate`
  MODIFY `rate_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `party_category_master`
--
ALTER TABLE `party_category_master`
  MODIFY `party_category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `party_condition_map`
--
ALTER TABLE `party_condition_map`
  MODIFY `map_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `party_deals_map`
--
ALTER TABLE `party_deals_map`
  MODIFY `map_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `party_product_brand_setup`
--
ALTER TABLE `party_product_brand_setup`
  MODIFY `row_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `party_type_master`
--
ALTER TABLE `party_type_master`
  MODIFY `party_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `product_brand`
--
ALTER TABLE `product_brand`
  MODIFY `map_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `product_group_master`
--
ALTER TABLE `product_group_master`
  MODIFY `product_group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `product_packing_detail`
--
ALTER TABLE `product_packing_detail`
  MODIFY `pack_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_type_master`
--
ALTER TABLE `product_type_master`
  MODIFY `product_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sku_unit`
--
ALTER TABLE `sku_unit`
  MODIFY `sku_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `states`
--
ALTER TABLE `states`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- AUTO_INCREMENT for table `term_type`
--
ALTER TABLE `term_type`
  MODIFY `term_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `transport`
--
ALTER TABLE `transport`
  MODIFY `transport_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `add_less_parameter_setup`
--
ALTER TABLE `add_less_parameter_setup`
  ADD CONSTRAINT `fk_add_less_module` FOREIGN KEY (`module_id`) REFERENCES `add_less_entry_module` (`module_id`) ON DELETE CASCADE;

--
-- Constraints for table `city`
--
ALTER TABLE `city`
  ADD CONSTRAINT `fk_city_district` FOREIGN KEY (`district_id`) REFERENCES `district` (`district_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `form_wise_book_setup`
--
ALTER TABLE `form_wise_book_setup`
  ADD CONSTRAINT `fk_fwbs_module` FOREIGN KEY (`module_id`) REFERENCES `form_wise_entry_module` (`module_id`) ON DELETE CASCADE;

--
-- Constraints for table `sales_items`
--
ALTER TABLE `sales_items`
  ADD CONSTRAINT `sales_items_ibfk_1` FOREIGN KEY (`entry_no`) REFERENCES `sales_master` (`entry_no`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
