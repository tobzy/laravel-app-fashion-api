<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['uuid','user_id','status','payment_method'];
    
    public function user(){
        return $this->belongsTo('App\User');
    }
    
    public function content(){
        return $this->hasMany('App\OrderContent');
    }
}
