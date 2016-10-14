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
//        Route::put('account/address/update', 'AccountController@updateAddress');
//        Route::put('account/email/update', 'AccountController@updateEmail');
        Route::get('account/address','AccountController@getAddresses');
        Route::post('account/address','AccountController@newAddress');
        Route::get('account/address/{id}','AccountController@getSingleAddress');
        Route::put('account/address/{id}/update','AccountController@updateAddress');
        Route::put('account/email/update','AccountController@updateEmail');
        Route::get('account/confirm_measurement','MeasurementController@confirmMeasurement');

        Route::get('user/measurement/{option}','MeasurementController@setMeasurements');
        Route::get('account/orders','UsersController@getOrders');
        Route::get('account/orders/{id}','AccountController@getSingleOrder');
        Route::get('user/credit_cards','UsersController@getCreditCards');
        Route::delete('user/credit_cards/{id}/delete','UsersController@deleteCreditCard');

        Route::post('payment/initialise_transaction', 'PaymentController@getAuthUrl');
        Route::get('payment/charge_customer','PaymentController@chargeCustomer');
        Route::get('payment/verify_transaction', 'PaymentController@verifyTransaction');
    });


    Route::group(['middleware' => ['designer.auth']], function () {
        //the design resourse route to handle all design routes
        Route::resource('/designer/design', 'DesignController', [
            'except' => ['edit', 'create']
        ]);

        Route::get('designers', 'DesignerController@authDesigner');
        Route::post('/designer/account/update', 'DesignerController@updateProfile');
        Route::get('design/{image}', 'ImageController@getDesign')->where('image', '^[^/]+$');
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

    Route::post('user/payment/{option}/callback','MeasurementController@payment');


    // Routes for the store and purchase process
    Route::get('store','StoreController@getProducts');
    Route::get('store/item','StoreController@getSingleItem');
    Route::get('materials','StoreController@getMaterials');
    Route::get('material','StoreController@getSingleMaterial');

    });

