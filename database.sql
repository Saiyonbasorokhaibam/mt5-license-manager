-- MetaTrader 5 EA License Manager Database Schema

CREATE DATABASE IF NOT EXISTS `mt5_license_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `mt5_license_db`;

CREATE TABLE IF NOT EXISTS `licenses` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `customer_name` VARCHAR(100) NOT NULL,
    `customer_email` VARCHAR(100) NOT NULL,
    `account_number` INT NOT NULL,
    `plan` VARCHAR(20) NOT NULL, -- '1month', '3month', '1year', 'lifetime'
    `license_key` VARCHAR(50) NOT NULL UNIQUE,
    `expiry_days` INT NOT NULL,
    `expiry_date` DATE NOT NULL,
    `status` VARCHAR(20) DEFAULT 'active', -- 'active', 'revoked', 'expired'
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
