<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdditionalDesigns extends Model
{
    //
    public function design(){
        return $this->belongsTo('App\Design');
    }
}
