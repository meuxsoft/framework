-- ==========================================================
-- PHP 7.3 Static MVC Framework Database Schema
-- Compatible with MySQL (8.0+) / MariaDB & SQLite 3
-- ==========================================================

-- MySQL Table Definition:
CREATE TABLE IF NOT EXISTS `products` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `sku` VARCHAR(100) NULL,
    `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `stock` INT NOT NULL DEFAULT 0,
    `description` TEXT NULL,
    `image` VARCHAR(255) NULL,
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_products_status` (`status`),
    INDEX `idx_products_sku` (`sku`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SQLite Table Definition:
-- CREATE TABLE IF NOT EXISTS products (
--     id INTEGER PRIMARY KEY AUTOINCREMENT,
--     name TEXT NOT NULL,
--     sku TEXT,
--     price REAL NOT NULL DEFAULT 0.00,
--     stock INTEGER NOT NULL DEFAULT 0,
--     description TEXT,
--     image TEXT,
--     status INTEGER NOT NULL DEFAULT 1,
--     created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
--     updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
-- );
