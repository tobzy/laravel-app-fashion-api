<?php
/**
 * Created by PhpStorm.
 * User: FOREGAN
 * Date: 9/18/2016
 * Time: 3:01 PM
 */

namespace Nattivv\OnlinePayments\Providers;


interface ProviderInterface
{

    const POST = 'POST';
    const GET = 'GET';
    const PUT = 'PUT';
    const DELETE = 'DELETE';



    //public function processHttpResponse($response);
}