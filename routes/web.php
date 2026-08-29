<?php

use Core\Libraries\Router\Router;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
| Routes are loaded by Core\Bootstrap.
|
*/

Router::get('/', 'HomeController@index');
Router::get('/about', 'HomeController@about');
Router::get('/api/status', 'HomeController@status');
