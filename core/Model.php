<?php

namespace Core;

use Core\Libraries\Database\Database;
use Core\Libraries\Database\QueryBuilder;
use ReflectionClass;

abstract class Model
{
    /**
     * Database table name. If null, automatically inferred from class name (e.g. User -> users).
     *
     * @var string|null
     */
    protected static $table = null;

    /**
     * Primary key column name.
     *
     * @var string
     */
    protected static $primaryKey = 'id';

    /**
     * Custom database connection name (e.g. 'mysql', 'sqlite'). Null uses default.
     *
     * @var string|null
     */
    protected static $connection = null;

    /**
     * Get table name for the model.
     *
     * @return string
     */
    public static function getTable(): string
    {
        if (static::$table !== null) {
            return static::$table;
        }

        $shortName = (new ReflectionClass(static::class))->getShortName();
        // Convert CamelCase to snake_case plural (e.g. User -> users, BlogPost -> blog_posts)
        $snake = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $shortName));
        return $snake . 's';
    }

    /**
     * Get QueryBuilder instance for this model.
     *
     * @return QueryBuilder
     */
    public static function query(): QueryBuilder
    {
        return Database::table(static::getTable(), static::$connection);
    }

    /**
     * Get all records.
     *
     * @return array
     */
    public static function all(): array
    {
        return static::query()->get();
    }

    /**
     * Find a record by its primary key.
     *
     * @param mixed $id
     * @return array|null
     */
    public static function find($id): ?array
    {
        return static::query()->where(static::$primaryKey, $id)->first();
    }

    /**
     * Find first record matching a column value.
     *
     * @param string $column
     * @param mixed $value
     * @return array|null
     */
    public static function findBy(string $column, $value): ?array
    {
        return static::query()->where($column, $value)->first();
    }

    /**
     * Start a WHERE condition query.
     *
     * @param string $column
     * @param mixed ...$args
     * @return QueryBuilder
     */
    public static function where(string $column, ...$args): QueryBuilder
    {
        return static::query()->where($column, ...$args);
    }

    /**
     * Insert a new record into database and return last inserted ID.
     *
     * @param array $data
     * @return int|string
     */
    public static function create(array $data)
    {
        return static::query()->insert($data);
    }

    /**
     * Update records by primary key.
     *
     * @param mixed $id
     * @param array $data
     * @return int Number of affected rows
     */
    public static function update($id, array $data): int
    {
        return static::query()->where(static::$primaryKey, $id)->update($data);
    }

    /**
     * Delete record by primary key.
     *
     * @param mixed $id
     * @return int Number of affected rows
     */
    public static function delete($id): int
    {
        return static::query()->where(static::$primaryKey, $id)->delete();
    }

    /**
     * Get total record count.
     *
     * @param string $column
     * @return int
     */
    public static function count(string $column = '*'): int
    {
        return static::query()->count($column);
    }

    /**
     * Dynamic static call forwarding to QueryBuilder (e.g. User::orderBy('id', 'DESC')->get()).
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic(string $method, array $arguments)
    {
        return static::query()->$method(...$arguments);
    }
}
