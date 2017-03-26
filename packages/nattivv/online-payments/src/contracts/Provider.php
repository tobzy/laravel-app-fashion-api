<?php
namespace Nattivv\OnlinePayments\Contracts;

interface Provider{

    /**
     * Redirect the user to the payment-gateway page for the provider.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect();

}
