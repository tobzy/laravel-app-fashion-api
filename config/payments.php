<?php

return[
    /*
      |--------------------------------------------------------------------------
      | Third Party Payment-Gateway Services
      |--------------------------------------------------------------------------
      |
      | This file is for storing the credentials for third party services such
      | as Paystark, Quickteller, PayPal, and others. This file provides a sane
      | default location for this type of information, allowing packages
      | to have a conventional place to find your various credentials.
      |
      */

    'paystark' => [
        'secrete_key'=>'sk_test_cb06034ec044b0541de7a56cfd8cd1d0c88038b9',
        'public_key'=>'pk_test_bb1a56cdf665fd0d9a8478b1947e8a95506c7110',
        'callback_url'=>'',
    ],

    'paypal'=>[
        'secrete_key'=>'',
        'public_key'=>'',
        'callback_url'=>'',
    ]
];