<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        // Admin can access everything
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        $allowedRoles = explode(',', $role);
        if (!Auth::check() || !in_array(Auth::user()->role, $allowedRoles)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}