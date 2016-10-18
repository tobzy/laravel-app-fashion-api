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
Route::group(['prefix' => 'v1'], function() {

    Route::post('auth', 'Auth\AuthController@authenticate');
    Route::post('auth/deauth', 'Auth\AuthController@deauthenticate');
    Route::post('auth/create', 'Auth\AuthController@create');
    Route::get('auth/activation/{token}', 'Auth\AuthController@activate')->name('user.activate');

    Route::get('auth/social', 'SocialAuthController@authSocial');



    Route::group(['middleware' => ['jwt.auth']], function() {
        Route::get('users', 'UsersController@authUser');
        Route::get('account', 'AccountController@account');
        Route::get('account/address','AccountController@getAddresses');
        Route::post('account/address','AccountController@newAddress');
        Route::get('account/address/{id}','AccountController@getSingleAddress');
        Route::put('account/address/{id}/update','AccountController@updateAddress');
        Route::put('account/email/update','AccountController@updateEmail');
        Route::get('account/confirm_measurement','MeasurementController@confirmMeasurement');
        Route::get('account/measurements', 'MeasurementController@getMeasurements');
        Route::put('account/measurements', 'MeasurementController@updateMeasurements');

        Route::get('user/measurement/{option}','MeasurementController@setMeasurements');

        Route::get('account/orders','UsersController@getOrders');
        Route::get('account/orders/{id}','AccountController@getSingleOrder');
        Route::get('user/credit_cards','UsersController@getCreditCards');
        Route::delete('user/credit_cards/{id}/delete','UsersController@deleteCreditCard');

        Route::post('payment/initialise_transaction', 'PaymentController@getAuthUrl');
        Route::get('payment/charge_customer','PaymentController@chargeCustomer');
        Route::get('payment/verify_transaction', 'PaymentController@verifyTransaction');
    });


    Route::get('payment/shipping','PaymentController@getShippingPrice');
    Route::post('user/payment/{option}/callback','MeasurementController@payment');

    Route::get('fitter/confirmation','MeasurementController@confirmFitter');


    // Routes for the store and purchase process
    Route::get('store','StoreController@getProducts');
    Route::get('store/item','StoreController@getSingleItem');
    Route::get('materials','StoreController@getMaterials');
    Route::get('material','StoreController@getSingleMaterial');


});
