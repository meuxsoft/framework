<?php

use Core\Libraries\Router\Router;

/*
|--------------------------------------------------------------------------
| Product Module Routes
|--------------------------------------------------------------------------
*/

Router::get('/products', 'ProductController@index');
Router::get('/products/create', 'ProductController@create');
Router::post('/products', 'ProductController@store');
Router::get('/products/{id}', 'ProductController@show');
Router::get('/products/{id}/edit', 'ProductController@edit');
Router::post('/products/{id}/update', 'ProductController@update');
Router::put('/products/{id}', 'ProductController@update');
Router::post('/products/{id}/delete', 'ProductController@delete');
Router::delete('/products/{id}', 'ProductController@delete');
