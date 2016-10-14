<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Paystack extends Model
{
    protected $table = 'pay_stack_authorisation';

    public $fillable = [
        'user_id',
        'customer_id',
        'customer_code',
        'auth_code',
        'card_type',
        'last4',
        'exp_month',
        'exp_year',
        'channel',
        'reusable'
    ];

    public function user(){
        return $this->belongsTo('App\User');
    }
}
