<?php

namespace App\Http\Controllers\Auth;

use App;
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use App\Services\PasswordServices;
use Validator;

class PasswordController extends ApiController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests.
    |
    |
    */

    protected $passwordServices;

    /**
     * PasswordController constructor.
     * @param PasswordServices $passwordServices
     */
    public function __construct(PasswordServices $passwordServices)
    {
        $this->passwordServices = $passwordServices;

        //parent::__construct();
    }


    /**
     * Authenticated user making call to change password.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8'
        ]);

        //if validator fails return json error responce
        if ($validator->fails()) {
            return $this->respondWithError(404, 'validation_error', $validator->errors());
        }

        $current_password = $request -> input('current_password');
        $new_password = $request->input('new_password');

        dd($this->user->id);

        $user = App\User::where('id',$this->user->id)
            ->wherePassword(bcrypt($current_password))
            ->first();

        if($user){
            $user -> password = bcrypt($new_password);
            $user -> save();

            return $this->respondWithoutError([
                'user' => $user
            ]);
        }

        return $this->respondWithError('unauthorised','unAuthorised_access','The password your provided is wrong');

    }

    public function sendResetPasswordEmail(Request $request){
        $user = App\User::whereEmail($request->input('email'))->first();

        if($user){
            // send email to the user for password reset
            $this->passwordServices->sendPasswordResetMail($user);

            return $this->respondWithoutError([
                'status' => true,
                'message' => 'An email has been sent to your mailbox, follow the instructions to reset your password.'
            ]);
        }

        return $this->respondWithError(
            'user_not_found','user_not_found','The email address does not exist'
        );
    }

    /**
     * non-authenticated user making call to change password.
     *
     * Forgot Password
     *
     * @param Request $request
     */
    public function resetPassword(Request $request){

    }
}
