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
            'database' => STORAGE_PATH . '/database.sqlite',
            'prefix'   => '',
        ],

        'mysql' => [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'port'      => 3306,
            'username'  => 'root',
            'password'  => 'root',
            'database'  => 'meuxsoft',
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
