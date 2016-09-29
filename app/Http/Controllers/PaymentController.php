<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Payment;
use App\Paystack;
use App\Order;
use App\OrderContent;
use Ramsey\Uuid\Uuid;
class PaymentController extends ApiController
{
    /**
     * @var string
     */
    protected $_DRIVER_PAYSTACK = 'paystark';

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAuthUrl(Request $request){
        $amount = $request -> input('amount');
        //first create a customer if its the first time charge
        Payment::driver($this->_DRIVER_PAYSTACK)->createCustomer($this->user->email,$this->user->first_name,$this->user->last_name);

        $callback_url = $request -> input('callback_url');
        $transaction = Payment::driver($this->_DRIVER_PAYSTACK)->initialiseTransaction(null,$amount,$this->user->email,$callback_url);

        if($transaction->status)
            return $this->respondWithoutError([
                'authorization_url' => $transaction ->data-> authorization_url,
                'access_code'=> $transaction -> data -> access_code
            ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function chargeCustomer(Request $request){
        $amount = $request -> input('amount');
        $charge_auth = Payment::driver($this->_DRIVER_PAYSTACK)->chargeAuthorisation($request->input('auth_code'),$amount,$this->user->email);

        if($charge_auth -> status){

            if($charge_auth ->data->status){
                // input the items into the cart
                $items = $request->input('items');
                $this->createOrder(json_decode($items));

                return $this->respondWithoutError([
                    'verified'=>true,
                    'items' => $items,
                    'message'=>'Transaction successful'
                ]);
            }

            return $this->respondWithoutError([
                'verified' => false,
                'message' => $charge_auth -> message
            ]);
        }
        return $this->respondWithoutError([
            'verified' => false,
            'message' => 'Sorry an error occured and your transaction couldn\'t be completed'
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyTransaction(Request $request){
        $reference = $request -> input('reference');
        $items = json_decode($request -> input('items'));

        $verification = Payment::driver($this->_DRIVER_PAYSTACK)->verifyTransaction($reference);

        if($verification -> status){
            if ($verification->data-> status == "success"){
                //create the orders
                $this->createOrder($items);

                // check if the user has an authorisation with this card
                $auth = Paystack::whereUserId($this->user->id)
                    -> where('auth_code',$verification->data->authorization->authorization_code)
                    ->first();

                if(!$auth)
                   Paystack::create([
                      'user_id' => $this->user->id,
                      'customer_id' => $verification -> data-> customer -> id,
                       'customer_code' => $verification -> data-> customer -> customer_code,
                       'auth_code' => $verification -> data-> authorization -> authorization_code,
                       'card_type' => $verification -> data-> authorization -> card_type,
                       'last4' => $verification -> data-> authorization -> last4,
                       'exp_month' => $verification -> data-> authorization -> exp_month,
                       'exp_year' => $verification -> data-> authorization -> exp_year,
                       'channel' => $verification -> data-> authorization -> channel,
                       'reusable' => $verification -> data-> authorization -> reusable,

                   ]);

                return $this->respondWithoutError([
                    'verified' => true,
                    'amount' => $verification ->data-> amount,
                    'message' => 'Transaction Successfull',
                ]);
            }
            return $this->respondWithoutError([
                'verified' => false,
                'amount' => $verification ->data-> amount,
                'message' => 'Transaction Un-Successful',
            ]);
        }
    }

    /**
     * @param $items
     */
    private function createOrder($items){
        //create order and add the customers items to the items table
        // TODO use better uuid naming
        $order = Order::create([
            'uuid' => Uuid::uuid4(),
            'user_id' => $this->user->id,
            'status' => 'processing',
            'payment_method' => 'Credit Card',

        ]);

        //put the item objects in to an array
        foreach ($items as $item){
            $content[] = new OrderContent([
                'order_id' => $order->id,
                'product_id' => $item -> id,
                'quantity' => $item -> qty,
                'product_price' => $item -> price,
                'material_id' => $item -> options -> material -> material_id,
                'material_price' => $item -> options -> material -> material_price
            ]);
        }

        // save the items to the order
        $order -> content()->saveMany($content);
        return;
    }
}
