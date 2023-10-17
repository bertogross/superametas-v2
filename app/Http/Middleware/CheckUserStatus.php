<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        // Check if the user is authenticated
        if (Auth::check() && Auth::user()->status == 0) {
            // Log the user out
            Auth::logout();

            // Redirect to the login page with an error message
            return redirect('/login')->with('error', 'Your account has been deactivated. Please contact support.');
        }

        return $next($request);
    }
}
