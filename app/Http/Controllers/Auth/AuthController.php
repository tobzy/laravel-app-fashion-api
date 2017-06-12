<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Services\ActivationServices;
use App;
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
            'password' => 'required',
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
            'message' => 'We sent you a confirmation email. Check your email to activate your account'
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
        $credentials = $request->only('email', 'password');

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

}
