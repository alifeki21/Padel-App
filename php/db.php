<?php
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'padel_app');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
function getDbConnection(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }
    $dsnNoDb = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';charset=' . DB_CHARSET;
    try {
        $temp = new PDO($dsnNoDb, DB_USER, DB_PASS);
        $temp->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "`
                     CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $temp = null;
    } catch (PDOException $e) {
        die('Cannot create database: ' . $e->getMessage());
    }
    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `users` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `first_name` VARCHAR(50) NOT NULL,
                `last_name` VARCHAR(50) NOT NULL,
                `email` VARCHAR(150) NOT NULL UNIQUE,
                `phone` VARCHAR(20) NOT NULL,
                `password_hash` VARCHAR(255) NOT NULL,
                `skill_level` DECIMAL(3,1) NOT NULL DEFAULT 1.0,
                `preferred_position` ENUM('left','right','both') NOT NULL DEFAULT 'both',
                `playing_hand` ENUM('left','right') NOT NULL DEFAULT 'right',
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                INDEX `idx_email` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    } catch (PDOException $e) {
        die('Database connection error: ' . $e->getMessage());
    }

    return $pdo;
}

function currentUser(): ?array
{
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    return [
        'id'        => $_SESSION['user_id'],
        'firstName' => $_SESSION['first_name'] ?? '',
        'lastName'  => $_SESSION['last_name']  ?? '',
        'email'     => $_SESSION['email']      ?? '',
    ];
}

function projectUrl(string $relative): string
{
    $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
    $base      = preg_replace('#/php$#', '', $scriptDir);
    return $base . '/' . ltrim($relative, '/');
}