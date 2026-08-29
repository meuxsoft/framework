<?php

namespace App\Models;

use Core\Model;

/**
 * Example User Model
 * 
 * Veritabanındaki 'users' tablosunu temsil eder.
 */
class User extends Model
{
    /**
     * Tablo Adı (Opsiyonel):
     * Belirtilmezse sınıf adından otomatik üretilir (Örn: User -> 'users').
     *
     * @var string|null
     */
    protected static $table = 'users';

    /**
     * Birincil Anahtar (Opsiyonel):
     * Varsayılan değer 'id' dir.
     *
     * @var string
     */
    protected static $primaryKey = 'id';

    /**
     * İsteğe bağlı özel model fonksiyonları ekleyebilirsiniz:
     * 
     * @return array
     */
    public static function getActiveUsers(): array
    {
        return static::where('status', 1)
            ->orderBy('id', 'DESC')
            ->get();
    }
}
