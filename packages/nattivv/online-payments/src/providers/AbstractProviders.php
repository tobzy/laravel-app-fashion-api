<?php
namespace Nattivv\OnlinePayments\Providers;

use GuzzleHttp\Client as Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\TooManyRedirectsException;
use GuzzleHttp\Exception\TransferException;
use Illuminate\Http\Request;
use Nattivv\OnlinePayments\Contracts\Provider as ProviderContract;

abstract class AbstractProviders implements ProviderContract
{

    /**
     * The HTTP request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * The HTTP Client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * The client ID.
     *
     * @var string
     */
    protected $clientId;

    /**
     * The client secret.
     *
     * @var string
     */
    protected $clientSecret;

    /**
     * The redirect URL.
     *
     * @var string
     */
    protected $redirectUrl;

    /**
     * The custom parameters to be sent with the request.
     *
     * @var array
     */
    protected $parameters = [];

    /**
     * Create a new provider instance.
     *
     * @param  Request $request
     * @param  string $clientId
     * @param  string $clientSecret
     * @param  string $redirectUrl
     */
    public function __construct(Request $request, $clientSecret,  $clientId, $redirectUrl)
    {
        $this->request = $request;
        $this->clientId = $clientId;
        $this->redirectUrl = $redirectUrl;
        $this->clientSecret = $clientSecret;

        //initiate the httpClient
        $this ->httpClient = $this->setHttpClient();
    }

    /**
     * initialises an http client for every request to be made
     * based on the gateway base url
     * @return Client
     */
    private function setHttpClient(){
        return new Client([
            // Base URI is used with relative requests
            'base_uri' => $this->getGatewayBaseUrl(),
            'verify'=> false,
            // You can set any number of default request options.
            'timeout'  => 60.0,
        ]);
    }

    /**
     * Get the gateway URL for the provider.
     * @return string
     * @internal param string $state
     */
    abstract protected function getGatewayBaseUrl();

    /**
     * How the http client response is to be processed
     * @param $response
     * @return mixed
     */
    abstract protected function processHttpResponse($response);

    /**
     * Redirect the user to the payment-gateway page for the provider.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirect()
    {
       // return new RedirectResponse($this->getGatewayBaseUrl());
    }


    /**
     * Generates a full resource url
     * @param $resource
     * @return string
     */
    protected function generateResourceUrl($resource){
        return $url = $this->getGatewayBaseUrl().'/'.$resource;
    }


    /**
     * makes a http request using the guzzleHttp client
     * @param $method
     * @param $resource
     * @param array $data
     * @return mixed
     */
    protected function getHttpResponse($method, $resource, array $data = null){
        $params = [
            'headers'=>[
                'Authorization' => 'Bearer ' . $this->clientSecret,
            ],

            ($method==ProviderInterface::POST ? 'json' : 'query')=>$data,
        ];

        try {
            $response = $this->httpClient->request((string)$method, $resource, $params);
            return $this->processHttpResponse($response);
        }catch (ConnectException $e){
            return $this->getError($e->getCode(),'connection_error',$e->getMessage());
        }catch (TransferException $e){
            return $this->getError($e->getCode(),'transfer_error',$e->getMessage());
        }catch (RequestException $e){
            return $this->getError($e->getCode(),'request_error',$e->getMessage());
        }catch (ClientException $e){
            return $this->getError($e->getCode(),'client_error',$e->getMessage());
        }catch (BadResponseException $e){
            return $this->getError($e->getCode(),'bad_response_error',$e->getMessage());
        }catch (ServerException $e){
            return $this->getError($e->getCode(),'server_error',$e->getMessage());
        }catch (TooManyRedirectsException $e){
            return $this->getError($e->getCode(),'too_many_redirects_error',$e->getMessage());
        }


    }

    /**
     * generates a standard error format
     * @param $code
     * @param $title
     * @param $message
     * @return string
     */
    protected function getError($code, $title, $message){
        return json_encode([
            'status' => false,
            'error' => [
                'code' => $code,
                'title' => $title,
                'message' => $message,
            ]
        ],128);
    }
}