<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Database Driver
    |--------------------------------------------------------------------------
    | Options: 'sqlite', 'mysql'
    */
    'default' => 'mysql',

    'connections' => [
        'sqlite' => [
            'driver'   => 'sqlite',
            'database' => defined('STORAGE_PATH') ? STORAGE_PATH . '/database.sqlite' : dirname(__DIR__, 2) . '/storage/database.sqlite',
            'prefix'   => '',
        ],

        'mysql' => [
            'driver'    => 'mysql',
            'host'      => '127.0.0.1',
            'port'      => 3306,
            'database'  => 'meuxsoft',
            'username'  => 'root',
            'password'  => 'root',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'options'   => [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ],
        ],
    ],
];
