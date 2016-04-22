<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;

use Response;
use JWTAuth;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function index(){
        
        $user = JWTAuth::parseToken()->authenticate();
        $name = $user -> first_name;
        return Response::json(compact('name'));
    }
}
