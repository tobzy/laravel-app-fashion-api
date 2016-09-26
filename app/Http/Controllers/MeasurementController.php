<?php

namespace App\Http\Controllers;

use App\Session;
use App\FitterUser;
use App\Measurement;
use Illuminate\Http\Request;
use Payment;


class MeasurementController extends ApiController
{

    public function setMeasurements($option,Request $request){

        switch ($option){
            case 'tape':
                $transaction = Payment::driver('paystark')->initialiseTransaction(null,1000300000,'odaiboamadossi@gmail.com');

                if(is_string($transaction))
                    $transaction = json_decode($transaction);

                if(!$transaction->status){
                    return $this->respondWithError($transaction -> error ->code, $transaction->error->title, $transaction->error->message);
                }
                   return response()->json($transaction);

            case 'self-measurement':
                break;

            case 'connect_fitter':
                $personal_fitter = FitterUser::whereUserId($this->user->id)->get(['fitter_id']);

                Session::create([
                    'user_id' => $this->user->id,
                    /*if the personal fitter hasn't been set yet
                    insert just the users id and leave the fitter column
                    blank*/
                    'fitter_id' => $personal_fitter==null ?  $personal_fitter : null,
                ]);

                return $this->respondWithoutError([
                   'message' => 'Your session has been booked and your personal fitter will contact you soon'
                ]);

            default :
                return $this->respondWithError('404','option_not_available','The provided option is not supported1');
        }
    }

    public function confirmMeasurement(){

        //check if the user has a session that is pending
        $pending_session = Session::whereUserId($this->user->id)->whereStatus('pending')->first();

        if($pending_session)
            return $this->respondWithoutError([
                'hasMeasurement' => true,
            ]);

        //check if the users measurement exists
        $measurements = Measurement::whereUserId($this->user->id)->first();

        if($measurements)
            return $this->respondWithoutError([
                'hasMeasurement' => true,
            ]);

        return $this->respondWithoutError([
            'hasMeasurement' => false,
        ]);
    }
}
