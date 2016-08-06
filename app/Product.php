<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['product_name','uuid','price','description'];
    
    public function order_content(){
        return $this->hasMany('App\OrderContent');
    }
    
    public function image(){
        return $this->hasMany('App\ProductImage');
    }
}
