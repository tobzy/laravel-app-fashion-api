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
    Route::post('auth/email/sendconfirmation', 'Auth\AuthController@resendConfirmationLink');

    Route::put('auth/password_reset_email','Auth\PasswordController@sendResetPasswordEmail');
    Route::get('auth/password_reset','Auth\PasswordController@resetPassword')->name('user.password.reset');

    Route::get('auth/social', 'SocialAuthController@authSocial');

    Route::get('product/{uuid}/image','StoreController@getProductImage');
    Route::get('image/{id}','StoreController@getImage');


    Route::group(['middleware' => ['jwt.auth']], function() {
        Route::get('users', 'UsersController@authUser');
        Route::get('account', 'AccountController@account');
        Route::post('account/password/update', 'Auth\PasswordController@changePassword');
        Route::get('account/address','AccountController@getAddresses');
        Route::post('account/address','AccountController@newAddress');
        Route::get('account/address/{id}','AccountController@getSingleAddress');
        Route::put('account/address/{id}/update','AccountController@updateAddress');
        Route::put('account/email/update','AccountController@updateEmail');
        Route::get('account/confirm_measurement','MeasurementController@confirmMeasurement');
        Route::get('account/measurements', 'MeasurementController@getMeasurements');
        Route::put('account/measurements', 'MeasurementController@updateMeasurements');

        Route::get('user/measurement/{option}','MeasurementController@setMeasurements');

        Route::post('order/add','StoreController@addOrder');
        Route::patch('order/content/{uuid}','StoreController@updateOrderContent');
        Route::delete('order/{id}/delete','StoreController@deleteOrderContent');


        Route::get('account/cart','UsersController@getCart');
        Route::get('account/orders','UsersController@getOrders');
        Route::get('account/orders/{id}','AccountController@getSingleOrder');
        Route::get('user/credit_cards','UsersController@getCreditCards');
        Route::delete('user/credit_cards/{id}/delete','UsersController@deleteCreditCard');

        Route::post('payment/initialise_transaction', 'PaymentController@getAuthUrl');
        Route::get('payment/charge_customer','PaymentController@chargeCustomer');
        Route::post('payment/verify_transaction', 'PaymentController@verifyTransaction');
    });


    Route::get('payment/shipping','PaymentController@getShippingPrice');
    Route::post('user/payment/{option}/callback','MeasurementController@payment');

    Route::get('fitter/confirmation','MeasurementController@confirmFitter');


    // Routes for the store and purchase process
    Route::get('store','StoreController@getProducts');
    Route::get('store/new_items','StoreController@getNewProducts');
    Route::get('store/item','StoreController@getSingleItem');


    Route::get('materials','StoreController@getMaterials');
    Route::get('material','StoreController@getSingleMaterial');
    Route::get('material/new_materials','StoreController@getNewMaterials');

    Route::group(['middleware' => ['designer.auth']], function () {
        //the design resourse route to handle all design routes
        Route::resource('/designer/design', 'DesignController', [
            'except' => ['edit', 'create']
        ]);

        Route::get('designers', 'DesignerController@authDesigner');
        Route::post('/designer/account/update', 'DesignerController@updateProfile');
        Route::get('design/{image}', 'ImageController@getDesign')->where('image', '^[^/]+$')->middleware('img.src');

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

    Route::post('/designer/search','DesignerController@searchDesigners');
    Route::get('/designer', 'DesignerController@getDesigner');



    //admin dashboard

    //login->
    Route::post('/admin/auth','AdminAuthController@authenticate');


    //put authenticated routes here....
    Route::group(['middleware' => ['admin.auth']], function () {
        //the admin resourse route to handle all design routes


    });


});
