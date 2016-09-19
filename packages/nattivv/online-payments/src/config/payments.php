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

    /*
     * Sets weather to verify the ssl for either of the API call
     * default set to false for development mode.
     * if in deployment and ssl certificate is available, set the path
     * to the .pem*/
    'verify'=>false,

    /*
     * Path to the SSL certificate for verification
     * */
    'ssl_cert' => '',

    'paystark' => [
        'secrete_key'=>'',
        'public_key'=>'',
        'callback_url'=>'',
    ],

    'paypal'=>[
        'secrete_key'=>'',
        'public_key'=>'',
        'callback_url'=>'',
    ]
];