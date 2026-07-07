<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        foreach ($guards as $guard) {
            if (auth()->guard($guard)->check()) {
                return redirect('/home');
            }
        }

        return $next($request);
    }
}
