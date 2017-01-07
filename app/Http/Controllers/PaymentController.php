<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Payment;
use Log;
use App\Paystack;
use App\Order;
use App\OrderContent;
use App\Product;
use App\Material;
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
        // get items from the request
        $items =json_decode($request->input('items'));
        //calculate the billing from the items purchased
        $total = $this -> calculateBilling($items);
        $total_in_kobo = $total * 100;

        //first create a customer if its the first time charge
        Payment::driver($this->_DRIVER_PAYSTACK)->createCustomer($this->user->email,$this->user->first_name,$this->user->last_name);

        //retrieve the call back url if available
        $callback_url = $request -> input('callback_url');

        //retrieve the initialised transaction
        $transaction = Payment::driver($this->_DRIVER_PAYSTACK)->initialiseTransaction(null,$total_in_kobo,$this->user->email,$callback_url);

        if($transaction->status)
            return $this->respondWithoutError([
                'authorization_url' => $transaction ->data-> authorization_url,
                'access_code'=> $transaction -> data -> access_code
            ]);

        $this->respondWithError('404','transaction_error','try again');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function chargeCustomer(Request $request){
        // get items from the request
        $items =json_decode($request->input('items'));

        //calculate the total of all the items
        $total = $this->calculateBilling($items);
        //convert the total to kobo value
        $total_in_kobo = $total * 100;

        // charge the customer with the total calculated price
        $charge_auth = Payment::driver( $this->_DRIVER_PAYSTACK)
            ->chargeAuthorisation(
                $request->input('auth_code'),
                $total_in_kobo,
                $this->user->email
            );

        if($charge_auth -> status){

            if($charge_auth ->data->status){
                // input the items into the cart
                $this->createOrder($items,$total,$request->input('left_over_material'),$request->input('delivery_add'));

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
            'message' => 'Sorry an error occurred and your transaction couldn\'t be completed'
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyTransaction(Request $request){
        $reference = $request -> input('reference');
        $items = json_decode($request -> input('items'));

        //verify the transaction using the transaction reference
        $verification = Payment::driver($this->_DRIVER_PAYSTACK)->verifyTransaction($reference);

        if($verification -> status){
            if ($verification->data-> status == "success"){
                //calculate the billing
                $total = $this -> calculateBilling($items);

                //create the orders
                $this->createOrder($items,$total,$request->input('left_over_material'),$request->input('delivery_add'));

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
                    'message' => 'Transaction Successful',
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
     * Method to calculate the totale cost of products purchased
     * @param $items
     * @return int|mixed
     */
    private function calculateBilling($items){
        //initialise a total counter
        $total = 0;

        foreach ($items as $item){
            $product =  Product::whereId($item->id)->first();
            $material = Material::whereId($item -> options -> material -> material_id)->first();

            Log::info('Product id '.$item->id);
            Log::info('Material id '.$item -> options -> material -> material_id);

            // add the products price times the quantity of the product
            $total += ($product -> price * $item->qty);
            Log::info('Product Cost'.$total);

            // add the material price per the amount of yards for the product
            $total += ($material->price * ($product->no_of_yards * $item -> qty));
            Log::info('Material Cost '.$material->price);
            Log::info('No of yards '.$product->no_of_yards);

            Log::info('Total before shipping '.$total);

        }

        //add the shipping price to the total
        $total += config('payments.shipping');

        Log::info('Total After shipping'+$total);
        return $total;
    }
    /**
     * @param $items
     * @param $total
     * @param $left_over
     * @param $delivery_add_id
     */
    private function createOrder($items,$total,$left_over,$delivery_add_id){
        //create order and add the customers items to the items table
        // TODO use better uuid naming
        $order = Order::create([
            'uuid' => Uuid::uuid4(),
            'user_id' => $this->user->id,
            'status' => 'processing',
            'payment_method' => 'Credit Card',
            'left_over_choice' => $left_over,
            'delivery_add_id' => $delivery_add_id,
            'total_paid' => $total,

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

        //todo send order details email to the user.
        return;
    }

    public function getShippingPrice(){
        return $this->respondWithoutError([
            'shipping' => config('payments.shipping'),
        ]);
    }
}
