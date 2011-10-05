-- phpMyAdmin SQL Dump
-- version 2.11.9.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 10, 2010 at 10:24 PM
-- Server version: 5.0.89
-- PHP Version: 5.2.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------

--
-- Table structure for table `retailers`
--

CREATE TABLE IF NOT EXISTS `retailers` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(200) collate utf8_unicode_ci NOT NULL,
  `address` varchar(200) collate utf8_unicode_ci NOT NULL,
  `phone` varchar(20) collate utf8_unicode_ci default NULL,
  `email` varchar(200) collate utf8_unicode_ci default NULL,
  `website` varchar(200) collate utf8_unicode_ci default NULL,
  `lat` float(10,6) NOT NULL,
  `lon` float(10,6) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `name` (`name`,`lat`,`lon`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=48 ;
