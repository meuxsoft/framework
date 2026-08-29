-- ==========================================================
-- Meuxsoft Framework Example Database Schema & Seed Data
-- Compatible with MySQL 8.0+ / MariaDB & SQLite 3
-- ==========================================================

-- MySQL Table Definition:
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_users_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Seed Data (MySQL):
INSERT INTO `users` (`name`, `email`, `password`, `status`) VALUES
('Ercan Ulucan', 'ercan@meuxsoft.com.tr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
('Ahmet Yılmaz', 'ahmet@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
('Ayşe Demir', 'ayse@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- SQLite Table Definition & Seed:
-- CREATE TABLE IF NOT EXISTS users (
--     id INTEGER PRIMARY KEY AUTOINCREMENT,
--     name TEXT NOT NULL,
--     email TEXT NOT NULL UNIQUE,
--     password TEXT NOT NULL,
--     status INTEGER NOT NULL DEFAULT 1,
--     created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
--     updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
-- );
-- INSERT OR IGNORE INTO users (id, name, email, password, status) VALUES
-- (1, 'Ercan Ulucan', 'ercan@meuxsoft.com.tr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
-- (2, 'Ahmet Yılmaz', 'ahmet@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);
