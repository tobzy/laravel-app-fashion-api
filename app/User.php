<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name','last_name','telephone','address','state','city','country', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at', 'created_at', 'updated_at'];
    
    
    public function activation(){
        return $this ->hasOne('App\Activation');
    }

    public function address(){
        return $this->hasMany('App\Address');
    }
    
    public function design(){
        return $this->hasMany('App\Design');
    }
    
    public function order(){
        return $this->hasMany('App\Order');
    }
    
    public function admin(){
        return $this->hasOne('App\Admin');
    }
    
    public function measurement(){
        return $this->hasOne('App\Measurement');
    }

    public function paystack_account(){
        return $this->hasMany('App\Paystack');
    }
}
