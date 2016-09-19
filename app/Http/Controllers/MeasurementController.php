<?php

namespace App\Http\Controllers;

use App\Session;
use Illuminate\Http\Request;
use Payment;


class MeasurementController extends ApiController
{

    public function setMeasurements($option,Request $request){

        switch ($option){
            case 'tape':
                   return response()->json(Payment::driver('paystark')->initialiseTransaction(null,1000300000,'odaiboamadosi@gmail.com'));

            case 'self-measurement':
                break;
            case 'session':
                Session::create([
                    'user_id' => $this->user->id,
                ]);
                break;
            default :
                return $this->respondWithError('404','option_not_available','The provided option is not supported1');
        }
    }

    public function payment(){

    }
}
