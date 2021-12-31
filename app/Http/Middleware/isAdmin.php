<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;
class isAdmin
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

        if (auth('api')->check() && auth('api')->user()->role !== 'Admin') {
            return response()->json([
                'success' => false,
                'message' => 'You need to be an Admin to perform this action'
            ], 401);
        }
        return $next($request);

    }
}
