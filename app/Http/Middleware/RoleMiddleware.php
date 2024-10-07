<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if the authenticated user's role is in the list of allowed roles
        if (!in_array($request->user()->role, $roles)) {
            return redirect()->route('dashboard')->with('error', 'Access Denied: You do not have permission to access this page.');
        }

        return $next($request);
    }
}
