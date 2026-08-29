<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Name & Environment
    |--------------------------------------------------------------------------
    */
    'name'     => 'Meuxsoft Framework',
    'env'      => 'development', // 'development' or 'production'
    'debug'    => true,
    'url'      => 'https://localhost',
    'timezone' => 'Europe/Istanbul',
    'charset'  => 'UTF-8',

    /*
    |--------------------------------------------------------------------------
    | Active Modules
    |--------------------------------------------------------------------------
    | Register modules located under app/Modules/ (e.g. ['Auth', 'Admin'])
    */
    'modules'  => [
        'Blog'
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Layout
    |--------------------------------------------------------------------------
    */
    'default_layout' => 'main',
];
