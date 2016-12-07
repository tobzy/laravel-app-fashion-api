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

    public function getOrders(Request $request ){
        $orders = App\Order::whereUserId($this -> user -> id)
            ->orderBy('created_at','DESC')
            ->paginate($request->input('per_page'));
        return $this->respondWithoutError([
            'orders' => $orders,
            'links' => (string)$orders->links(),
        ]);
    }

    public function getCreditCards(){
        $cards = App\Paystack::whereUserId($this ->user -> id)->get(['id','auth_code','card_type','last4','exp_month','exp_year']);
        return $this->respondWithoutError(['cards'=>$cards]);
    }

    public function deleteCreditCard($id){
        $card = App\Paystack::whereId($id)->whereUserId($this->user->id)->first();

        if(true) {
            $card->delete();
            return $this->respondWithoutError([
                'deleted' => true,
                'message' => 'Credit card deleted successfully',
            ]);
        }

        return $this->respondWithoutError([
            'deleted' => false,
            'message' => 'The credit card does not exist',
        ]);
    }
    
    private function transformUserToJson($user){

        return ['user' =>[
            'uuid' => (string)$user->uuid,
            'first_name' => (string)$user -> first_name,
            'last_name' => (string)$user -> last_name,
            'email' => (string)$user -> email,
            'confirmed' => (boolean)$user -> confirmation,
        ]];
    }
}
