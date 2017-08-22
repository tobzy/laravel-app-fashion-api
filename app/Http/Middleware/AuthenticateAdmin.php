<?php

namespace App\Http\Middleware;
use App\Designer;
//use Illuminate\Support\Facades\DB;
use JWTAuth;
use Closure;
use DB;

class AuthenticateAdmin
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
            $user = DB::table('admin')->where('token',$la_token)->first();
            if(!empty($user)){
                $request->user_id = $user->id;
                return $next($request);
            }
            return response()->json([
                'user' => "No Admin with the token",
            ]);
        }
        return response()->json([
            'error' => 'No token provided'
        ]);

    }
}
