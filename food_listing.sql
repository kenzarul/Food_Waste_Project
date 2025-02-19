-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 19, 2025 at 03:03 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Database: `food_listing`

-- Table structure for table `categorie`
CREATE TABLE `categorie` (
  `id_categorie` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `description` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `donateurs`
CREATE TABLE `donateurs` (
  `id_donor` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `nom_etablissement` varchar(50) NOT NULL,
  `mail` varchar(50) NOT NULL,
  `telephone` bigint(20) NOT NULL,
  `mot_de_passe` varchar(50) NOT NULL,
  PRIMARY KEY (`id_donor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `etablissement`
CREATE TABLE `etablissement` (
  `id_etablissement` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `adresse` varchar(50) DEFAULT NULL,
  `id_donor` int(11) NOT NULL,
  PRIMARY KEY (`id_etablissement`),
  UNIQUE KEY `id_donor` (`id_donor`),
  FOREIGN KEY (`id_donor`) REFERENCES `donateurs` (`id_donor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `recipient`
CREATE TABLE `recipient` (
  `id_rec` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `mail` varchar(50) NOT NULL,
  `mot_de_passe` varchar(50) NOT NULL,
  `telephone` bigint(20) NOT NULL,
  PRIMARY KEY (`id_rec`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `listing`
CREATE TABLE `listing` (
  `id_list` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) DEFAULT NULL,
  `description` varchar(50) DEFAULT NULL,
  `quantité` int(11) DEFAULT NULL,
  `date_expire` date DEFAULT NULL,
  `STATUS` varchar(50) DEFAULT NULL,
  `id_donor` int(11) NOT NULL,
  PRIMARY KEY (`id_list`),
  FOREIGN KEY (`id_donor`) REFERENCES `donateurs` (`id_donor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `reservation`
CREATE TABLE `reservation` (
  `id_reserve` int(11) NOT NULL AUTO_INCREMENT,
  `STATUS` varchar(50) NOT NULL,
  `_date` date DEFAULT NULL,
  `pickup_time` time DEFAULT NULL,
  `id_rec` int(11) NOT NULL,
  `id_list` int(11) NOT NULL,
  PRIMARY KEY (`id_reserve`),
  FOREIGN KEY (`id_rec`) REFERENCES `recipient` (`id_rec`) ON DELETE CASCADE,
  FOREIGN KEY (`id_list`) REFERENCES `listing` (`id_list`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `feedback`
CREATE TABLE `feedback` (
  `id_feed` int(11) NOT NULL AUTO_INCREMENT,
  `id_reserve` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `commentaire` text DEFAULT NULL,
  PRIMARY KEY (`id_feed`),
  FOREIGN KEY (`id_reserve`) REFERENCES `reservation` (`id_reserve`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `peut_avoir`
CREATE TABLE `peut_avoir` (
  `id_list` int(11) NOT NULL,
  `id_categorie` int(11) NOT NULL,
  PRIMARY KEY (`id_list`, `id_categorie`),
  FOREIGN KEY (`id_list`) REFERENCES `listing` (`id_list`) ON DELETE CASCADE,
  FOREIGN KEY (`id_categorie`) REFERENCES `categorie` (`id_categorie`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;
