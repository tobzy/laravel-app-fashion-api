<?php

namespace App\Http\Controllers;

use App\Designer;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Config;
use Ramsey\Uuid\Uuid;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;

class DesignerController extends Controller
{
    public function store(Request $request)
    {

        $this->validate($request, [
            'username' => 'required|unique:designers',
            'email' => 'required|unique:designers',
            'password' => 'required|min:8'
        ]);

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
                'designer' => $designer
            ];

            return response()->json($response, 201);
        }
        $response = [
            'message' => 'Error, you couldn\'t register. Please try again'
        ];

        return response()->json($response, 404);
    }

    public function signin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // grab credentials from the request
        $credentials = $request->only('email', 'password');

        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['message' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['message' => 'could_not_create_token'], 500);
        }

        // all good so return the token
        return response()->json(['token'=>$token]);
    }
}
