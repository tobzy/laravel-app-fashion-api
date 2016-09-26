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

    public function getAuthUrl(Request $request){
        $amount = $request -> input('amount');
        $callback_url = $request -> input('callback_url');
        $transaction = Payment::driver('paystark')->initialiseTransaction(null,$amount,$this->user->email,$callback_url);

        if($transaction->status)
            return $this->respondWithoutError([
                'authorization_url' => $transaction ->data-> authorization_url,
                'access_code'=> $transaction -> data -> access_code
            ]);
    }

    public function verifyTransaction(Request $request){
        $reference = $request -> input('reference');
        $items = json_decode($request -> input('items'));

        $verification = Payment::driver('paystark')->verifyTransaction($reference);

        if($verification -> status){
            if ($verification->data-> status == "success"){

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

                // check if the user has an authorisation with this card
                $auth = Paystack::whereUserId($this->user->id)
                    -> where('auth_code',$verification->data->authorization->authorization_code)
                    ->first();

                if($auth)
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
}
