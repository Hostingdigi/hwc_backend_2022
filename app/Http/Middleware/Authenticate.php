<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
       /* if (! $request->expectsJson()) {
            return route('login');
        }*/

        $guard = array_get($exeption->guards(), 0);
        switch($guard)
        {
            case 'admin':
                $login = 'admin.login';
            break;
            default:
            $login ='login';
        break;
        }
        return route($login);
    }
}
