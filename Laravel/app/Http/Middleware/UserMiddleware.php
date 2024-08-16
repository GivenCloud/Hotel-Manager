<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role == 'user') {
            if ($request->isMethod('get')) {
                return $next($request);
            } else {
                return response()->json(['error' => 'Unauthorized. Users can only perform GET requests.'], 403);
            }
        }

        return response()->json(['error' => 'Unauthorized.'], 403);
    }
}
