<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Carried over unchanged from Laravel 8's RouteServiceProvider. Note there is
        // no ->by(...), so this is ONE shared bucket of 60 requests/minute for the
        // whole API, not 60 per client.
        RateLimiter::for('api', function () {
            return Limit::perMinute(60);
        });

        // API v2: per customer (token) or, when signed out, per IP address
        RateLimiter::for('api-v2', function (Request $request) {
            return Limit::perMinute(120)->by(optional($request->user('sanctum'))->cust_id ?: $request->ip());
        });

        // Sign-in / sign-up / password reset: slows down password guessing
        RateLimiter::for('api-v2-auth', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });
    }
}
