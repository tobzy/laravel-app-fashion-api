<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'addresses';

    protected $fillable = ['user_id','street_add','city','state','country','phone_no','type'];
    
    public function user(){
        return $this ->belongsTo('App\User');
    }
}
