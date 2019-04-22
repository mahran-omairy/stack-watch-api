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


 // public access endpoints
 
$router->post('user/login', 'UserController@login');
$router->post('user/register','UserController@register');


// endpoints guarded with jwt.auth middleware
$router->group(
    ['middleware' => 'jwt.auth'],
    function () use ($router) {
        // users endpoints
        $router->get('user','UserController@show');
        $router->put('user','UserController@update');
        $router->put('user/update-password','UserController@updatePassword');
        $router->delete('user','UserController@destroy');


        // category endpoints
        $router->get('category/date','CategoryController@listMonth');
        $router->get('category/{id}','CategoryController@show');
        $router->post('category','CategoryController@create');
        $router->put('category/{id}','CategoryController@update');
        $router->delete('category/{id}','CategoryController@destroy');
        $router->get('categories','CategoryController@list');
        

        // envelops endpoints
        $router->post('envelop','EnvelopController@create');
        $router->put('envelop/{id}','EnvelopController@update');
        $router->delete('envelop/{id}','EnvelopController@destroy');
        $router->get('envelops/surmmary','EnvelopController@summary');

        // settings endpoints
        $router->get('settings','SettingsController@show');

    }
);
