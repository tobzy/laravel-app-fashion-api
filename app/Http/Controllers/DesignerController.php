<?php

namespace App\Http\Controllers;

use App\Designer;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Mail;
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
        $confirmation_code = str_random(30);

        $designer = new Designer();
        $designer->username = $request->input('username');
        $designer->email = $request->input('email');
        $designer->password = bcrypt($request->input('password'));
        $time = new Carbon();
        $time = $time->timestamp;
        $designer->uuid = Uuid::uuid1() . '_' . $time;
        $designer->full_name = $request->input('full_name');
        $designer->confirmation_code = $confirmation_code;

        $email = $designer->email;
        $designa = $designer->username;
        if ($designer->save()) {
            $designer->signin = [
                'href' => 'v1/designer/signin',
                'method' => 'POST',
                'params' => 'email, password'
            ];

            Mail::send('emails.designer_confirmation',['confirmation_code'=>$confirmation_code],function ($message) use ($email, $designa){
                $message->to('admin@nattiv.com', 'Admin');
                $message->from($email, $designa);
                $message->subject('Thanks..');
            });
            $response = [
                'msg' => 'Check your Email for Confirmation Link',
                'designer' => $designer->full_name,
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

        if($designer->confirmed != 1){
            return $this->respondWithError(404, 'signin_error', 'Please check your email to verify your account.');
        }
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

    public function authDesigner(Request $request)
    {


            $designer = Designer::where('id',$request->designer_id)->first();

            if(!empty($designer)){
                return $this->respondWithoutError($this->transformDesignerToJson($designer));
            }

    }

    public function confirm($confirmation_code){
        if( ! $confirmation_code)
        {
            return $this->respondWithError(404, 'request_error', 'Invalid confirmation code');
        }

        $designer = Designer::whereConfirmationCode($confirmation_code)->first();

        if ( ! $designer)
        {
            return $this->respondWithError(404, 'request_error', 'Invalid credentials');
        }

        $designer->confirmed = 1;
        $designer->confirmation_code = null;
        $designer->save();

        $response = [
            'msg' => 'You have successfully verified your account.',
            'designer' => $designer->full_name,
        ];

        return $this->respondWithoutError($response);
    }
    private function transformDesignerToJson($designer){
        return ['designer' =>[
            'uuid' => (string)$designer->uuid,
            'full_name' => (string)$designer -> full_name,
            'username' => (string)$designer -> username,
            'email' => (string)$designer -> email
        ]];
    }
}
