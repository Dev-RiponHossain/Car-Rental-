-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 21, 2025 at 06:07 AM
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
-- Database: `carrental`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `UserName` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `updationDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `UserName`, `Password`, `updationDate`) VALUES
(1, 'admin', 'e10adc3949ba59abbe56e057f20f883e', '2025-08-12 06:43:58');

-- --------------------------------------------------------

--
-- Table structure for table `districts`
--

CREATE TABLE `districts` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `division_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `districts`
--

INSERT INTO `districts` (`id`, `name`, `division_id`) VALUES
(1, 'Dhaka', 1),
(2, 'Faridpur', 1),
(3, 'Gazipur', 1),
(4, 'Gopalganj', 1),
(5, 'Kishoreganj', 1),
(6, 'Madaripur', 1),
(7, 'Manikganj', 1),
(8, 'Munshiganj', 1),
(9, 'Narayanganj', 1),
(10, 'Narsingdi', 1),
(11, 'Rajbari', 1),
(12, 'Shariatpur', 1),
(13, 'Tangail', 1),
(14, 'Bandarban', 2),
(15, 'Brahmanbaria', 2),
(16, 'Chandpur', 2),
(17, 'Chattogram', 2),
(18, 'Cumilla', 2),
(19, 'Cox\'s Bazar', 2),
(20, 'Feni', 2),
(21, 'Khagrachari', 2),
(22, 'Lakshmipur', 2),
(23, 'Noakhali', 2),
(24, 'Rangamati', 2),
(25, 'Bogura', 3),
(26, 'Joypurhat', 3),
(27, 'Naogaon', 3),
(28, 'Natore', 3),
(29, 'Chapainawabganj', 3),
(30, 'Pabna', 3),
(31, 'Rajshahi', 3),
(32, 'Sirajganj', 3),
(33, 'Bagerhat', 4),
(34, 'Chuadanga', 4),
(35, 'Jashore', 4),
(36, 'Jhenaidah', 4),
(37, 'Khulna', 4),
(38, 'Kushtia', 4),
(39, 'Magura', 4),
(40, 'Meherpur', 4),
(41, 'Narail', 4),
(42, 'Satkhira', 4),
(43, 'Barguna', 5),
(44, 'Barisal', 5),
(45, 'Bhola', 5),
(46, 'Jhalokathi', 5),
(47, 'Patuakhali', 5),
(48, 'Pirojpur', 5),
(49, 'Habiganj', 6),
(50, 'Moulvibazar', 6),
(51, 'Sunamganj', 6),
(52, 'Sylhet', 6),
(53, 'Dinajpur', 7),
(54, 'Gaibandha', 7),
(55, 'Kurigram', 7),
(56, 'Lalmonirhat', 7),
(57, 'Nilphamari', 7),
(58, 'Panchagarh', 7),
(59, 'Rangpur', 7),
(60, 'Thakurgaon', 7),
(61, 'Jamalpur', 8),
(62, 'Mymensingh', 8),
(63, 'Netrokona', 8),
(64, 'Sherpur', 8);

-- --------------------------------------------------------

--
-- Table structure for table `divisions`
--

CREATE TABLE `divisions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `divisions`
--

INSERT INTO `divisions` (`id`, `name`) VALUES
(1, 'Dhaka'),
(2, 'Chattogram'),
(3, 'Rajshahi'),
(4, 'Khulna'),
(5, 'Barisal'),
(6, 'Sylhet'),
(7, 'Rangpur'),
(8, 'Mymensingh');

-- --------------------------------------------------------

--
-- Table structure for table `payment_requests`
--

CREATE TABLE `payment_requests` (
  `id` int(11) NOT NULL,
  `payment_method` varchar(20) NOT NULL,
  `trx_id` varchar(100) DEFAULT NULL,
  `request_time` datetime DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_requests`
--

INSERT INTO `payment_requests` (`id`, `payment_method`, `trx_id`, `request_time`, `status`) VALUES
(12, 'bKash', '123456', '2025-06-09 15:44:52', 'Invalid'),
(13, 'bKash', '123456789', '2025-06-09 15:45:04', 'Invalid'),
(14, 'bKash', '123456789', '2025-06-09 15:47:52', 'Invalid');

-- --------------------------------------------------------

--
-- Table structure for table `tblbooking`
--

