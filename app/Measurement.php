<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Measurement extends Model
{
    protected $fillable = ['user_id','arm','waist','burst','leg','neck'];
    
    public function user(){
        return $this->belongsTo('App\User');
    }
}
