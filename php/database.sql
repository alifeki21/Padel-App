-- Run this file once in phpMyAdmin (or MySQL CLI) before using

CREATE DATABASE IF NOT EXISTS `padel_app`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `padel_app`;

CREATE TABLE IF NOT EXISTS `users` (
    `id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `first_name`         VARCHAR(50)  NOT NULL,
    `last_name`          VARCHAR(50)  NOT NULL,
    `email`              VARCHAR(150) NOT NULL UNIQUE,
    `phone`              VARCHAR(20)  NOT NULL,
    `password_hash`      VARCHAR(255) NOT NULL,
    `skill_level`        DECIMAL(3,1) NOT NULL DEFAULT 1.0,
    `preferred_position` ENUM('left','right','both') NOT NULL DEFAULT 'both',
    `playing_hand`       ENUM('left','right')        NOT NULL DEFAULT 'right',
    `created_at`         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
