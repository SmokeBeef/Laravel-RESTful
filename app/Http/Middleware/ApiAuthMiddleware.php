<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header("Authorization");
        $autenticate = true;

        if (!$token) {
            $autenticate = false;
        }

        $user = User::where("token", $token)->first();
        
        if (!$user) {
            $autenticate = false;
        }

        

        
        if ($autenticate) {
            Auth::login($user);
            return $next($request);
        } else {

            return response()->json([
                "errors" => [
                    "message" => [
                        "unathorized"
                    ]
                ]
            ])->setStatusCode(401);
        }
    }
}
