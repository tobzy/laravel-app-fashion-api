<?php

namespace App\Http\Controllers;

use App\Designer;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Ramsey\Uuid\Uuid;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;
use Validator;

class DesignerController extends ApiController
{
    public function store(Request $request)
    {

        //validate the post request
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:designers',
            'email' => 'required|unique:designers',
            'password' => 'required|min:8',
            'full_name' => 'required'
        ]);

        //if validator fails return json error responce
        if ($validator->fails()) {
            $error = [
                'hasError' => true,
                'message' => $validator->errors(),
            ];
            return response()->json(['errors' => $error]);
        }
        //

        $designer = new Designer();
        $designer->username = $request->input('username');
        $designer->email = $request->input('email');
        $designer->password = bcrypt($request->input('password'));
        $time = new Carbon();
        $time = $time->timestamp;
        $designer->uuid = Uuid::uuid1() . '_' . $time;
        $designer->full_name = $request->input('full_name');

        if ($designer->save()) {
            $designer->signin = [
                'href' => 'v1/designer/signin',
                'method' => 'POST',
                'params' => 'email, password'
            ];
            $response = [
                'msg' => 'Designer created',
                'designer' => $designer,
            ];

            return $this->respondWithoutError($response);
        }
        return $this->respondWithError(404, 'request_error', 'Error, you couldn\'t register. Please try again');
    }

    public function signin(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        //if validator fails return json error responce
        if ($validator->fails()) {
            $error = [
                'hasError' => true,
                'message' => $validator->errors(),
            ];
            return response()->json(['errors' => $error]);
        }
        //

        // grab credentials from the request
        $email = $request['email'];
        $password = ($request['password']);

        $designer = Designer::where('email', $email)->first();
        //grab password from database
        if (!empty($designer)) {
            $db_password = $designer->password;
            if (password_verify($password, $db_password)) {
                // all good so return the token;
                $token = JWTAuth::fromUser($designer);
                $designer->token = $token;
//                $designer->token = "12345";
                $designer->save();
                return $this->respondWithoutError(['token' => $designer->token]);
            }
        }

        return $this->respondWithError(404, 'request_error', 'Invalid credentials');

    }
}
