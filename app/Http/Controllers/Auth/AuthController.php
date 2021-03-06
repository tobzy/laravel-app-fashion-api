<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Services\ActivationServices;
use App;
use App\User;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Validator;

class AuthController extends App\Http\Controllers\ApiController
{

    protected $activationService;

    public function __construct(ActivationServices $activationService)
    {
        $this->activationService = $activationService;
    }

    public function create(Request $request)
    {

        //validate the post request
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|alpha',
            'last_name' => 'required|alpha',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        //if validator fails return json error responce
        if ($validator->fails()) {
            return $this->respondWithError(404, 'validation_error', $validator->errors());
        }

        // create the user and retrieve an instance.
        $user = App\User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);

        //develop a uuid from the id of the user.
        $uuid5 = Uuid::uuid5(Uuid::NAMESPACE_DNS, $user->id . '.com');
        $user->uuid = $uuid5;
        $user->save();

        //send an activation email to the users email
        $this->activationService->sendActivationMail($user);

        return $this->respondWithoutError([
            'message' => 'A confirmation email has been sent to you. Please check your email.',
        ]);
    }

    /**
     * API Login, on success return JWT Auth token
     *
     * @param value $token
     * @return \Illuminate\Http\JsonResponse
     * @internal param value $token
     */
    public function activate($token)
    {
        if ($token === null) {
            return;
        }
        $user = $this->activationService->activateUser($token);
        if($user == null){

            //todo change to the nattivv url
            return redirect('http://nattivv.com/?confirmed=0');
            //return;
        }

        //todo change to the nattivv url
        return redirect('http://nattivv.com/?confirmed=1');
    }

    /**
     * API Login, on success return JWT Auth token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticate(Request $request)
    {

        // grab credentials from the request
        $user = User::where('email', $request->input('email'))->first();
        if($user){
            if($user->confirmation != 1){
                return $this->respondWithError('ERR-AUTH-002','not_confirmed_error',"Please confirm email before login. If you can't find it in inbox, please check the spam folder.");
            }
            $credentials = $request->only('email', 'password');
        }else{
            return $this->respondWithError('ERR-AUTH-001','Invalid Credentials','Username Or Password Is Incorrect');
        }



        try {
            // attempt to verify the credentials and create a token for the user
//            $jw = new JWTAuth();
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->respondWithError('ERR-AUTH-001','Invalid Credentials','Username Or Password Is Incorrect');
            }

        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return $this->respondWithError('ERR-AUTH-002','token_error',$e->getMessage());

        }

        // all good so return the token
        return $this->respondWithoutError([
            'token' => $token
        ]);
    }

    /**
     * Log out
     * Invalidate the token, so user cannot use it anymore
     * They have to relogin to get a new token
     *
     * @param Request $request
     */
    public function deauthenticate(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        JWTAuth::invalidate($request->input('token'));
    }

    public function resendConfirmationLink(Request $request)
    {
        //validate the post request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        //if validator fails return json error responce
        if ($validator->fails()) {
            return $this->respondWithError(404, 'validation_error', $validator->errors());
        }

        $user = User::where('email', $request->input('email'))->first();

        if($user){
            $this->activationService->sendActivationMail($user);
        }

        return $this->respondWithoutError([
            'message' => 'A confirmation email has been sent to you. Please check your email.',
            'user' => $user
        ]);

    }
}
