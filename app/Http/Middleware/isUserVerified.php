<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class isUserVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth('api')->check() && auth('api')->user()->email_verified_at === null) {
            return response()->json([
                'success' => false,
                'message' => 'You need to verify your account before proceeding'
            ], 401);
        }
        return $next($request);
    }
}
