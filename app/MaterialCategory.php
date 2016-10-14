<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MaterialCategory extends Model
{
    protected $fillable = ['name'];
    public $table = 'material_categories';
    
    public function material(){
        $this ->hasMany('App\Material','material_category_id');
    }
}