CREATE TABLE `tblbooking` (
  `id` int(11) NOT NULL,
  `BookingNumber` bigint(12) DEFAULT NULL,
  `userEmail` varchar(100) DEFAULT NULL,
  `VehicleId` int(11) DEFAULT NULL,
  `FromDate` varchar(20) DEFAULT NULL,
  `ToDate` varchar(20) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `Status` int(11) NOT NULL DEFAULT 0 COMMENT '0=Pending, 1=Confirmed, 2=Cancelled',
  `PostingDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `LastUpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `payment_status` enum('Pending','Due','Paid') NOT NULL DEFAULT 'Pending',
  `payment_amount` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `mobile_number` varchar(20) DEFAULT NULL,
  `transaction_pin` varchar(10) DEFAULT NULL,
  `card_number` varchar(20) DEFAULT NULL,
  `is_read` int(1) NOT NULL DEFAULT 0,
  `verification_code` varchar(10) DEFAULT NULL,
  `division_name` varchar(100) DEFAULT NULL,
  `district_name` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblbooking`
--

INSERT INTO `tblbooking` (`id`, `BookingNumber`, `userEmail`, `VehicleId`, `FromDate`, `ToDate`, `message`, `Status`, `PostingDate`, `LastUpdationDate`, `payment_status`, `payment_amount`, `payment_method`, `mobile_number`, `transaction_pin`, `card_number`, `is_read`, `verification_code`, `division_name`, `district_name`, `address`) VALUES
(2, 972642984, 'rakib123@gmail.com', 2, '2025-06-18', '2025-06-20', 'ok', 1, '2025-06-16 12:32:56', '2025-06-16 12:33:36', 'Paid', 18000.00, 'Nagad', '01742569851', 'Ripo', NULL, 1, NULL, NULL, NULL, NULL),
(3, 457448613, 'md6430349@gmail.com', 2, '2025-06-25', '2025-06-26', 'ok', 1, '2025-05-13 12:41:08', '2025-06-16 12:53:05', 'Paid', 12000.00, 'bKash', '01478546933', '2365', NULL, 1, NULL, NULL, NULL, NULL),
(5, 512524653, 'sajim123@gmail.com', 8, '2025-06-25', '2025-06-26', 'Beautiful car', 1, '2025-05-09 12:51:36', '2025-05-10 12:52:19', 'Paid', 11000.00, 'Card', '145896422447', '9854', NULL, 1, NULL, NULL, NULL, NULL),
(6, 678219622, 'sajim123@gmail.com', 9, '2025-06-21', '2025-06-23', 'This car is so attractive', 1, '2025-05-15 12:55:32', '2025-05-16 12:56:14', 'Paid', 15000.00, 'bKash', '01698547851', '2589', NULL, 1, NULL, NULL, NULL, NULL),
(7, 423703656, 'jibonahmed5868@gmail.com', 2, '2025-06-27', '2025-06-29', 'This car is avaibale?', 1, '2025-06-16 18:40:02', '2025-06-16 18:41:39', 'Paid', 18000.00, 'bKash', '01954125698', '8569', NULL, 1, NULL, NULL, NULL, NULL),
(9, 551351357, 'jibonahmed5868@gmail.com', 8, '2025-07-01', '2025-07-03', 'This is very beautiful car', 1, '2025-06-16 18:49:44', '2025-06-16 18:50:48', 'Paid', 16500.00, 'bKash', '01789652563', '3652', NULL, 1, NULL, NULL, NULL, NULL),
(11, 933259694, 'md6430349@gmail.com', 9, '2025-06-19', '2025-06-20', 'ok', 1, '2025-06-18 06:42:01', '2025-06-18 06:45:53', 'Paid', 10000.00, 'bKash', '01762663285', 'Ripo', NULL, 1, NULL, NULL, NULL, NULL),
(15, 887250113, 'jibonahmed5868@gmail.com', 1, '2025-08-28', '2025-08-29', 'ok', 1, '2025-06-18 18:01:21', '2025-06-18 18:03:16', 'Paid', 10000.00, 'Cash', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL),
(17, 630281477, 'md6430349@gmail.com', 11, '2025-06-26', '2025-06-27', 'ok', 1, '2025-06-19 18:05:16', '2025-06-19 18:05:55', 'Paid', 10000.00, 'Cash', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL),
(18, 132162984, 'jibonahmed5868@gmail.com', 12, '2025-06-21', '2025-06-22', 'ok', 1, '2025-06-20 03:45:20', '2025-06-20 03:46:41', 'Paid', 13000.00, 'bKash', '01369584782', '2589', '', 1, NULL, NULL, NULL, NULL),
(81, 394473635, 'md6430349@gmail.com', 2, '2025-07-08', '2025-07-09', 'ok', 1, '2025-07-05 16:01:47', '2025-07-05 16:04:10', 'Paid', 6000.00, 'bKash', '01955874798', '8520', '', 1, '428217', 'Dhaka', 'Narayanganj', 'Casara'),
(82, 683949012, 'md6430349@gmail.com', 3, '2025-07-07', '2025-07-08', 'ok', 1, '2025-07-04 18:07:45', '2025-07-06 03:50:28', 'Paid', 3000.00, 'bKash', '01954125698', '8520', '', 1, '646667', 'Chattogram', 'Noakhali', 'ok'),
(83, 154063151, 'md6430349@gmail.com', 4, '2025-07-07', '2025-07-08', 'ok', 1, '2025-07-05 04:07:50', '2025-07-06 04:11:43', 'Paid', 4500.00, 'bKash', '01955874798', '9012', '', 1, '846992', 'Dhaka', 'Narayanganj', 'Fatullah'),
(84, 999794277, 'md6430349@gmail.com', 2, '2025-07-10', '2025-07-10', 'ok', 1, '2025-07-06 04:17:02', '2025-07-06 04:17:54', 'Paid', 6000.00, 'Nagad', '01742569851', '3652', '', 1, '545139', 'Dhaka', 'Narayanganj', 'Fatullah'),
(85, 381234864, 'md6430349@gmail.com', 11, '2025-07-07', '2025-07-08', 'ok', 1, '2025-07-06 04:29:02', '2025-07-06 04:30:49', 'Paid', 5000.00, 'Nagad', '01742569851', '1254', '', 1, '404497', 'Dhaka', 'Narayanganj', 'Rupganj'),
(86, 534912572, 'md6430349@gmail.com', 7, '2025-07-07', '2025-07-08', 'ok', 1, '2025-07-06 04:47:59', '2025-07-06 04:49:42', 'Paid', 3000.00, 'Nagad', '01954125698', '1030', '', 1, '613751', 'Dhaka', 'Narayanganj', 'Fatullah'),
(88, 572777112, 'md6430349@gmail.com', 9, '2025-07-07', '2025-07-08', 'ok', 1, '2025-07-06 05:04:59', '2025-07-06 05:06:45', 'Paid', 5000.00, 'bKash', '01954125698', '2020', '', 1, '668600', 'Dhaka', 'Narsingdi', 'ok'),
(89, 104746470, 'md6430349@gmail.com', 6, '2025-07-08', '2025-07-09', 'ok', 1, '2025-07-06 17:04:13', '2025-07-06 17:05:43', 'Paid', 4000.00, 'bKash', '01916381190', '3020', '', 1, '964618', 'Dhaka', 'Narayanganj', 'Fatullah'),
(90, 648945216, 'md6430349@gmail.com', 9, '2025-07-09', '2025-07-10', 'ok', 1, '2025-07-07 03:14:18', '2025-07-07 03:15:53', 'Paid', 5000.00, 'Nagad', '01632589652', '3021', '', 1, '124493', 'Dhaka', 'Narayanganj', 'Fatullah'),
(92, 174561353, 'md6430349@gmail.com', 5, '2025-07-08', '2025-07-09', 'ok', 1, '2025-07-07 03:26:05', '2025-07-07 03:39:05', 'Paid', 2500.00, 'Nagad', '01916381190', '3015', '', 1, '626047', 'Dhaka', 'Narayanganj', 'Fatullah'),
(93, 117027921, 'md6430349@gmail.com', 8, '2025-07-08', '2025-07-08', 'ok', 1, '2025-07-07 06:35:33', '2025-07-07 06:36:25', 'Paid', 5500.00, 'Nagad', '01632589652', '0000', '', 1, '726766', 'Chattogram', 'Rangamati', 'ok'),
(94, 518047827, 'md6430349@gmail.com', 4, '2025-07-09', '2025-07-10', 'ok', 1, '2025-07-07 06:58:52', '2025-07-07 07:00:57', 'Paid', 4500.00, 'bKash', '01916381190', '1012', '', 1, '643542', 'Chattogram', 'Chandpur', 'ok'),
(95, 371981997, 'md6430349@gmail.com', 9, '2025-07-11', '2025-07-12', 'ok', 1, '2025-07-09 03:24:04', '2025-07-09 03:27:46', 'Paid', 10000.00, 'Nagad', '01632589652', '3025', '', 1, '897765', 'Dhaka', 'Narayanganj', 'Fatullah '),
(100, 525190387, 'md6430349@gmail.com', 13, '2025-07-10', '2025-07-10', 'ok', 1, '2025-07-09 04:02:52', '2025-07-09 04:04:01', 'Paid', 2500.00, 'bKash', '01916381190', '0000', '', 1, '993668', 'Dhaka', 'Narayanganj', 'ok'),
(101, 588159985, 'md6430349@gmail.com', 6, '2025-08-26', '2025-08-27', 'erterrt', 1, '2025-08-12 06:38:39', '2025-08-12 06:42:08', 'Pending', NULL, NULL, NULL, NULL, NULL, 1, NULL, 'Dhaka', 'Narsingdi', 'dghbgfh'),
(102, 649979404, 'md6430349@gmail.com', 4, '2025-08-27', '2025-08-28', 'hi', 1, '2025-08-23 03:23:40', '2025-08-23 03:27:44', 'Paid', 4500.00, 'bKash', '01955215474', '2020', '', 1, '317910', 'Dhaka', 'Dhaka', 'narayanjang'),
(103, 618634836, 'riponhossainmd744@gmail.com', 4, '2025-10-27', '2025-10-27', 'fd', 1, '2025-10-21 03:58:34', '2025-10-21 04:00:37', 'Paid', 2250.00, 'bKash', '01955215474', '1111', '', 1, '110014', 'Barisal', 'Jhalokathi', 'cghgd');

-- --------------------------------------------------------

--
-- Table structure for table `tblbrands`
--

CREATE TABLE `tblbrands` (
  `id` int(11) NOT NULL,
  `BrandName` varchar(120) NOT NULL,
  `CreationDate` timestamp NULL DEFAULT current_timestamp(),
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblbrands`
--

INSERT INTO `tblbrands` (`id`, `BrandName`, `CreationDate`, `UpdationDate`) VALUES
(1, 'Maruti', '2024-05-01 16:24:34', '2024-06-05 05:26:25'),
(2, 'BMW', '2024-05-01 16:24:34', '2024-06-05 05:26:34'),
(3, 'Audi', '2024-05-01 16:24:34', '2024-06-05 05:26:34'),
(4, 'Nissan', '2024-05-01 16:24:34', '2024-06-05 05:26:34'),
(5, 'Toyota', '2024-05-01 16:24:34', '2024-06-05 05:26:34'),
(7, 'Volkswagon', '2024-05-01 16:24:34', '2024-06-05 05:26:34'),
(9, 'Ford', '2025-06-14 16:29:30', NULL),
(10, 'Tesla', '2025-06-19 18:00:39', NULL),
(14, 'Lexus ', '2025-06-20 02:47:05', NULL),
(15, 'Mitsubishi', '2025-06-20 02:47:17', NULL),
(16, 'Opel', '2025-06-20 02:47:29', NULL),
(17, 'Alfa Romeo', '2025-06-20 02:47:42', NULL),
(18, 'Mazda', '2025-06-28 16:34:53', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblcontactusinfo`
--

CREATE TABLE `tblcontactusinfo` (
  `id` int(11) NOT NULL,
  `Address` tinytext DEFAULT NULL,
  `EmailId` varchar(255) DEFAULT NULL,
  `ContactNo` char(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblcontactusinfo`
--

INSERT INTO `tblcontactusinfo` (`id`, `Address`, `EmailId`, `ContactNo`) VALUES
(1, 'House 12, Road 5, Dhanmondi, Dhaka 1209, Bangladesh', 'carrentalportal@gmail.com', '8974561236');

-- --------------------------------------------------------

--
-- Table structure for table `tblcontactusquery`
--

CREATE TABLE `tblcontactusquery` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `EmailId` varchar(120) DEFAULT NULL,
  `ContactNumber` char(11) DEFAULT NULL,
  `Message` longtext DEFAULT NULL,
  `PostingDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblcontactusquery`
--

INSERT INTO `tblcontactusquery` (`id`, `name`, `EmailId`, `ContactNumber`, `Message`, `PostingDate`, `status`) VALUES
(1, 'Kunal ', 'kunal@gmail.com', '7977779798', 'I want to know you brach in Chandigarh?', '2024-06-04 09:34:51', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tblpages`
--

CREATE TABLE `tblpages` (
  `id` int(11) NOT NULL,
  `PageName` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT '',
  `detail` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblpages`
--

INSERT INTO `tblpages` (`id`, `PageName`, `type`, `detail`) VALUES
(1, 'Terms and Conditions', 'terms', '<P align=justify><FONT size=2><STRONG><FONT color=#990000>(1) ACCEPTANCE OF TERMS</FONT><BR><BR></STRONG>Welcome to Yahoo! India. 1Yahoo Web Services India Private Limited Yahoo\", \"we\" or \"us\" as the case may be) provides the Service (defined below) to you, subject to the following Terms of Service (\"TOS\"), which may be updated by us from time to time without notice to you. You can review the most current version of the TOS at any time at: <A href=\"http://in.docs.yahoo.com/info/terms/\">http://in.docs.yahoo.com/info/terms/</A>. In addition, when using particular Yahoo services or third party services, you and Yahoo shall be subject to any posted guidelines or rules applicable to such services which may be posted from time to time. All such guidelines or rules, which maybe subject to change, are hereby incorporated by reference into the TOS. In most cases the guides and rules are specific to a particular part of the Service and will assist you in applying the TOS to that part, but to the extent of any inconsistency between the TOS and any guide or rule, the TOS will prevail. We may also offer other services from time to time that are governed by different Terms of Services, in which case the TOS do not apply to such other services if and to the extent expressly excluded by such different Terms of Services. Yahoo also may offer other services from time to time that are governed by different Terms of Services. These TOS do not apply to such other services that are governed by different Terms of Service. </FONT></P>\r\n<P align=justify><FONT size=2>Welcome to Yahoo! India. Yahoo Web Services India Private Limited Yahoo\", \"we\" or \"us\" as the case may be) provides the Service (defined below) to you, subject to the following Terms of Service (\"TOS\"), which may be updated by us from time to time without notice to you. You can review the most current version of the TOS at any time at: </FONT><A href=\"http://in.docs.yahoo.com/info/terms/\"><FONT size=2>http://in.docs.yahoo.com/info/terms/</FONT></A><FONT size=2>. In addition, when using particular Yahoo services or third party services, you and Yahoo shall be subject to any posted guidelines or rules applicable to such services which may be posted from time to time. All such guidelines or rules, which maybe subject to change, are hereby incorporated by reference into the TOS. In most cases the guides and rules are specific to a particular part of the Service and will assist you in applying the TOS to that part, but to the extent of any inconsistency between the TOS and any guide or rule, the TOS will prevail. We may also offer other services from time to time that are governed by different Terms of Services, in which case the TOS do not apply to such other services if and to the extent expressly excluded by such different Terms of Services. Yahoo also may offer other services from time to time that are governed by different Terms of Services. These TOS do not apply to such other services that are governed by different Terms of Service. </FONT></P>\r\n<P align=justify><FONT size=2>Welcome to Yahoo! India. Yahoo Web Services India Private Limited Yahoo\", \"we\" or \"us\" as the case may be) provides the Service (defined below) to you, subject to the following Terms of Service (\"TOS\"), which may be updated by us from time to time without notice to you. You can review the most current version of the TOS at any time at: </FONT><A href=\"http://in.docs.yahoo.com/info/terms/\"><FONT size=2>http://in.docs.yahoo.com/info/terms/</FONT></A><FONT size=2>. In addition, when using particular Yahoo services or third party services, you and Yahoo shall be subject to any posted guidelines or rules applicable to such services which may be posted from time to time. All such guidelines or rules, which maybe subject to change, are hereby incorporated by reference into the TOS. In most cases the guides and rules are specific to a particular part of the Service and will assist you in applying the TOS to that part, but to the extent of any inconsistency between the TOS and any guide or rule, the TOS will prevail. We may also offer other services from time to time that are governed by different Terms of Services, in which case the TOS do not apply to such other services if and to the extent expressly excluded by such different Terms of Services. Yahoo also may offer other services from time to time that are governed by different Terms of Services. These TOS do not apply to such other services that are governed by different Terms of Service. </FONT></P>'),
(2, 'Privacy Policy', 'privacy', '<span style=\"color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; text-align: justify;\">At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat</span>'),
(3, 'About Us ', 'aboutus', '<span style=\"color: rgb(51, 51, 51); font-family: &quot;Helvetica Neue&quot;, Helvetica, Arial, sans-serif; font-size: 13.3333px;\">We offer a varied fleet of cars, ranging from the compact. All our vehicles have air conditioning, &nbsp;power steering, electric windows. All our vehicles are bought and maintained at official dealerships only. Automatic transmission cars are available in every booking class.&nbsp;</span><span style=\"color: rgb(52, 52, 52); font-family: Arial, Helvetica, sans-serif;\">As we are not affiliated with any specific automaker, we are able to provide a variety of vehicle makes and models for customers to rent.</span><div><span style=\"color: rgb(62, 62, 62); font-family: &quot;Lucida Sans Unicode&quot;, &quot;Lucida Grande&quot;, sans-serif; font-size: 11px;\">ur mission is to be recognised as the global leader in Car Rental for companies and the public and private sector by partnering with our clients to provide the best and most efficient Cab Rental solutions and to achieve service excellence.</span><span style=\"color: rgb(52, 52, 52); font-family: Arial, Helvetica, sans-serif;\"><br></span></div>'),
(11, 'FAQs', 'faqs', '																														<span style=\"color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; text-align: justify;\">Address------Test &nbsp; &nbsp;dsfdsfds</span>');

-- --------------------------------------------------------

--
-- Table structure for table `tblsubscribers`
--

CREATE TABLE `tblsubscribers` (
  `id` int(11) NOT NULL,
  `SubscriberEmail` varchar(120) DEFAULT NULL,
  `PostingDate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblsubscribers`
--

INSERT INTO `tblsubscribers` (`id`, `SubscriberEmail`, `PostingDate`) VALUES
(5, 'kunal@gmail.com', '2024-05-31 09:35:07'),
(6, 'md6430349@gmail.com', '2025-06-17 16:24:35');

-- --------------------------------------------------------

--
-- Table structure for table `tbltestimonial`
--

CREATE TABLE `tbltestimonial` (
  `id` int(11) NOT NULL,
  `UserEmail` varchar(100) NOT NULL,
  `Testimonial` mediumtext NOT NULL,
  `PostingDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbltestimonial`
--

INSERT INTO `tbltestimonial` (`id`, `UserEmail`, `Testimonial`, `PostingDate`, `status`) VALUES
(5, 'rakib123@gmail.com', 'I am satisfy their services.', '2025-06-14 16:20:59', 1),
(6, 'md6430349@gmail.com', 'I am satisfy their services.', '2025-06-14 16:21:27', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tblusers`
--

CREATE TABLE `tblusers` (
  `id` int(11) NOT NULL,
  `FullName` varchar(120) DEFAULT NULL,
  `EmailId` varchar(100) DEFAULT NULL,
  `Password` varchar(100) DEFAULT NULL,
  `ContactNo` char(11) DEFAULT NULL,
  `dob` varchar(100) DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `City` varchar(100) DEFAULT NULL,
  `Country` varchar(100) DEFAULT NULL,
  `RegDate` timestamp NULL DEFAULT current_timestamp(),
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblusers`
--

INSERT INTO `tblusers` (`id`, `FullName`, `EmailId`, `Password`, `ContactNo`, `dob`, `Address`, `City`, `Country`, `RegDate`, `UpdationDate`) VALUES
(1, 'Test', 'test@gmail.com', 'f925916e2754e5e03f75dd58a5733251', '6465465465', '', 'L-890, Gaur City Ghaziabad', 'Ghaziabad', 'India', '2024-05-01 14:00:49', '2024-06-05 05:27:37'),
(2, 'Amit', 'amikt12@gmail.com', 'f925916e2754e5e03f75dd58a5733251', '1425365214', NULL, NULL, NULL, NULL, '2024-06-05 05:31:05', NULL),
(3, 'Ripon Hossain', 'md6430349@gmail.com', '25d55ad283aa400af464c76d713c07ad', '01955215474', '12/5/1997', 'Aliganj, Jorpul', 'Dhaka', 'Bangladesh', '2025-06-05 14:28:07', '2025-10-11 04:31:35'),
(5, 'Shimanto', 'moynulislamshimanto24@gmail.com', '577f2852d267e9283f9d87a8e4fdbf84', '0194985450', NULL, NULL, NULL, NULL, '2025-06-09 16:43:00', NULL),
(6, 'Rakib', 'rakib123@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0185707814', '12/7/1999', 'Mirpur', 'Dhaka', 'Bangladesh', '2025-06-10 05:02:14', '2025-06-11 08:15:48'),
(7, 'sajim', 'sajim123@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0191638119', '12/7/1999', 'Sreenagar', 'Munshiganj', 'Bangladesh', '2025-06-16 12:50:45', '2025-06-16 12:54:36'),
(14, 'Jibon', 'jibonahmed5868@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', '0163252145', '12/12/20005', 'Muhammadpur', 'Dhaka,', 'Bangladesh', '2025-06-16 18:39:06', '2025-06-18 18:05:59'),
(15, 'Ripon hossain', 'riponhossainmd744@gmail.com', '25d55ad283aa400af464c76d713c07ad', '0176266328', NULL, NULL, NULL, NULL, '2025-10-21 03:55:39', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblvehicles`
--

CREATE TABLE `tblvehicles` (
  `id` int(11) NOT NULL,
  `VehiclesTitle` varchar(150) DEFAULT NULL,
  `VehiclesBrand` int(11) DEFAULT NULL,
  `VehiclesOverview` longtext DEFAULT NULL,
  `PricePerDay` int(11) DEFAULT NULL,
  `FuelType` varchar(100) DEFAULT NULL,
  `ModelYear` int(6) DEFAULT NULL,
  `SeatingCapacity` int(11) DEFAULT NULL,
  `Vimage1` varchar(120) DEFAULT NULL,
  `Vimage2` varchar(120) DEFAULT NULL,
  `Vimage3` varchar(120) DEFAULT NULL,
  `Vimage4` varchar(120) DEFAULT NULL,
  `Vimage5` varchar(120) DEFAULT NULL,
  `AirConditioner` int(11) DEFAULT NULL,
  `PowerDoorLocks` int(11) DEFAULT NULL,
  `AntiLockBrakingSystem` int(11) DEFAULT NULL,
  `BrakeAssist` int(11) DEFAULT NULL,
  `PowerSteering` int(11) DEFAULT NULL,
  `DriverAirbag` int(11) DEFAULT NULL,
  `PassengerAirbag` int(11) DEFAULT NULL,
  `PowerWindows` int(11) DEFAULT NULL,
  `CDPlayer` int(11) DEFAULT NULL,
  `CentralLocking` int(11) DEFAULT NULL,
  `CrashSensor` int(11) DEFAULT NULL,
  `LeatherSeats` int(11) DEFAULT NULL,
  `RegDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `status` enum('Available','Booked','Rented','Maintenance') NOT NULL DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblvehicles`
--

INSERT INTO `tblvehicles` (`id`, `VehiclesTitle`, `VehiclesBrand`, `VehiclesOverview`, `PricePerDay`, `FuelType`, `ModelYear`, `SeatingCapacity`, `Vimage1`, `Vimage2`, `Vimage3`, `Vimage4`, `Vimage5`, `AirConditioner`, `PowerDoorLocks`, `AntiLockBrakingSystem`, `BrakeAssist`, `PowerSteering`, `DriverAirbag`, `PassengerAirbag`, `PowerWindows`, `CDPlayer`, `CentralLocking`, `CrashSensor`, `LeatherSeats`, `RegDate`, `UpdationDate`, `status`) VALUES
(1, 'Maruti Suzuki Wagon R', 1, 'Maruti Wagon R Latest Updates\r\n\r\nMaruti Suzuki has launched the BS6 Wagon R S-CNG in India. The LXI CNG and LXI (O) CNG variants now cost Rs 5.25 lakh and Rs 5.32 lakh respectively, up by Rs 19,000. Maruti claims a fuel economy of 32.52km per kg. The CNG Wagon R’s continuation in the BS6 era is part of the carmaker’s ‘Mission Green Million’ initiative announced at Auto Expo 2020.\r\n\r\nPreviously, the carmaker had updated the 1.0-litre powertrain to meet BS6 emission norms. It develops 68PS of power and 90Nm of torque, same as the BS4 unit. However, the updated motor now returns 21.79 kmpl, which is a little less than the BS4 unit’s 22.5kmpl claimed figure. Barring the CNG variants, the prices of the Wagon R 1.0-litre have been hiked by Rs 8,000.', 5000, 'Petrol', 2019, 5, 'rear-3-4-left-589823254_930x620.jpg', 'tail-lamp-1666712219_930x620.jpg', 'rear-3-4-right-520328200_930x620.jpg', 'steering-close-up-1288209207_930x620.jpg', 'boot-with-standard-luggage-202327489_930x620.jpg', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, '2024-05-10 07:04:35', '2025-06-14 16:28:45', 'Available'),
(2, 'BMW 5 Series', 2, 'BMW 5 Series price starts at ? 55.4 Lakh and goes upto ? 68.39 Lakh. The price of Petrol version for 5 Series ranges between ? 55.4 Lakh - ? 60.89 Lakh and the price of Diesel version for 5 Series ranges between ? 60.89 Lakh - ? 68.39 Lakh.', 6000, 'Petrol', 2018, 5, 'BMW-5-Series-Exterior-102005.jpg', 'BMW-5-Series-New-Exterior-89729.jpg', 'BMW-5-Series-Exterior-102006.jpg', 'BMW-5-Series-Interior-102021.jpg', 'BMW-5-Series-Interior-102022.jpg', 1, 1, 1, 1, 1, 1, 1, 1, NULL, 1, 1, 1, '2024-05-10 07:04:35', '2025-06-10 05:11:18', 'Available'),
(3, 'Audi Q8', 3, 'As per ARAI, the mileage of Q8 is 0 kmpl. Real mileage of the vehicle varies depending upon the driving habits. City and highway mileage figures also vary depending upon the road conditions.', 3000, 'Petrol', 2017, 5, 'audi-q8-front-view4.jpg', '1920x1080_MTC_XL_framed_Audi-Odessa-Armaturen_Spiegelung_CC_v05.jpg', 'audi1.jpg', '1audiq8.jpg', 'audi-q8-front-view4.jpeg', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, '2024-05-10 07:04:35', '2024-06-05 05:30:33', 'Available'),
(4, 'Nissan Kicks', 4, 'Latest Update: Nissan has launched the Kicks 2020 with a new turbocharged petrol engine. You can read more about it here.\r\n\r\nNissan Kicks Price and Variants: The Kicks is available in four variants: XL, XV, XV Premium, and XV Premium(O).', 4500, 'Petrol', 2020, 5, 'front-left-side-47.jpg', 'kicksmodelimage.jpg', 'download.jpg', 'kicksmodelimage.jpg', '', 1, NULL, NULL, 1, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, '2024-05-10 07:04:35', '2025-06-10 05:11:34', 'Available'),
(5, 'Nissan GT-R', 4, ' The GT-R packs a 3.8-litre V6 twin-turbocharged petrol, which puts out 570PS of max power at 6800rpm and 637Nm of peak torque. The engine is mated to a 6-speed dual-clutch transmission in an all-wheel-drive setup. The 2+2 seater GT-R sprints from 0-100kmph in less than 3', 2500, 'Petrol', 2019, 5, 'Nissan-GTR-Right-Front-Three-Quarter-84895.jpg', 'Best-Nissan-Cars-in-India-New-and-Used-1.jpg', '2bb3bc938e734f462e45ed83be05165d.jpg', '2020-nissan-gtr-rakuda-tan-semi-aniline-leather-interior.jpg', 'images.jpg', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, '2024-05-10 07:04:35', '2025-06-10 05:11:54', 'Available'),
(6, 'Nissan Sunny 2020', 4, 'Value for money product and it was so good It is more spacious than other sedans It looks like a luxurious car.', 4000, 'CNG', 2018, 5, 'Nissan-Sunny-Right-Front-Three-Quarter-48975_ol.jpg', 'images (1).jpg', 'Nissan-Sunny-Interior-114977.jpg', 'nissan-sunny-8a29f53-500x375.jpg', 'new-nissan-sunny-photo.jpg', 1, 1, NULL, 1, 1, 1, 1, 1, 1, 1, 1, 1, '2024-05-10 07:04:35', '2025-06-10 05:12:05', 'Available'),
(7, 'Toyota Fortuner', 5, 'Toyota Fortuner Features: It is a premium seven-seater SUV loaded with features such as LED projector headlamps with LED DRLs, LED fog lamp, and power-adjustable and foldable ORVMs. Inside, the Fortuner offers features such as power-adjustable driver seat, automatic climate control, push-button stop/start, and cruise control.\r\n\r\nToyota Fortuner Safety Features: The Toyota Fortuner gets seven airbags, hill assist control, vehicle stability control with brake assist, and ABS with EBD.', 3000, 'Petrol', 2020, 5, '2015_Toyota_Fortuner_(New_Zealand).jpg', 'toyota-fortuner-legender-rear-quarters-6e57.jpg', 'zw-toyota-fortuner-2020-2.jpg', '2017_ford_taurus_sedan_se_fd_izmo_1_500.jpg', '', NULL, NULL, NULL, NULL, NULL, 1, NULL, 1, NULL, 1, 1, 1, '2024-05-10 07:04:35', '2025-06-28 17:23:47', 'Available'),
(8, 'Maruti Suzuki Vitara Brezza', 1, 'The new Vitara Brezza is a well-rounded package that is feature-loaded and offers good drivability. And it is backed by Maruti’s vast service network, which ensures a peace of mind to customers. The petrol motor could have been more refined and offered more pep.', 5500, 'Petrol', 2018, 5, 'marutisuzuki-vitara-brezza-right-front-three-quarter3.jpg', 'marutisuzuki-vitara-brezza-rear-view37.jpg', 'marutisuzuki-vitara-brezza-dashboard10.jpg', 'marutisuzuki-vitara-brezza-boot-space59.jpg', 'marutisuzuki-vitara-brezza-boot-space28.jpg', NULL, 1, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, '2024-05-10 07:04:35', '2025-06-10 05:12:19', 'Available'),
(9, 'Ford Taurus', 9, 'he Ford Taurus is a full-size sedan that has played a significant role in Ford\'s lineup, especially in North America. It was introduced in 1986 and produced in various generations until 2019 (in the U.S.), although production still continued in China.', 5000, 'Diesel', 2020, 5, '2017_ford_taurus_sedan_limited_fq_oem_2_500.jpg', 'knowledge_base_bg.jpg', 'boot-with-standard-luggage-202327489_930x620.jpg', '2017_ford_taurus_sedan_sho_ds_izmo_1_500.jpg', '', 1, 1, 1, 1, NULL, 1, 1, 1, 1, 1, 1, 1, '2025-06-14 16:32:41', '2025-06-28 18:03:29', 'Available'),
(10, 'Ford Edge', 9, 'Latest Update: Ford Edgehas launched the Kicks 2020 with a new turbocharged petrol engine. You can read more about it here. Nissan Kicks Price and Variants: The Kicks is available in four variants: XL, XV, XV Premium, and XV Premium(O).', 6000, 'Petrol', 2018, 5, 'car_755x430.png', 'marutisuzuki-vitara-brezza-boot-space28.jpg', 'marutisuzuki-vitara-brezza-rear-view37.jpg', 'img_390x390.jpg', '', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, '2025-06-14 16:39:29', NULL, 'Available'),
(11, 'Vireon Auto', 10, ' Leading with forward-thinking design and features. Durable build quality and dependable support. Electric and hybrid powertrains, eco-conscious manufacturing.\r\n\r\n', 5000, 'CNG', 2025, 6, 'recent-car-4.jpg', 'knowledge_base_bg.jpg', 'listing_img5.jpg', 'listing_img1.jpg', 'blog_img3.jpg', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, '2025-06-19 18:03:22', '2025-06-19 18:04:39', 'Available'),
(12, 'LC 500', 14, 'Crafted with precision, powered by innovation. Driven to deliver excellence on every road. imple, bold, modern sans-serif font\r\n\r\n', 6500, 'Diesel', 2025, 4, 'LC500c_220-scaled.jpg', 'LC500c_202_Steering_Wheel_Head_On-scaled.jpg', 'LC500c_209_Door_Drivers_Side-scaled.jpg', 'LC500c_147_Roof_Switch_1-scaled.jpg', 'LC500c_158_Headlight_Above-scaled.jpg', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, '2025-06-20 02:54:05', NULL, 'Available'),
(13, 'Mazda CX-3', 18, 'The Mazda CX?3 blends elegant Kodo styling with agile driving dynamics and a refined interior. While it slots in as an older entry in the segment, its feature-rich trims, strong safety aids, and Mazda’s SkyActiv engineering keep it competitive—especially for city dwellers and small families. That said, if spacious rear seats or large cargo volume are priorities, shoppers might consider larger competitors or Mazda\'s own', 5000, 'Diesel', 2025, 6, 'mazda-cx-3-tail-light-984278.jpg', 'mazda-cx-3-front-side-ac-vents-259306.jpg', 'mazda-cx-3-dashboard-view-108614.jpg', 'mazda-cx-3-medium-angle-front-view-124927.jpg', '', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, '2025-06-28 16:35:50', NULL, 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_due_payments`
--

CREATE TABLE `tbl_due_payments` (
  `id` int(11) NOT NULL,
  `booking_number` bigint(12) DEFAULT NULL,
  `user_email` varchar(100) DEFAULT NULL,
  `paid_amount` decimal(10,2) DEFAULT NULL,
  `due_amount` decimal(10,2) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `paid_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','Paid') DEFAULT 'Pending',
  `note` text DEFAULT NULL,
  `payment_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_due_payments`
--

INSERT INTO `tbl_due_payments` (`id`, `booking_number`, `user_email`, `paid_amount`, `due_amount`, `total_amount`, `payment_method`, `paid_date`, `status`, `note`, `payment_date`) VALUES
(52, 394473635, 'md6430349@gmail.com', 12000.00, 0.00, 12000.00, 'Cash', '2025-07-05 16:02:43', 'Paid', '', '2025-07-05'),
(53, 683949012, 'md6430349@gmail.com', 6000.00, 0.00, 6000.00, 'Cash', '2025-07-05 18:08:17', 'Paid', '', '2025-07-06'),
(54, 154063151, 'md6430349@gmail.com', 9000.00, 0.00, 9000.00, 'Cash', '2025-07-06 04:09:03', 'Paid', '', '2025-07-06'),
(55, 381234864, 'md6430349@gmail.com', 10000.00, 0.00, 10000.00, 'Cash', '2025-07-06 04:29:38', 'Paid', '', '2025-07-06'),
(56, 534912572, 'md6430349@gmail.com', 6000.00, 0.00, 6000.00, 'Cash', '2025-07-06 04:48:35', 'Paid', '', '2025-07-06'),
(57, 981639211, 'md6430349@gmail.com', 6000.00, 0.00, 6000.00, 'Cash', '2025-07-06 04:54:15', 'Paid', '', '2025-07-06'),
(58, 572777112, 'md6430349@gmail.com', 10000.00, 0.00, 10000.00, 'Cash', '2025-07-06 05:05:35', 'Paid', '', '2025-07-06'),
(59, 104746470, 'md6430349@gmail.com', 8000.00, 0.00, 8000.00, 'Cash', '2025-07-06 17:04:53', 'Paid', '', '2025-07-06'),
(60, 648945216, 'md6430349@gmail.com', 10000.00, 0.00, 10000.00, 'Cash', '2025-07-07 03:15:00', 'Paid', '', '2025-07-07'),
(65, 525190387, 'md6430349@gmail.com', 5000.00, 0.00, 5000.00, 'Cash', '2025-07-08 18:00:00', 'Paid', '', NULL),
(66, 649979404, 'md6430349@gmail.com', 9000.00, 0.00, 9000.00, 'Cash', '2025-08-22 18:00:00', 'Paid', '', NULL),
(67, 618634836, 'riponhossainmd744@gmail.com', 4500.00, 0.00, 4500.00, 'Cash', '2025-10-20 18:00:00', 'Paid', '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_due_payment_logs`
--

CREATE TABLE `tbl_due_payment_logs` (
  `id` int(11) NOT NULL,
  `booking_number` varchar(100) DEFAULT NULL,
  `due_paid_amount` decimal(10,2) DEFAULT NULL,
  `payment_date` date DEFAULT curdate(),
  `payment_method` varchar(50) NOT NULL DEFAULT 'Cash'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_due_payment_logs`
--

INSERT INTO `tbl_due_payment_logs` (`id`, `booking_number`, `due_paid_amount`, `payment_date`, `payment_method`) VALUES
(6, '394473635', 6000.00, '2025-07-05', 'Cash'),
(7, '683949012', 3000.00, '2025-07-05', 'Cash'),
(8, '154063151', 4500.00, '2025-07-06', 'Cash'),
(9, '381234864', 5000.00, '2025-07-06', 'Cash'),
(12, '572777112', 5000.00, '2025-07-06', 'Cash'),
(13, '104746470', 4000.00, '2025-07-06', 'Cash'),
(14, '648945216', 5000.00, '2025-07-07', 'Cash'),
(15, '174561353', 2500.00, '2025-07-07', 'Cash'),
(19, '525190387', 2500.00, '2025-07-09', 'Cash'),
(20, '649979404', 4500.00, '2025-08-23', 'Cash'),
(21, '618634836', 2250.00, '2025-10-21', 'Cash');

-- --------------------------------------------------------

--
-- Table structure for table `valid_transactions`
--

CREATE TABLE `valid_transactions` (
  `id` int(11) NOT NULL,
  `trx_id` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('unused','used') DEFAULT 'unused'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `valid_transactions`
--

INSERT INTO `valid_transactions` (`id`, `trx_id`, `email`, `amount`, `status`) VALUES
(1, 'TRX6846FE0B37B9C', 'md6430349@gmail.com', 800.00, 'used');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `districts`
--
ALTER TABLE `districts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `division_id` (`division_id`);

--
-- Indexes for table `divisions`
--
ALTER TABLE `divisions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_requests`
--
ALTER TABLE `payment_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblbooking`
--
ALTER TABLE `tblbooking`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblbrands`
--
ALTER TABLE `tblbrands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblcontactusinfo`
--
ALTER TABLE `tblcontactusinfo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblcontactusquery`
--
ALTER TABLE `tblcontactusquery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblpages`
--
ALTER TABLE `tblpages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblsubscribers`
--
ALTER TABLE `tblsubscribers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbltestimonial`
--
ALTER TABLE `tbltestimonial`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblusers`
--
ALTER TABLE `tblusers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `EmailId` (`EmailId`);

--
-- Indexes for table `tblvehicles`
--
ALTER TABLE `tblvehicles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_due_payments`
--
ALTER TABLE `tbl_due_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_due_payment_logs`
--
ALTER TABLE `tbl_due_payment_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `valid_transactions`
--
ALTER TABLE `valid_transactions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `districts`
--
ALTER TABLE `districts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `divisions`
--
ALTER TABLE `divisions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `payment_requests`
--
ALTER TABLE `payment_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tblbooking`
--
ALTER TABLE `tblbooking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT for table `tblbrands`
--
ALTER TABLE `tblbrands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `tblcontactusinfo`
--
ALTER TABLE `tblcontactusinfo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblcontactusquery`
--
ALTER TABLE `tblcontactusquery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblpages`
--
ALTER TABLE `tblpages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `tblsubscribers`
--
ALTER TABLE `tblsubscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbltestimonial`
--
ALTER TABLE `tbltestimonial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tblusers`
--
ALTER TABLE `tblusers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tblvehicles`
--
ALTER TABLE `tblvehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tbl_due_payments`
--
ALTER TABLE `tbl_due_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `tbl_due_payment_logs`
--
ALTER TABLE `tbl_due_payment_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `valid_transactions`
--
ALTER TABLE `valid_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `districts`
--
ALTER TABLE `districts`
  ADD CONSTRAINT `districts_ibfk_1` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
