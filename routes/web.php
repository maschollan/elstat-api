<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Http\Controllers\AuthController;

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

$router->post('/login', 'AuthController@login');
$router->post('/register', 'AuthController@register');
$router->post('/verify', 'AuthController@verify');



$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->get('/class', 'DataController@class');
    $router->get('/materials', 'DataController@materials');
    $router->get('/history', 'DataController@history');

    $router->post('/class', 'DataController@createClass');
    $router->post('/joinclass', 'DataController@joinClass');
    $router->post('/class/edit', 'DataController@editClass');
    $router->post('/class/delete', 'DataController@deleteClass');

    $router->post('/material', 'DataController@createMaterial');
    $router->post('/material/edit', 'DataController@editMaterial');
    $router->post('/material/delete', 'DataController@deleteMaterial');

    $router->post('/quiz', 'DataController@createQuiz');
    $router->post('/quiz/delete', 'DataController@deleteQuiz');

    $router->post('/quiz/pass', 'DataController@passQuiz');
});
