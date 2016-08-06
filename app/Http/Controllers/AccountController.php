<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;

class AccountController extends ApiController {

    public function account() {
        
    }

    public function updateEmail(Request $request) {
        try {
            $this->user->email = $request->input('new_email');
            $this->user->save();

            return $this->respondWithoutError(['email' => $request->input('new_email')]);
            
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                return $this->respondWithError($e->errorInfo[1], 'duplicate_email_error', $e->errorInfo[2]);
            }
            return $this->respondWithError(404, 'request_error', 'Sorry your request could not be completed at this time, please try again.');
        }
    }

    public function updateAddress(Request $request) {
        $address = App\Address::whereUserId($this->user->id)->whereType($request->input('type'))->first();

        if (!$address) {
            $address = App\Address::create(['user_id' => $this->user->id]);
        }
        $address->street_add = $request->input('street_add');
        $address->city = $request->input('city');
        $address->state = $request->input('state');
        $address->country = $request->input('country');
        $address->save();

        return $this->respondWithoutError(['address' => $address]);
    }

    public function changePassword() {
        
    }

    public function updateCreditCards() {
        
    }

}
