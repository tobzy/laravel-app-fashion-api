<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FitterUser extends Model
{
    protected $table = 'fitter_users';

    public $fillable = ['fitter_id','user_id'];
}
