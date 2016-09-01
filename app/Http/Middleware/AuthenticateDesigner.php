<?php

namespace App\Http\Middleware;
use App\Designer;
use JWTAuth;
use Closure;

class AuthenticateDesigner
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
        $token = JWTAuth::getToken();

        if (!empty($token)) {
            $la_token = $token->get();
            $designer = Designer::where('token',$la_token)->first();
            if(!empty($designer)){
                $request->designer_id = $designer->id;
                return $next($request);
            }
            return response()->json([
                'designer' => "No Designer with the token",
                'token' => $la_token
            ]);
        }
        return response()->json([
            'error' => 'No token provided'
        ]);

    }
}
