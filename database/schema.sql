-- Database: login
-- Schema for fresh installations (English column names)

CREATE DATABASE IF NOT EXISTS `login` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `login`;

-- Table: users
CREATE TABLE IF NOT EXISTS `users` (
  `id`         int(11)      NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name`  varchar(100) NOT NULL,
  `email`      varchar(100) NOT NULL DEFAULT '',
  `username`   varchar(50)  NOT NULL,
  `password`   varchar(255) NOT NULL,
  `is_admin`   tinyint(1)   NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email`    (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Table: password_resets
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id`         int(11)      NOT NULL AUTO_INCREMENT,
  `email`      varchar(255) NOT NULL,
  `token`      varchar(255) NOT NULL,
  `created_at` timestamp    NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp    NULL     DEFAULT NULL,
  `used`       tinyint(1)            DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
