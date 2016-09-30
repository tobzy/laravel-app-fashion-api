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
Route::group(['prefix' => 'v1'], function () {

    Route::post('auth', 'Auth\AuthController@authenticate');
    Route::post('auth/deauth', 'Auth\AuthController@deauthenticate');
    Route::post('auth/create', 'Auth\AuthController@create');
    Route::get('auth/activation/{token}', 'Auth\AuthController@activate')->name('user.activate');

    Route::get('auth/social', 'SocialAuthController@authSocial');


    Route::group(['middleware' => ['jwt.auth']], function () {
        Route::get('users', 'UsersController@authUser');
        Route::get('account', 'AccountController@account');
        Route::put('account/address/update', 'AccountController@updateAddress');
        Route::put('account/email/update', 'AccountController@updateEmail');
    });


    Route::group(['middleware' => ['designer.auth']], function () {
        //the design resourse route to handle all design routes
        Route::resource('/designer/design', 'DesignController', [
            'except' => ['edit', 'create']
        ]);

        Route::get('designers', 'DesignerController@authDesigner');
    });

    //signup verification and confirmation.
    Route::get('register/verify/{confirmationCode}', [
        'as' => 'confirmation_path',
        'uses' => 'DesignerController@confirm'
    ]);

    //the designer registration store post to database
    Route::post('/designer', [
        'uses' => 'DesignerController@store'
    ]);
    //the designer sign in route...
    Route::post('/designer/signin', [
        'uses' => 'DesignerController@signin'
    ]);
//    Route::post('test', function () {
//        $token = JWTAuth::getToken();
//        $a = $token->get();
//        return response()->json([
//            'token' => $a
//        ]);
//    });

});

