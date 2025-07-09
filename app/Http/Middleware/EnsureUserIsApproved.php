<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsApproved
{
    /*  public function handle(Request $request, Closure $next)
      {
          if (!$request->user() || !$request->user()->approved) {
              Auth::logout();
              return redirect()->route('login')->with('status', 'Your account is awaiting admin approval.');
          }

          return $next($request);
      } */
}
