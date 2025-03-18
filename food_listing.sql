-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
-- Host: 127.0.0.1
-- Generation Time: Mar 18, 2025 at 03:03 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Database: `food_listing`
CREATE DATABASE IF NOT EXISTS `food_listing`;
USE `food_listing`;

-- Table structure for `categorie`
CREATE TABLE IF NOT EXISTS `categorie` (
  `id_categorie` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `description` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for `donateurs`
CREATE TABLE IF NOT EXISTS `donateurs` (
  `id_donor` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `nom_etablissement` varchar(50) NOT NULL,
  `mail` varchar(50) NOT NULL,
  `telephone` bigint(20) NOT NULL,
  `mot_de_passe` varchar(50) NOT NULL,
  `adresse` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_donor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for `etablissement`
CREATE TABLE IF NOT EXISTS `etablissement` (
  `id_etablissement` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `adresse` varchar(50) DEFAULT NULL,
  `id_donor` int(11) NOT NULL,
  `telephone` bigint(20) NOT NULL,
  PRIMARY KEY (`id_etablissement`),
  UNIQUE KEY `id_donor` (`id_donor`),
  FOREIGN KEY (`id_donor`) REFERENCES `donateurs` (`id_donor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for `recipient`
CREATE TABLE IF NOT EXISTS `recipient` (
  `id_rec` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) NOT NULL,
  `mail` varchar(50) NOT NULL,
  `mot_de_passe` varchar(50) NOT NULL,
  `telephone` bigint(20) NOT NULL,
  PRIMARY KEY (`id_rec`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for `listing`
CREATE TABLE IF NOT EXISTS `listing` (
  `id_list` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) DEFAULT NULL,
  `description` varchar(50) DEFAULT NULL,
  `quantité` int(11) DEFAULT NULL,
  `date_expire` date DEFAULT NULL,
  `STATUS` varchar(50) DEFAULT NULL,
  `id_donor` int(11) NOT NULL,
  PRIMARY KEY (`id_list`),
  FOREIGN KEY (`id_donor`) REFERENCES `donateurs` (`id_donor`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for `reservation`
CREATE TABLE IF NOT EXISTS `reservation` (
  `id_reserve` int(11) NOT NULL AUTO_INCREMENT,
  `STATUS` varchar(50) NOT NULL,
  `_date` date DEFAULT NULL,
  `pickup_time` time DEFAULT NULL,
  `id_rec` int(11) NOT NULL,
  `id_list` int(11) NOT NULL,
  PRIMARY KEY (`id_reserve`),
  FOREIGN KEY (`id_rec`) REFERENCES `recipient` (`id_rec`) ON DELETE CASCADE,
  FOREIGN KEY (`id_list`) REFERENCES `listing` (`id_list`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for `feedback`
CREATE TABLE IF NOT EXISTS `feedback` (
  `id_feed` int(11) NOT NULL AUTO_INCREMENT,
  `id_reserve` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `commentaire` text DEFAULT NULL,
  PRIMARY KEY (`id_feed`),
  FOREIGN KEY (`id_reserve`) REFERENCES `reservation` (`id_reserve`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for `peut_avoir`
CREATE TABLE IF NOT EXISTS `peut_avoir` (
  `id_list` int(11) NOT NULL,
  `id_categorie` int(11) NOT NULL,
  PRIMARY KEY (`id_list`, `id_categorie`),
  FOREIGN KEY (`id_list`) REFERENCES `listing` (`id_list`) ON DELETE CASCADE,
  FOREIGN KEY (`id_categorie`) REFERENCES `categorie` (`id_categorie`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 🔹 Create Views to Secure Data 🔹 --

-- View for Recipients: See donor and listing info, but no private data
CREATE OR REPLACE VIEW recipient_view AS
SELECT 
    d.id_donor, 
    d.nom AS donor_name, 
    d.mail,  
    d.telephone,  
    d.nom_etablissement, 
    e.adresse AS establishment_address, 
    l.id_list, 
    l.type AS food_type, 
    l.description, 
    l.quantité, 
    l.date_expire, 
    l.STATUS
FROM donateurs d
LEFT JOIN etablissement e ON d.id_donor = e.id_donor
LEFT JOIN listing l ON d.id_donor = l.id_donor;

-- View for Donors: See their listings and reservations
CREATE OR REPLACE VIEW donor_reservations AS
SELECT 
    l.id_list, 
    l.type, 
    l.description, 
    l.quantité, 
    l.date_expire, 
    l.STATUS, 
    r.id_reserve,
    r.id_rec,  
    r.STATUS AS reservation_status, 
    r._date, 
    r.pickup_time, 
    rec.nom AS recipient_name, 
    rec.telephone AS recipient_contact
FROM listing l
LEFT JOIN reservation r ON l.id_list = r.id_list
LEFT JOIN recipient rec ON r.id_rec = rec.id_rec;

-- 🔹 Create Users and Grant Access 🔹 --

-- Create a user for recipients
CREATE USER IF NOT EXISTS 'recipient_user'@'localhost' IDENTIFIED BY 'recipient_pass';
GRANT SELECT ON food_listing.recipient_view TO 'recipient_user'@'localhost';

-- Create a user for donors
CREATE USER IF NOT EXISTS 'donor_user'@'localhost' IDENTIFIED BY 'donor_pass';
GRANT SELECT ON food_listing.donor_reservations TO 'donor_user'@'localhost';


COMMIT;
