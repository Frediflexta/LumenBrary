<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

/**
 * Lumenbrary Routes
 */
$router->group(['prefix' => 'api/v1'], function() use($router) {
    /**
     * Auth
     */
    $router->group(['prefix' => 'auth'], function() use($router) {
        $router->post('/register', 'UserController@register');
        $router->post('/login', 'UserController@login');
    });

    /**
     * Unprotected Routes
     */
    $router->group(['prefix' => '/books'], function() use($router) {
        $router->get('/', 'BooksController@index');
        $router->get('/{id}', 'BooksController@show');
    });

    $router->group(['prefix' => '/authors'], function() use($router) {
        $router->get('/', 'AuthorController@index');
        $router->get('/{id}', 'AuthorController@show');
    });

    /**
     * Protected Routes
     */
    $router->group(['middleware' => 'jwt.auth'], function() use(&$router) {
        $router->group(['prefix' => '/books'], function() use($router) {
            $router->post('/', 'BooksController@store');
            $router->put('/{id}', 'BooksController@update');
            $router->delete('/{id}', 'BooksController@destroy');
        });

        $router->group(['prefix' => '/authors'], function() use($router) {
            $router->post('/', 'AuthorController@store');
            $router->put('/{id}', 'AuthorController@update');
            $router->delete('/{id}', 'AuthorController@destroy');
        });
    });
});
