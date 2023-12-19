<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectBasedOnRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user) {
            if ($user->role == User::ROLE_OPERATIONAL) {
                return redirect()->route('profileShowURL');
            } elseif ($user->role == User::ROLE_CONTROLLERSHIP) {
                return redirect()->route('surveysIndexURL');
            } else {
                return redirect()->route('root');
            }
        }

        return $next($request);
    }
}
