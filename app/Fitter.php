<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fitter extends Model
{
    public function session(){
        return $this->hasMany('App\Session');
    }
}
