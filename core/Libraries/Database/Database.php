<?php

namespace Core\Libraries\Database;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    protected static $pdo = null;
    protected static $activeConnection = null;

    private function __construct()
    {
    }

    public static function connect($name = null)
    {
        $dbConfig = function_exists('config') ? config('database') : [];
        if (empty($dbConfig)) {
            $configPath = CONFIG_PATH . '/database.php';
            if (file_exists($configPath)) {
                $dbConfig = require $configPath;
            }
        }

        $connectionName = $name ?: ($dbConfig['default'] ?? 'sqlite');

        if (self::$pdo !== null && self::$activeConnection === $connectionName) {
            return self::$pdo;
        }

        $config = $dbConfig['connections'][$connectionName] ?? null;
        if (!$config) {
            throw new RuntimeException("Database connection [{$connectionName}] is not configured.");
        }

        $driver = $config['driver'] ?? 'sqlite';

        try {
            if ($driver === 'sqlite') {
                $dbPath = $config['database'];
                $dir = dirname($dbPath);
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                $dsn = "sqlite:{$dbPath}";
                $pdo = new PDO($dsn, null, null, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
                $pdo->exec('PRAGMA foreign_keys = ON;');
            } elseif ($driver === 'mysql') {
                $dsn = sprintf(
                    'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                    $config['host'] ?? '127.0.0.1',
                    $config['port'] ?? 3306,
                    $config['database'] ?? '',
                    $config['charset'] ?? 'utf8mb4'
                );
                $options = $config['options'] ?? [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ];
                $pdo = new PDO($dsn, $config['username'] ?? 'root', $config['password'] ?? '', $options);
            } else {
                throw new RuntimeException("Unsupported database driver [{$driver}].");
            }

            self::$pdo = $pdo;
            self::$activeConnection = $connectionName;
            return self::$pdo;
        } catch (PDOException $e) {
            throw new RuntimeException('Database Connection Error: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    public static function getPdo()
    {
        if (self::$pdo === null) {
            return self::connect();
        }
        return self::$pdo;
    }

    public static function table($table)
    {
        return new QueryBuilder(self::getPdo(), $table);
    }

    public static function query($sql, $params = [])
    {
        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public static function statement($sql, $params = [])
    {
        $stmt = self::getPdo()->prepare($sql);
        return $stmt->execute($params);
    }

    public static function raw($sql)
    {
        return self::getPdo()->exec($sql);
    }

    public static function beginTransaction()
    {
        return self::getPdo()->beginTransaction();
    }

    public static function commit()
    {
        return self::getPdo()->commit();
    }

    public static function rollback()
    {
        return self::getPdo()->rollBack();
    }

    public static function inTransaction()
    {
        return self::getPdo()->inTransaction();
    }

    public static function disconnect()
    {
        self::$pdo = null;
        self::$activeConnection = null;
    }
}
