-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 04, 2025 at 01:37 PM
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
-- Database: `card_game`
--

-- --------------------------------------------------------

--
-- Table structure for table `cards`
--

CREATE TABLE `cards` (
  `card_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `type` varchar(20) DEFAULT NULL CHECK (`type` in ('Attack','Skill','Power','Status','Curse')),
  `rarity` varchar(20) DEFAULT NULL CHECK (`rarity` in ('Common','Uncommon','Rare','Special')),
  `energy_cost` int(11) NOT NULL,
  `description` text NOT NULL,
  `character_id` int(11) DEFAULT NULL,
  `is_starter` tinyint(1) DEFAULT 0,
  `starter_quantity` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `characters`
--

CREATE TABLE `characters` (
  `character_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `base_hp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `characters`
--

INSERT INTO `characters` (`character_id`, `name`, `base_hp`) VALUES
(1, 'Ironclad', 80);

-- --------------------------------------------------------

--
-- Table structure for table `enemies`
--

CREATE TABLE `enemies` (
  `enemy_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `hp` int(11) NOT NULL,
  `type` varchar(20) DEFAULT NULL CHECK (`type` in ('Normal','Elite','Boss'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `players`
--

CREATE TABLE `players` (
  `player_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `players`
--

INSERT INTO `players` (`player_id`, `username`, `email`, `password_hash`, `created_at`, `last_login`) VALUES
(1, 'admin', 'admin@admin.admin', '$2y$10$3J43upQECCIgIw0O08ouNuhHpyrIvDVhakjl59rJCKGrpkzfG8mLu', '2025-04-03 07:41:44', '2025-04-04 11:19:58'),
(2, 'asd', 'a@a.a', '$2y$10$xAzhN.MfyXI2/syn1VZBK.NqCEE0ASRwNP5a5BoNffwDGj9/JlRwi', '2025-04-03 07:42:37', '0000-00-00 00:00:00'),
(3, 'asdf', 'asdg@f.r', '$2y$10$72lTTGXLs2hiF8kZstm55.PcJvwCBlAXaccUy2sgh9d5ynvO5Q1mW', '2025-04-03 07:48:16', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `relics`
--

CREATE TABLE `relics` (
  `relic_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `rarity` varchar(20) DEFAULT NULL CHECK (`rarity` in ('Common','Uncommon','Rare','Boss','Shop','Event')),
  `effect_type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `runs`
--

CREATE TABLE `runs` (
  `run_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `character_id` int(11) NOT NULL,
  `start_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `end_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `floor_reached` int(11) DEFAULT NULL,
  `victory` tinyint(1) DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `ascension_level` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `run_details`
--

CREATE TABLE `run_details` (
  `detail_id` int(11) NOT NULL,
  `run_id` int(11) NOT NULL,
  `floor` int(11) DEFAULT NULL,
  `detail_type` varchar(20) DEFAULT NULL CHECK (`detail_type` in ('card','relic','encounter')),
  `card_id` int(11) DEFAULT NULL,
  `relic_id` int(11) DEFAULT NULL,
  `encounter_type` varchar(20) DEFAULT NULL CHECK (`encounter_type` in ('Monster','Elite','Boss','Rest','Merchant','Treasure','Event')),
  `quantity` int(11) DEFAULT 1,
  `result` varchar(20) DEFAULT NULL CHECK (`result` in ('Victory','Defeat','Fled','Completed'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cards`
--
ALTER TABLE `cards`
  ADD PRIMARY KEY (`card_id`),
  ADD KEY `character_id` (`character_id`);

--
-- Indexes for table `characters`
--
ALTER TABLE `characters`
  ADD PRIMARY KEY (`character_id`);

--
-- Indexes for table `enemies`
--
ALTER TABLE `enemies`
  ADD PRIMARY KEY (`enemy_id`);

--
-- Indexes for table `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`player_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `relics`
--
ALTER TABLE `relics`
  ADD PRIMARY KEY (`relic_id`);

--
-- Indexes for table `runs`
--
ALTER TABLE `runs`
  ADD PRIMARY KEY (`run_id`),
  ADD KEY `player_id` (`player_id`),
  ADD KEY `character_id` (`character_id`);

--
-- Indexes for table `run_details`
--
ALTER TABLE `run_details`
  ADD PRIMARY KEY (`detail_id`),
  ADD KEY `run_id` (`run_id`),
  ADD KEY `card_id` (`card_id`),
  ADD KEY `relic_id` (`relic_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cards`
--
ALTER TABLE `cards`
  MODIFY `card_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `characters`
--
ALTER TABLE `characters`
  MODIFY `character_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `enemies`
--
ALTER TABLE `enemies`
  MODIFY `enemy_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `players`
--
ALTER TABLE `players`
  MODIFY `player_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `relics`
--
ALTER TABLE `relics`
  MODIFY `relic_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `runs`
--
ALTER TABLE `runs`
  MODIFY `run_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `run_details`
--
ALTER TABLE `run_details`
  MODIFY `detail_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cards`
--
ALTER TABLE `cards`
  ADD CONSTRAINT `cards_ibfk_1` FOREIGN KEY (`character_id`) REFERENCES `characters` (`character_id`);

--
-- Constraints for table `runs`
--
ALTER TABLE `runs`
  ADD CONSTRAINT `runs_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `players` (`player_id`),
  ADD CONSTRAINT `runs_ibfk_2` FOREIGN KEY (`character_id`) REFERENCES `characters` (`character_id`);

--
-- Constraints for table `run_details`
--
ALTER TABLE `run_details`
  ADD CONSTRAINT `run_details_ibfk_1` FOREIGN KEY (`run_id`) REFERENCES `runs` (`run_id`),
  ADD CONSTRAINT `run_details_ibfk_2` FOREIGN KEY (`card_id`) REFERENCES `cards` (`card_id`),
  ADD CONSTRAINT `run_details_ibfk_3` FOREIGN KEY (`relic_id`) REFERENCES `relics` (`relic_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
