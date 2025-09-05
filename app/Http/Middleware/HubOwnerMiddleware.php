<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HubOwnerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        if (Auth::user()->role !== 'hub_owner') {
            return redirect('/dashboard')->with('error', 'Access denied. Hub owner privileges required.');
        }

        return $next($request);
    }
}
