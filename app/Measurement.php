<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Measurement extends Model
{
    protected $fillable = ['arm','waist','burst','leg','neck'];
    
    public function user(){
        return $this->belongsTo('App\User');
    }
}
