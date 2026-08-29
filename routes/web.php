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
Router::get('/admin', 'Admin\DashboardController@index');
Router::get('/api/status', 'HomeController@status');
