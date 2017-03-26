<?php
namespace Nattivv\OnlinePayments;

use InvalidArgumentException;
use Illuminate\Support\Manager;
use Nattivv\OnlinePayments\Contracts\Factory;

class OnlinePaymentManager extends Manager implements Factory
{

    /**
     * Create an instance of the specified driver.
     *
     * @return \Laravel\Socialite\Two\AbstractProvider
     */
    protected function createPaystarkDriver()
    {
        $config = $this->app['config']['payments.paystark'];

        return $this->buildProvider(
            'Nattivv\OnlinePayments\Providers\PaystarkProvider', $config
        );
    }

    /**
     * Build an OAuth 2 provider instance.
     *
     * @param  string  $provider
     * @param  array  $config
     * @return \Laravel\Socialite\Two\AbstractProvider
     */
    public function buildProvider($provider, $config)
    {
        return new $provider(
            $this->app['request'], $config['secrete_key'],
            $config['public_key'], $config['callback_url']
        );
    }


    /**
     * Get the default driver name.
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        throw new InvalidArgumentException('No OnlinePayment driver was specified.');
    }
}