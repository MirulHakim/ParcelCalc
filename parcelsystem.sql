-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 18, 2025 at 06:57 PM
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
-- Database: `parcelsystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `parcel_info`
--

CREATE TABLE `parcel_info` (
  `Parcel_id` varchar(10) NOT NULL,
  `PhoneNum` varchar(11) NOT NULL,
  `Parcel_type` varchar(10) NOT NULL,
  `Parcel_owner` varchar(50) NOT NULL,
  `Date_arrived` date NOT NULL,
  `Date_received` date NOT NULL,
  `Parcel_image` longblob NOT NULL,
  `Status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parcel_info`
--

INSERT INTO `parcel_info` (`Parcel_id`, `PhoneNum`, `Parcel_type`, `Parcel_owner`, `Date_arrived`, `Date_received`, `Parcel_image`, `Status`) VALUES
('04 JUN/00', '01145275596', 'kotak', 'Harith', '0000-00-00', '0000-00-00', '', 0),
('04 Jun/01', '0135975868', 'HITAM', 'Rizqy', '0000-00-00', '0000-00-00', '', 0),
('04 Jun/02', '0135975868', 'PUTIH', 'Ainnur', '0000-00-00', '0000-00-00', '', 0),
('04 Jun/03', '0135975868', 'KOTAK', 'Nurul', '0000-00-00', '0000-00-00', '', 0),
('04 Jun/04', '0135975868', 'PUTIH', 'Balqis', '0000-00-00', '0000-00-00', '', 0),
('04 Jun/06', '0112345678', 'KELABU', 'Wardah', '0000-00-00', '0000-00-00', '', 0),
('04 Jun/07', '0112345678', 'KOTAK', 'Iman', '0000-00-00', '0000-00-00', '', 0),
('18 Jun/00', '01125467852', 'kelabu', 'Aini', '0000-00-00', '0000-00-00', '', 0),
('18 Jun/05', '01135761993', 'hitam', 'Nabira', '0000-00-00', '0000-00-00', '', 0),
('18 Jun/09', '01234567855', 'kotak', 'Siti', '0000-00-00', '0000-00-00', '', 0),
('18 Jun/10', '01234567855', 'others', 'Nurul', '0000-00-00', '0000-00-00', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `Report_id` int(11) NOT NULL,
  `Parcel_id` int(11) NOT NULL,
  `Student_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `Staff_id` int(6) NOT NULL,
  `Name_staff` varchar(50) NOT NULL,
  `Password` varchar(10) NOT NULL,
  `PhoneNum_staff` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`Staff_id`, `Name_staff`, `Password`, `PhoneNum_staff`) VALUES
(222297, 'Balqis', '1234567890', '01135445868');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `Student_id` int(6) NOT NULL,
  `Name_student` varchar(50) NOT NULL,
  `PhoneNum` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `parcel_info`
--
ALTER TABLE `parcel_info`
  ADD PRIMARY KEY (`Parcel_id`);

--
-- Indexes for table `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`Report_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`Staff_id`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`Student_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `report`
--
ALTER TABLE `report`
  MODIFY `Report_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
