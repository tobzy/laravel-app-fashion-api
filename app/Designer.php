<?php

namespace App;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Designer extends Model implements Authenticatable
{
    use \Illuminate\Auth\Authenticatable;
    protected $hidden = [
        'password', 'remember_token',
    ];
    public function designs(){
        return $this->hasMany('App\Design');
    }
}
