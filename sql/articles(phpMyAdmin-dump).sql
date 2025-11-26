-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: MySQL-5.7
-- Created: Nov 26 2025, 18:28
-- Server: 5.7.44
-- PHP: 8.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- DB: `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `short_desc` varchar(255) NOT NULL,
  `full_desc` text NOT NULL,
  `ctgry` int(11) NOT NULL DEFAULT '1',
  `article_status` enum('moderation','approved','declined') NOT NULL,
  `id_author` int(11) NOT NULL,
  `added_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `edited_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `a_catgries` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `a_catgries` (`id`, `name`) VALUES
(1, 'No category'),
(2, 'Science'),
(3, 'Cinema'),
(4, 'Animation'),
(5, 'Games');

CREATE TABLE `a_comments` (
  `id` int(11) NOT NULL,
  `id_article` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `msg` varchar(512) NOT NULL,
  `added_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `moderation_decisions` (
  `id` int(11) NOT NULL,
  `id_article` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `decision` int(11) NOT NULL,
  `decision_time` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ranks` (
  `id` int(11) NOT NULL,
  `name` varchar(55) NOT NULL,
  `approved_power` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `ranks` (`id`, `name`, `approved_power`) VALUES
(1, 'Simple User', 0),
(2, 'Moderator', 3),
(3, 'Administrator', 5);

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nick` varchar(55) NOT NULL,
  `email` varchar(128) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `avatar` varchar(5) DEFAULT '0',
  `rank` int(11) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ctgry` (`ctgry`),
  ADD KEY `id_author` (`id_author`);

ALTER TABLE `a_catgries`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `a_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_article` (`id_article`),
  ADD KEY `id_user` (`id_user`);

ALTER TABLE `moderation_decisions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_article` (`id_article`),
  ADD KEY `id_user` (`id_user`);

ALTER TABLE `ranks`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rank` (`rank`);

ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `a_catgries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `a_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `moderation_decisions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ranks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `articles`
  ADD CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`ctgry`) REFERENCES `a_catgries` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `articles_ibfk_2` FOREIGN KEY (`id_author`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

ALTER TABLE `a_comments`
  ADD CONSTRAINT `a_comments_ibfk_1` FOREIGN KEY (`id_article`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `a_comments_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

ALTER TABLE `moderation_decisions`
  ADD CONSTRAINT `moderation_decisions_ibfk_1` FOREIGN KEY (`id_article`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `moderation_decisions_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`rank`) REFERENCES `ranks` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
