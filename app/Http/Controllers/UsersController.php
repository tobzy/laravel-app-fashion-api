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
    
    private function transformUserToJson($user){
        $address = $user -> address;
        return ['user' =>[
            'uuid' => (string)$user->uuid,
            'first_name' => (string)$user -> first_name,
            'last_name' => (string)$user -> last_name,
            'email' => (string)$user -> email,
            'designer' => (boolean)$user -> designer,
            'confirmed' => (boolean)$user -> confirmation,
            'billing_address' => $address -> where('type','billing'),
            'delivery_address' => $address -> where('type','delivery'),
        ]];
    }
}
