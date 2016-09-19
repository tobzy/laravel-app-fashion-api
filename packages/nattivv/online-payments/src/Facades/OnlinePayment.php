<?php
namespace Nattivv\OnlinePayments\Facades;

use Illuminate\Support\Facades\Facade;

class OnlinePayment extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'Payment'; }


}