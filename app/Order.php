<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'uuid',
        'user_id',
        'status',
        'payment_method',
        'delivery_add_id',
        'left_over_choice',
        'total_paid',
        'comment'
    ];
    
    public function user(){
        return $this->belongsTo('App\User');
    }
    
    public function content(){
        return $this->hasMany('App\OrderContent');
    }

    public function design()
    {
        return $this->belongsTo('App\Design');
    }

    public function address(){
        return $this->belongsTo('App\Address','delivery_add_id');
    }
}
