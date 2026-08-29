<?php

namespace App\Models;

use Core\Model;

class User extends Model
{
    protected static $table = 'users';
    protected static $primaryKey = 'id';

    public static function getActiveUsers()
    {
        return static::where('status', 1)
            ->orderBy('id', 'DESC')
            ->get();
    }
}
