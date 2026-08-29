<?php

namespace App\Modules\Product\Models;

use Core\Libraries\Database\Database;

class Product
{
    /**
     * @var string
     */
    protected static $table = 'products';

    /**
     * Get all products with optional order.
     *
     * @param string $orderBy
     * @param string $direction
     * @return array
     */
    public static function all(string $orderBy = 'id', string $direction = 'DESC'): array
    {
        return Database::table(self::$table)
            ->orderBy($orderBy, $direction)
            ->get();
    }

    /**
     * Find product by primary key ID.
     *
     * @param int|string $id
     * @return array|null
     */
    public static function find($id): ?array
    {
        return Database::table(self::$table)->find($id);
    }

    /**
     * Create a new product.
     *
     * @param array $data
     * @return int|string Last inserted ID
     */
    public static function create(array $data)
    {
        $now = date('Y-m-d H:i:s');
        $data['created_at'] = $now;
        $data['updated_at'] = $now;

        return Database::table(self::$table)->insert($data);
    }

    /**
     * Update existing product by ID.
     *
     * @param int|string $id
     * @param array $data
     * @return int
     */
    public static function update($id, array $data): int
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        return Database::table(self::$table)
            ->where('id', $id)
            ->update($data);
    }

    /**
     * Delete product by ID.
     *
     * @param int|string $id
     * @return int
     */
    public static function delete($id): int
    {
        return Database::table(self::$table)
            ->where('id', $id)
            ->delete();
    }
}
