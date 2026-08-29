<?php

namespace App\Modules\Product;

use Core\Libraries\Database\Database;

class Module
{
    /**
     * Boot the Product module.
     *
     * @return void
     */
    public static function boot(): void
    {
        try {
            self::ensureTableExists();
        } catch (\Throwable $e) {
            // Log notice or continue if DB not yet migrated
        }
    }

    /**
     * Create products table if missing.
     *
     * @return void
     */
    protected static function ensureTableExists(): void
    {
        $driver = config('database.default', 'sqlite');

        if ($driver === 'sqlite') {
            Database::raw("
                CREATE TABLE IF NOT EXISTS products (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT NOT NULL,
                    sku TEXT,
                    price REAL NOT NULL DEFAULT 0.00,
                    stock INTEGER NOT NULL DEFAULT 0,
                    description TEXT,
                    image TEXT,
                    status INTEGER NOT NULL DEFAULT 1,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );
            ");
        } else {
            Database::raw("
                CREATE TABLE IF NOT EXISTS products (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    sku VARCHAR(100) NULL,
                    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                    stock INT NOT NULL DEFAULT 0,
                    description TEXT NULL,
                    image VARCHAR(255) NULL,
                    status TINYINT NOT NULL DEFAULT 1,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }
    }
}
