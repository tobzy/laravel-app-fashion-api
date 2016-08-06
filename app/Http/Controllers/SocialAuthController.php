<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\SocialUser;
use JWTAuth;
use App\Http\Controllers\Controller;
use App\Services\SocialAccountService;

class SocialAuthController extends Controller
{
    
    public function authSocial(Request $request,SocialAccountService $service)
    {
         $user = $service->createOrGetUser(new SocialUser($request));
        $token = JWTAuth::fromUser($user);
        
        return $this->respondWithoutError($this->transformUserToJson($user), $token);
    }
    
    private function respondWithoutError($data,$token) {

        $response = [
            'errors' => [
                'hasError' => false
            ],
            'token'=>$token,
            'data' => $data,
        ];

        return response()->json($response);
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
