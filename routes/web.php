<?php

use Core\Libraries\Router\Router;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Router::get('/', 'HomeController@index');
Router::get('/login', 'HomeController@login');
Router::get('/upload-demo', 'HomeController@uploadDemo');
Router::post('/upload-demo', 'HomeController@handleUploadDemo');

// 1. Örnek: Basit URL Prefix Gruplama (/admin, /admin/dashboard)
Router::group('/admin', function () {
    Router::get('/', 'Admin\DashboardController@index');
    Router::get('/dashboard', 'Admin\DashboardController@index');
});

// 2. Örnek: Prefix + Namespace Gruplama (Otomatik Admin\ namespace'ini ekler)
// Router::group(['prefix' => '/admin', 'namespace' => 'Admin'], function () {
//     Router::get('/users', 'UserController@index'); // App\Controllers\Admin\UserController@index
// });

// 3. Örnek: API Rota Gruplaması (/api/status)
Router::group('/api', function () {
    Router::get('/status', 'HomeController@status');
});
