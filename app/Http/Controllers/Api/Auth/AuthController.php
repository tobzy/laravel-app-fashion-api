<?php

namespace App\Http\Controllers\Api\Auth;

use App;
use Response;
use Illuminate\Http\Request;
use JWTAuth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response as IlluminateResponse;

class AuthController extends Controller {

    /**
     * API Login, on success return JWT Auth token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request) {
        $credentials = $request->only('email', 'password');

        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        // all good so return the token
        return response()->json(compact('token'));
    }

    /**
     * Log out
     * Invalidate the token, so user cannot use it anymore
     * They have to relogin to get a new token
     * 
     * @param Request $request
     */
    public function logout(Request $request) {
        $this->validate($request, [
            'token' => 'required'
        ]);

        JWTAuth::invalidate($request->input('token'));
    }

    public function register(Request $request) {

        try {
            $this->validate($request, [
                'first_name' => 'required',
                'last_name' => 'required',
                'address' => 'required',
                'telephone' => 'required',
                'city' => 'required',
                'state' => 'required',
                'country' => 'required',
                'email' => 'required|email|max:255',
                'password' => 'required',
            ]);
        } catch (HttpResponseException $e) {
            return response()->json([
                        'error' => [
                            'message' => 'Invalid auth',
                            'status_code' => IlluminateResponse::HTTP_BAD_REQUEST
                        ]], IlluminateResponse::HTTP_BAD_REQUEST, $headers = []
            );
        }

        $credentials = $request->only('first_name', 'last_name', 'address', 'telephone', 'city', 'state', 'country', 'email', 'password');

        $created = App\User::create([
                    'first_name' => $credentials['first_name'],
                    'last_name' => $credentials['last_name'],
                    'address' => $credentials['address'],
                    'telephone' => $credentials['telephone'],
                    'city' => $credentials['city'],
                    'state' => $credentials['state'],
                    'country' => $credentials['country'],
                    'email' => $credentials['email'],
                    'password' => bcrypt($credentials['password']),
        ]);

        if ($created) {
            return Response::json([
                        'error' => false,
                        'status' => 'Account Created Successfully',
                        'status_code' => 200
            ]);
        }else{
            return Response::json([
                        'error' => true,
                        'status' => 'Unable To create account',
                        'status_code' => IlluminateResponse::HTTP_EXPECTATION_FAILED
            ]);
        }
    }

}
