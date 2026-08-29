<?php

namespace Core\Libraries\Database;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    /**
     * @var PDO|null
     */
    protected static $pdo = null;

    /**
     * @var string|null
     */
    protected static $activeConnection = null;

    /**
     * Private constructor to prevent direct instantiation.
     */
    private function __construct()
    {
    }

    /**
     * Get or initialize the PDO connection.
     *
     * @param string|null $name Connection name (e.g. 'mysql', 'sqlite')
     * @return PDO
     */
    public static function connect(?string $name = null): PDO
    {
        $dbConfig = function_exists('config') ? config('database') : [];
        if (empty($dbConfig)) {
            $configPath = defined('CONFIG_PATH') ? CONFIG_PATH . '/database.php' : dirname(__DIR__, 3) . '/app/Config/database.php';
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
            throw new RuntimeException('Database Connection Error: ' . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    /**
     * Get active PDO instance.
     *
     * @return PDO
     */
    public static function getPdo(): PDO
    {
        if (self::$pdo === null) {
            return self::connect();
        }
        return self::$pdo;
    }

    /**
     * Start fluent QueryBuilder for a given table.
     *
     * @param string $table
     * @return QueryBuilder
     */
    public static function table(string $table): QueryBuilder
    {
        return new QueryBuilder(self::getPdo(), $table);
    }

    /**
     * Execute a raw SQL query with parameters and return records.
     *
     * @param string $sql
     * @param array $params
     * @return array
     */
    public static function query(string $sql, array $params = []): array
    {
        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Execute a raw SQL statement (INSERT, UPDATE, DELETE, DDL).
     *
     * @param string $sql
     * @param array $params
     * @return bool
     */
    public static function statement(string $sql, array $params = []): bool
    {
        $stmt = self::getPdo()->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Execute raw SQL directly (e.g. migrations/multi-statements).
     *
     * @param string $sql
     * @return int|false
     */
    public static function raw(string $sql)
    {
        return self::getPdo()->exec($sql);
    }

    /**
     * Begin database transaction.
     *
     * @return bool
     */
    public static function beginTransaction(): bool
    {
        return self::getPdo()->beginTransaction();
    }

    /**
     * Commit database transaction.
     *
     * @return bool
     */
    public static function commit(): bool
    {
        return self::getPdo()->commit();
    }

    /**
     * Rollback database transaction.
     *
     * @return bool
     */
    public static function rollback(): bool
    {
        return self::getPdo()->rollBack();
    }

    /**
     * Check if currently within a transaction.
     *
     * @return bool
     */
    public static function inTransaction(): bool
    {
        return self::getPdo()->inTransaction();
    }

    /**
     * Close connection.
     */
    public static function disconnect(): void
    {
        self::$pdo = null;
        self::$activeConnection = null;
    }
}
