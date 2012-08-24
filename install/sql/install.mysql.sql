-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 24, 2012 at 10:02 AM
-- Server version: 5.5.24
-- PHP Version: 5.3.10-1ubuntu3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `webfiltration_jelix`
--

-- --------------------------------------------------------

--
-- Table structure for table `ecom_account`
--

CREATE TABLE IF NOT EXISTS `ecom_account` (
  `login` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(50) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `civility` varchar(20) NOT NULL,
  PRIMARY KEY (`login`),
  UNIQUE KEY `nom` (`firstname`,`lastname`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ecom_account_address`
--

CREATE TABLE IF NOT EXISTS `ecom_account_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(50) NOT NULL,
  `label` varchar(50) NOT NULL,
  `civility` varchar(20) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `company` varchar(100) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) NOT NULL,
  `country` varchar(100) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `label` (`user`,`label`),
  KEY `user` (`user`),
  KEY `firstname` (`firstname`,`lastname`),
  KEY `civility` (`civility`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `ecom_cart`
--

CREATE TABLE IF NOT EXISTS `ecom_cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(50) DEFAULT NULL,
  `session` varchar(100) NOT NULL,
  `dao` varchar(100) NOT NULL,
  `foreignkeys` varchar(255) NOT NULL,
  `namefield` varchar(100) NOT NULL,
  `pricefield` varchar(100) NOT NULL,
  `tax` float DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `thumbnail` text,
  `last_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `foreignkeys` (`foreignkeys`),
  KEY `user` (`user`),
  KEY `dao` (`dao`),
  KEY `tax` (`tax`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `ecom_order`
--

CREATE TABLE IF NOT EXISTS `ecom_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reference` varchar(50) NOT NULL,
  `user` varchar(50) NOT NULL,
  `delivery` int(11) NOT NULL,
  `payment` varchar(50) NOT NULL,
  `status` varchar(25) NOT NULL,
  `date_order` timestamp NULL DEFAULT NULL,
  `date_valid` timestamp NULL DEFAULT NULL,
  `date_delivery` timestamp NULL DEFAULT NULL,
  `fact_civility` varchar(20) NOT NULL,
  `fact_firstname` varchar(100) NOT NULL,
  `fact_lastname` varchar(100) NOT NULL,
  `fact_company` varchar(100) DEFAULT NULL,
  `fact_address` varchar(255) NOT NULL,
  `fact_city` varchar(100) NOT NULL,
  `fact_state` varchar(100) DEFAULT NULL,
  `fact_postal_code` varchar(20) NOT NULL,
  `fact_country` varchar(100) NOT NULL,
  `fact_phone` varchar(30) DEFAULT NULL,
  `delivery_civility` varchar(20) NOT NULL,
  `delivery_firstname` varchar(100) NOT NULL,
  `delivery_lastname` varchar(100) NOT NULL,
  `delivery_company` varchar(100) DEFAULT NULL,
  `delivery_address` varchar(255) NOT NULL,
  `delivery_city` varchar(100) NOT NULL,
  `delivery_state` varchar(100) DEFAULT NULL,
  `delivery_postal_code` varchar(20) NOT NULL,
  `delivery_country` varchar(100) NOT NULL,
  `delivery_phone` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference` (`reference`),
  KEY `user` (`user`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `ecom_order_item`
--

CREATE TABLE IF NOT EXISTS `ecom_order_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order` int(11) NOT NULL,
  `dao` varchar(100) NOT NULL,
  `foreignkeys` varchar(255) NOT NULL,
  `namefield` varchar(100) NOT NULL,
  `pricefield` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` float DEFAULT NULL,
  `tax` float DEFAULT NULL,
  `detail` text,
  `quantity` int(11) NOT NULL,
  `thumbnail` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `foreignkeys` (`foreignkeys`),
  KEY `dao` (`dao`),
  KEY `name` (`name`),
  KEY `price` (`price`),
  KEY `tax` (`tax`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
