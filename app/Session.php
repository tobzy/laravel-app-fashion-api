<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    public $fillable = ['user_id','fitter_id'];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function fitter(){
        return $this->belongsTo('App\Fitter');
    }
}
