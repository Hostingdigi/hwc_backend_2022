<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Session;

class EnsureAdminIsLoggedIn
{
    /**
     * Admin login (AdminController@verifylogin) stores admin_id in the session.
     * Every admin route except the login form and the login post must have it.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Session::has('admin_id')) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect('/admin/');
        }

        return $next($request);
    }
}
