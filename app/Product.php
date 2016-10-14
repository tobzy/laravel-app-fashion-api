<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'product_name','uuid','price',
        'description','type','no_of_yard',
        'default_material','image','category',
    ];
    
    public function order_content(){
        return $this->hasMany('App\OrderContent');
    }
    
    public function image(){
        return $this->hasMany('App\ProductImage');
    }

    public function default_material(){
        return $this->belongsTo('App\Material','default_material');
    }

    public function default_material_with_cat($id){
        $material = Material::with('category')->whereId($id)->first();
        return $material;
    }
}
