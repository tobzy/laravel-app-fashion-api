<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class ImageController extends Controller
{
    public function _construct()
    {
//        $this->middleware('img.src');
    }

    public function getDesign(Request $request,$image)
    {
        return response()->download(storage_path('app/public/uploads/'.$image),null,['Set-Cookie'=>"laravel_session=$request->input('laravel_session')
"],null);
    }
}
