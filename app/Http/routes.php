<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

//group for the routes linking to the api
Route::group(['prefix' => 'api'], function() {

    Route::post('login', 'Api\Auth\AuthController@login');
    Route::post('register', 'Api\Auth\AuthController@register');

    Route::group(['middleware' => ['jwt.auth', 'jwt.refresh']], function() {
        Route::post('logout', 'Api\AuthController@logout');

        Route::get('test', 'Api\v1\ProfileController@index');
        
        Route::get('tests', function(){
            return response()->json(['foo'=>'bar']);
        });
    });
});
