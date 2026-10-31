<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BlockPunchoutRoutes
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (session('punchout') === true) {
            $currentPath = $request->path();
            $blockedPaths = ['customer/myorders','customer/pendingorders','customer/quotations','customer/address','customer/changepassword',
                'login'];
            if(in_array($currentPath,$blockedPaths)) return abort(403, 'Access denied for punchout session');
        }

        return $next($request);
    }
}
