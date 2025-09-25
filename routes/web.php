<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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
    return "";
});

$router->get('/version', function () use ($router) {
    return $router->app->version();
});

$router->group([

    'prefix' => 'api'

], function ($router) {
    $router->post('login', 'AuthController@login');
    $router->post('logout', 'AuthController@logout');
    $router->post('refresh', 'AuthController@refresh');
    $router->post('user-profile', 'AuthController@me');
    $router->group(['middleware' => ['auth:api']], function () use ($router) {
        $router->post('/groups/create', 'GroupsController@create');
        $router->get('/groups', 'GroupsController@index');
        $router->delete('/groups/{id}', 'GroupsController@destroy');
        $router->put('groups/{id}', 'GroupsController@update');
    });

});

