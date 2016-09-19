<?php

namespace Nattivv\OnlinePayments;

use Illuminate\Support\ServiceProvider;
use Nattivv\OnlinePayments\Facades\OnlinePayment;

class OnlinePaymentsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/payments.php' => config_path('payments.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        //register the online payment binding for the facade
        $this->registerOnlinePaymentBinding();

    }

    private function registerOnlinePaymentBinding(){
        $this->app->bind('Payment', function ($app) {
            return new OnlinePaymentManager($app);
        });

    }
}
