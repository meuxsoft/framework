<?php

use Core\Libraries\Router\Router;

/*
|--------------------------------------------------------------------------
| Blog Module Routes
|--------------------------------------------------------------------------
|
| Modüle ait rotalar burada tanımlanır.
|
*/

Router::get('/blog', 'BlogController@index');
Router::get('/blog/{id}', 'BlogController@show');
