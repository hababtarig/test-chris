<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    
    {
         \Log::info('✅ AdminMiddleware is being triggered');
        if (Auth::check() && Auth::user()->is_admin) {
            return $next($request);
        }
if (!auth()->user() || !auth()->user()->is_admin)
    {abort(403, 'Unauthorized');}
        
    }
}
