<?php

namespace Core;

use Core\Libraries\Database\Database;
use ReflectionClass;

abstract class Model
{
    protected static $table = null;
    protected static $primaryKey = 'id';
    protected static $connection = null;

    public static function getTable()
    {
        if (static::$table !== null) {
            return static::$table;
        }

        $shortName = (new ReflectionClass(static::class))->getShortName();
        $snake = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $shortName));
        return $snake . 's';
    }

    public static function query()
    {
        return Database::table(static::getTable(), static::$connection);
    }

    public static function all()
    {
        return static::query()->get();
    }

    public static function find($id)
    {
        return static::query()->where(static::$primaryKey, $id)->first();
    }

    public static function findBy($column, $value)
    {
        return static::query()->where($column, $value)->first();
    }

    public static function where($column, ...$args)
    {
        return static::query()->where($column, ...$args);
    }

    public static function create($data)
    {
        return static::query()->insert($data);
    }

    public static function update($id, $data)
    {
        return static::query()->where(static::$primaryKey, $id)->update($data);
    }

    public static function delete($id)
    {
        return static::query()->where(static::$primaryKey, $id)->delete();
    }

    public static function count($column = '*')
    {
        return static::query()->count($column);
    }

    public static function __callStatic($method, $arguments)
    {
        return static::query()->$method(...$arguments);
    }
}
