<?php

namespace Nattivv\OnlinePayments\Providers;


class PaystarkProvider extends AbstractProviders implements ProviderInterface
{

    /**
     * @var string
     */
    protected $gatewayBaseUrl = 'https://api.paystack.co';

    /**
     *
     */
    protected $_RISK_ACTION_ALLOW = 'allow';

    /**
     *
     */
    protected $_RISK_ACTION_DENY = 'deny';

    /**
     * create a customer in the paystark domain
     * @param $email
     * @param null $first_name
     * @param null $last_name
     * @param null $phone
     * @param array|null $metadata
     * @return json
     */
    public function createCustomer($email, $first_name = null, $last_name = null, $phone = null, array $metadata = null){

        return $this->getHttpResponse(ProviderInterface::POST,'customer',[
            'email'=>$email,
            'first_name'=>$first_name,
            'last_name'=>$last_name,
            'phone' => $phone,
            'metadata'=>$metadata,
        ]);

    }

    /**
     * Get a set of all the customers from the paystark domain
     * @param int $perPage
     * @param int $page
     * @return json
     */
    public function listCustomers($perPage=50, $page=1){

        return $this->getHttpResponse(ProviderInterface::GET,'customer',[
            'perPage' => $perPage,
            'page'=>$page,
        ]);
    }

    /**
     * Gets a customers details based on the customer code or id
     * @param $id
     * @return json
     */
    public function fetchCustomer($id){
        return $this->getHttpResponse(ProviderInterface::GET,'customer/'.$id);
    }

    /**
     * @param $id
     * @param array $data
     * @return mixed
     */
    public function updateCustomer($id, array $data){
        return $this->getHttpResponse(ProviderInterface::PUT,'customer/'.$id, $data);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function whiteListCostumer($id){
        return $this->getHttpResponse(ProviderInterface::POST,'customer/set_risk_action',[
            'customer'=>$id,
            'risk_action' => $this->_RISK_ACTION_ALLOW
        ]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function blackListCostumer($id){
        return $this->getHttpResponse(ProviderInterface::POST,'customer/set_risk_action',[
            'customer'=>$id,
            'risk_action' => $this->_RISK_ACTION_DENY
        ]);
    }


    /*Transaction methods*/
    /**
     * Initialises a transaction with the amount and the customers id|code
     * @param $reference
     * @param $amount
     * @param $customerEmail
     * @param null $callback_url
     * @return json
     */
    public function initialiseTransaction($reference = null, $amount, $customerEmail, $callback_url = null ){
        return $this->getHttpResponse(ProviderInterface::POST,'transaction/initialize',[
            'reference' => $reference,
            'amount' => $amount,
            'email' => $customerEmail,
            'callback_url' => $callback_url,
        ]);
    }

    /**
     * Verifys the transaction and charges the customer
     * @param $reference
     * @return json
     * @internal param $reference
     */
    public function verifyTransaction($reference){
        return $this->getHttpResponse(ProviderInterface::GET,'transaction/verify/'.$reference);
    }

    /**
     * Gets a list of the transactions in a paginated format
     * @param int $perPage
     * @param int $page
     * @return mixed
     */
    public function listTransaction($perPage = 50, $page=1){
        return $this->getHttpResponse(ProviderInterface::GET,'transaction',[
            'perPage' => $perPage,
            'page' => $page
        ]);
    }

    /**
     * Gets a single transaction using the transactions id
     * @param $id
     * @return json
     */
    public function fetchTransaction($id){
        return $this->getHttpResponse(ProviderInterface::GET,'transaction/'.$id);
    }

    /**
     * Gives an instant charge to the customer
     * using an authorization code
     * @param $authCode
     * @param $amount
     * @param $email
     * @param null $reference
     * @return json
     */
    public function chargeAuthorisation($authCode,$amount,$email,$reference = null){
        return $this->getHttpResponse(ProviderInterface::POST,'transaction/charge_authorization',[
            'authorization_code' => $authCode,
            'amount' => $amount,
            'email' => $email,
            'reference' => $reference,
        ]);
    }

    /**
     * Gives an instant charge to the customer
     * using a verified token
     * @param $token
     * @param $amount
     * @param $email
     * @param null $reference
     * @return json
     */
    public function chargeToken($token,$amount,$email,$reference = null){
        return $this->getHttpResponse(ProviderInterface::POST,'transaction/charge_token',[
            'token' => $token,
            'amount' => $amount,
            'email' => $email,
            'reference' => $reference,
        ]);
    }

    /**
     * exports a the transaction between dates
     * @param null $from Lower bound of date range. Leave undefined to export transactions from day one.
     * @param null $to Upper bound of date range. Leave undefined to export transactions till date
     * @return json
     */
    public function exportTransaction($from = null, $to = null){
        return $this->getHttpResponse(ProviderInterface::GET,'transaction/export',[
            'from' => $from,
            'to' => to
        ]);
    }

    /**
     * Gets the activity time line for a transaction
     * @param $id
     * @return mixed
     */
    public function viewTransactionTimeLine($id){
        return $this->getHttpResponse(ProviderInterface::GET,'transaction/timeline/'.$id);
    }


    /*Plans Methods*/
    /**
     *
     */
    public function createPlan(){
        return;
    }

    /**
     *
     */
    public function listPlans(){
        return;
    }

    /**
     *
     */
    public function fetchPlan(){
        return;
    }

    /**
     *
     */
    public function updatePlan(){
        return;
    }

    /*Subscriptions Method*/
    /**
     *
     */
    public function createSubscription(){
        return;
    }

    /**
     *
     */
    public function disableSubscription(){
        return;
    }

    /**
     *
     */
    public function enableSubscription(){
        return;
    }

    /**
     *
     */
    public function fetchSubscription(){
        return;
    }

    /**
     * @return string
     */
    protected function getGatewayBaseUrl()
    {
        return $this->gatewayBaseUrl;
    }


    /**
     * @param $response
     * @return mixed|string
     */
    public function processHttpResponse($response)
    {
        switch($response->getStatusCode()){

            case 201:
            case 200:
                return json_decode((string)$response->getBody());

            case 400:
                return $this->getError(
                    400,'validation_error','A validation or client side error occurred and the request was not fulfilled.'
                );

            case 401:
                return $this->getError(
                    401,'request_not_authorised','The request was not authorized. This can be triggered by passing an invalid secret key in the authorization header or the lack of one'
                );

            case 500:
            case 501:
            case 502:
            case 503:
            case 504:
                return $this->getError(
                    500,'internal_sever_error','Request could not be fulfilled due to an error on Paystack\'s end. This shouldn\'t happen so please report as soon as you encounter any instance of this.'
                );


        }
    }
}