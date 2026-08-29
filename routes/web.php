<?php

use Core\Libraries\Router\Router;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Router::get('/', 'HomeController@index');
Router::get('/login', 'HomeController@login');
Router::get('/api/status', 'HomeController@status');
