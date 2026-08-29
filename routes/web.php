<?php

use Core\Libraries\Router\Router;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Define application web routes here.
| Example: Router::get('/example', 'ExampleController@index');
|
*/

Router::get('/', 'HomeController@index');
Router::get('/api/status', 'HomeController@status');
