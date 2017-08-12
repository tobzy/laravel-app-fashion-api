<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use Validator;

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

    public function newAddress(Request $request){
        $address = App\Address::create([
            'user_id' => $this ->user->id,
            'street_add' => $request->input('street_add'),
            'city' => $request -> input('city'),
            'state' => $request -> input('state'),
            'country' => $request -> input('country'),
            'phone_no' => $request -> input('phone_no'),
            'type' => $request -> input('type'),
            'full_name' => $request ->input('full_name'),
        ]);

        return $this->respondWithoutError([
            'address' => $address
        ]);
    }

    public function getAddresses(Request $request){
        $addresses = App\Address::where('user_id',$this->user->id)
        ->limit($request->input('limit'))
        ->get();

        return $this->respondWithoutError([
            'addresses' => $addresses
        ]);
    }

    public function getSingleAddress($id){
        $address = App\Address::whereId($id)->first();
        return $this -> respondWithoutError([
            'address' => $address,
        ]);
    }
    public function updateAddress($id,Request $request) {

        //validate the post request
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'street_add' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'phone_no' => 'required'
        ]);

        //if validator fails return json error responce
        if ($validator->fails()) {
            return $this->respondWithError(404, 'validation_error', $validator->errors());
        }
        $address = App\Address::whereUserId($this->user->id)->whereId($id)->first();

        if (!$address) {
            return $this->respondWithoutError([
                'updated' => false,
                'message' => 'The email address does not exist',
            ]);
        }
        $address->type = $request->input('type');
        $address->street_add = $request->input('street_add');
        $address->city = $request->input('city');
        $address->state = $request->input('state');
        $address->country = $request->input('country');
        $address->phone_no = $request->input('phone_no');
        $address->save();

        return $this->respondWithoutError([
            'updated' => true,
            'message' => 'address updated successfully',
            'address' => $address]);
    }

    public function getSingleOrder($id){
        $order = App\Order::with('address')->whereUuid($id)->whereUserId($this->user->id)->first();

        $order_content = App\OrderContent::with('product','material')->whereOrderId($order->id)->get();

        return $this->respondWithoutError([
            'order' =>[
                'id'=>$order->id,
                'uuid'=>$order->uuid,
                'status'=>$order->status,
                'total_paid' => $order->total_paid,
                'payment_method'=>$order->payment_method,
                'date' => $order->created_at,
                'delivery_add' => $order->address,
                'order_content'=>$order_content,
            ]
        ]);
    }
    public function changePassword(Request $request)
    {
        $uuid = $request ->input('uuid');
        $email = $request ->input('email');
        $current_password = $request -> input('current_password');
        $new_password = $request->input('new_password');

        $user = App\User::whereEmail($email)
            ->whereUuid($this->user->id)
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
}
