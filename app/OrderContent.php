<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderContent extends Model
{
    protected $fillable = ['order_id','product_id','quantity','price_total','material_id','product_price','material_price'];
    
    public function order(){
        return $this->belongsTo('App\Order');
    }
    
    public function product(){
        return $this->belongsTo('App\Product');
    }
    
    public function material(){
        return $this->belongsTo('App\Material');
    }
}
