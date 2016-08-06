<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = ['product_id','image','alt_text'];
    
    public function product(){
        return $this->belongsTo('App\Product');
    }
}
