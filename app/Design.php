<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Design extends Model
{
    protected $fillable = ['designer_id','title','description','location'];
    //
    public function designer(){
        return $this->belongsTo('App\Designer');
    }

    public function orders(){
        return $this->hasMany('App\Order');
    }
    public function additional_designs(){
        return $this->hasMany('App\AdditionalDesigns');
    }
}
