<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $fillable = ['access_level'];
    
    public function user(){
        return $this->belongsTo('App\User');
    }
}
