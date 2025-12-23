<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Menu;
use Illuminate\Support\Facades\Log;

class CheckMenuAccess
{
    public function handle(Request $request, Closure $next)
    {
        // Admin can access everything
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        $routeName = $request->route()->getName();
        $userRole = Auth::user()->role;

        Log::info("CheckMenuAccess: Route {$routeName}, User role {$userRole}");

        $menu = Menu::where('route', $routeName)->first();

        // If exact route match, check hasRole
        if ($menu) {
            Log::info("Menu found: {$menu->name}, hasRole: " . ($menu->hasRole($userRole) ? 'yes' : 'no'));
            if ($menu->hasRole($userRole)) {
                return $next($request);
            }
        }

        // If no exact match, check for parent route (e.g., barang-masuk.index for barang-masuk.create)
        $parentRoute = explode('.', $routeName);
        if (count($parentRoute) > 1) {
            array_pop($parentRoute); // remove last part
            $parentRouteName = implode('.', $parentRoute) . '.index';
            $parentMenu = Menu::where('route', $parentRouteName)->first();
            if ($parentMenu) {
                Log::info("Parent menu found: {$parentMenu->name}, hasRole: " . ($parentMenu->hasRole($userRole) ? 'yes' : 'no'));
                if ($parentMenu->hasRole($userRole)) {
                    return $next($request);
                }
            }
        }

        Log::info("Access denied for route {$routeName}");
        abort(403, 'Unauthorized');
    }
}