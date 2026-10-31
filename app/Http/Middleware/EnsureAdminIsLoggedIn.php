<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureAdminIsLoggedIn
{
    /**
     * Every admin route except the login form, login post and logout needs an
     * admin logged in on the "admin" guard (AdminController@verifylogin).
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('admin')->check()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect('/admin/');
        }

        return $next($request);
    }
}
