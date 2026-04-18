-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 22, 2014 at 09:16 AM
-- Server version: 5.6.12-log
-- PHP Version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `khuzam_industries`
--

-- --------------------------------------------------------

--
-- Table structure for table `wl_meta_tags`
--

CREATE TABLE IF NOT EXISTS `wl_meta_tags` (
  `meta_id` int(11) NOT NULL AUTO_INCREMENT,
  `is_fixed` enum('Y','N') NOT NULL DEFAULT 'N',
  `entity_type` varchar(80) DEFAULT NULL COMMENT 'name of controllers  ',
  `entity_id` int(11) NOT NULL DEFAULT '0',
  `page_url` varchar(200) NOT NULL,
  `meta_title` varchar(80) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `meta_description` varchar(220) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `meta_keyword` varchar(460) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `page_url` (`page_url`),
  KEY `entity_type` (`entity_type`),
  KEY `entity_id` (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
