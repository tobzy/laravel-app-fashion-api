<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = ['category','grade','image','color','available_qty','price'];
    
    public function category(){
        return $this ->belongsTo('App\MaterialCategory');
    }
    
    public function order_content(){
        return $this->hasMany('App\OrderContent');
    }
}
