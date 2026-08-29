<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Name & Environment
    |--------------------------------------------------------------------------
    */
    'name'     => 'PHP 7.3 Static MVC',
    'env'      => 'development', // 'development' or 'production'
    'debug'    => true,
    'url'      => 'https://localhost',
    'timezone' => 'Europe/Istanbul',
    'charset'  => 'UTF-8',

    /*
    |--------------------------------------------------------------------------
    | Active Modules
    |--------------------------------------------------------------------------
    | Modules registered automatically on application startup.
    */
    'modules'  => [
        'Product',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Layout
    |--------------------------------------------------------------------------
    */
    'default_layout' => 'main',
];
