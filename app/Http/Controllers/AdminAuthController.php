<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Services\ActivationServices;
use App;
use App\User;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Validator;
use DB;

class AuthController extends App\Http\Controllers\ApiController
{

    protected $activationService;

    public function __construct(ActivationServices $activationService)
    {
        $this->activationService = $activationService;
    }


//    public function authenticate(Request $request)
//    {
//
//        // grab credentials from the request
//        $user = User::where('email', $request->input('email'))->first();
//        if($user){
//            if($user->confirmation != 1){
//                return $this->respondWithError('ERR-AUTH-002','not_confirmed_error',"Please confirm email before login. If you can't find it in inbox, please check the spam folder.");
//            }
//            $credentials = $request->only('email', 'password');
//        }else{
//            return $this->respondWithError('ERR-AUTH-001','Invalid Credentials','Username Or Password Is Incorrect');
//        }
//
//
//
//        try {
//            // attempt to verify the credentials and create a token for the user
////            $jw = new JWTAuth();
//            if (!$token = JWTAuth::attempt($credentials)) {
//                return $this->respondWithError('ERR-AUTH-001','Invalid Credentials','Username Or Password Is Incorrect');
//            }
//
//        } catch (JWTException $e) {
//            // something went wrong whilst attempting to encode the token
//            return $this->respondWithError('ERR-AUTH-002','token_error',$e->getMessage());
//
//        }
//
//        // all good so return the token
//        return $this->respondWithoutError([
//            'token' => $token
//        ]);
//    }

    public function deauthenticate(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        JWTAuth::invalidate($request->input('token'));
    }
    public function authenticate(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required'
        ]);

        //if validator fails return json error responce
        if ($validator->fails()) {
            return $this->respondWithError(404, 'validation_error', $validator->errors()->toJson());
        }



        // grab credentials from the request
        $username = $request['username'];
        $password = ($request['password']);

        $user = DB::table('admin')->where('username', $username)->first();


        //grab password from database
        if (!empty($user)) {
            $db_password = $user->password;

            if (password_verify($password, $db_password)) {
                // all good so return the token;
                $token = JWTAuth::fromUser($user);
                $user->token = $token;
                $user->save();
                return $this->respondWithoutError(['token' => $user->token]);
            }
        }

        return $this->respondWithError(404, 'request_error', 'Invalid credentials');

    }
}
