-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 06, 2025 at 02:00 PM
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
-- Database: `fishguard`
--

-- --------------------------------------------------------

--
-- Table structure for table `catch_reports`
--

CREATE TABLE `catch_reports` (
  `CRID` int(11) NOT NULL,
  `UID` int(11) NOT NULL,
  `SID` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `size_cm` decimal(5,2) DEFAULT NULL,
  `catch_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `catch_reports`
--

INSERT INTO `catch_reports` (`CRID`, `UID`, `SID`, `quantity`, `size_cm`, `catch_date`) VALUES
(1, 1, 1, 3, 60.50, '2025-05-01'),
(2, 3, 1, 6, 30.00, '2025-05-02'),
(4, 4, 5, 3, 62.00, '2025-05-04'),
(5, 1, 5, 10, 60.00, '2025-05-05');

-- --------------------------------------------------------

--
-- Table structure for table `species`
--

CREATE TABLE `species` (
  `SID` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `catch_limit` int(11) NOT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `fine_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `endangered_level` enum('Low','Medium','High') NOT NULL DEFAULT 'Low'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `species`
--

INSERT INTO `species` (`SID`, `name`, `catch_limit`, `is_available`, `fine_rate`, `endangered_level`) VALUES
(1, 'Tuna', 5, 1, 500.00, 'High'),
(2, 'Grouper', 3, 0, 2134.00, 'Medium'),
(3, 'Mackerel', 10, 1, 209349.00, 'Low'),
(5, 'Timothy Dionela', 4, 1, 12.00, 'Low');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UID` int(11) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `name` text NOT NULL,
  `address` text NOT NULL,
  `contact_no` text NOT NULL,
  `role` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UID`, `username`, `password`, `name`, `address`, `contact_no`, `role`) VALUES
(1, 'admin', '$2y$10$l1ZnzDmLrSw56dzAGDSLhuIV6/JtubwMVBbeZeaBci7XHfVsA1K0K', 'LeBron James', 'atlantis', '9167436785', 1),
(3, 'sunflowerseeds', '$2y$10$jKpXzW7WsNAXc/gYYFKaL.O/CO8LTJBti9GzJ/D.oHJ08JBuk/eG6', 'Timothy Dionela', 'dionela st. marilag city', '09123972384', 2),
(4, 'corwin', '$2y$10$ziDdLe02tO9eXMII6PmN9.xBh48dryv5hZft1UU4IHegzFVOxUxr2', 'Kit Baes', 'marikina', '9912534274', 2),
(5, 'test', '$2y$10$ayRLDWv/6fCSzh6X8t7EbeL.kwdTqfEVmpDfNiWruYbVuVZfcYk3.', 'Erik Santos', 'tondo', '09912534274', 1),
(6, 'try', '$2y$10$SOwjVt/jOwG4V1ihfZN.BOqvR0otP98fD7elLHrf97r2Qpp3tkHDW', 'try', 'try', '09912534274', 2),
(7, 'sova', '$2y$10$k48wZAl3Pu7B6RvYA6V7a.VsXvrqHX7O7oThBmSmxgPzBe3mgFcwm', 'Sova', 'marikina', '09167436785', 2);

-- --------------------------------------------------------

--
-- Table structure for table `violations`
--

CREATE TABLE `violations` (
  `id` int(11) NOT NULL,
  `UID` int(11) NOT NULL,
  `SID` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `description` text NOT NULL,
  `penalty` decimal(10,2) DEFAULT NULL,
  `resolved` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `violations`
--

INSERT INTO `violations` (`id`, `UID`, `SID`, `date`, `description`, `penalty`, `resolved`) VALUES
(1, 3, 1, '2025-05-04', 'Caught 6 Tuna (limit is 5)', 500.00, 1),
(2, 1, 2, '2025-05-01', 'Caught Grouper during closed season', 0.00, 1),
(10, 3, 1, '2025-05-02', 'Exceeded by 1 units.', 500.00, 1),
(11, 1, 5, '2025-05-05', 'Exceeded by 6 units.', 72.00, 1),
(12, 1, 5, '2025-05-05', 'Exceeded by 6 units.', 72.00, 1),
(13, 1, 5, '2025-05-05', 'Exceeded by 6 units.', 72.00, 0),
(14, 3, 1, '2025-05-02', 'Exceeded by 1 units.', 500.00, 0),
(15, 3, 1, '2025-05-02', 'Exceeded by 1 units.', 500.00, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `catch_reports`
--
ALTER TABLE `catch_reports`
  ADD PRIMARY KEY (`CRID`);

--
-- Indexes for table `species`
--
ALTER TABLE `species`
  ADD PRIMARY KEY (`SID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UID`);

--
-- Indexes for table `violations`
--
ALTER TABLE `violations`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `catch_reports`
--
ALTER TABLE `catch_reports`
  MODIFY `CRID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `species`
--
ALTER TABLE `species`
  MODIFY `SID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `violations`
--
ALTER TABLE `violations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
