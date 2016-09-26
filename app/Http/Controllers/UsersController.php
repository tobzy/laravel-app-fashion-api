<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App;
use App\Http\Requests;

class UsersController extends ApiController
{
    
    public function authUser(){
        //check if the user has been deleted
        if(!$this->user -> trashed()){
            return $this->respondWithoutError($this->transformUserToJson($this->user));
        }
        
    }

    public function getOrders(){
        $orders = App\Order::whereUserId($this -> user -> id)->
            limit(3)
            ->get();
        return $this->respondWithoutError(['orders' => $orders]);
    }

    public function getCreditCards(){
        $cards = App\Paystack::whereUserId($this ->user -> id)->get(['card_type','last4','exp_month','exp_year']);
        return $this->respondWithoutError(['cards'=>$cards]);
    }
    
    private function transformUserToJson($user){
        $address = $user -> address;
        return ['user' =>[
            'uuid' => (string)$user->uuid,
            'first_name' => (string)$user -> first_name,
            'last_name' => (string)$user -> last_name,
            'email' => (string)$user -> email,
            'confirmed' => (boolean)$user -> confirmation,
            'billing_address' => $address -> where('type','billing'),
            'delivery_address' => $address -> where('type','delivery'),
        ]];
    }
}
