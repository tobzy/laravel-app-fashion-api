<?php

namespace App\Http\Middleware;

use App\AdditionalDesigns;
use App\Design;
use Closure;

class DesignViewAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $id = $request->designer_id;
        $a = $request->path();
        $arr = preg_split('/[\/\?]/',$a);
        $location = $arr[2];
        $design_place1 = Design::where('location',$location)->first();
        $design_place2 =AdditionalDesigns::where('location',$location)->first();

        if($design_place2){
            $dezain = $design_place2->design;
            if($dezain->designer_id == $id){
                return $next($request);
            }
        }

        if($design_place1->designer_id == $id){
            return $next($request);
        }


//        if (!empty($token)) {
//            $la_token = $token->get();
//            $designer = Designer::where('token',$la_token)->first();
//            if(!empty($designer)){
//                $request->designer_id = $designer->id;
//                return $next($request);
//            }
//            return response()->json([
//                'designer' => "No Designer with the token",
//            ]);
//        }
        return response()->json([
            'error' => 'No token provided'
        ]);
    }
}
