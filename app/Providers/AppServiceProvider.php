<?php

namespace App\Providers;

use App\Models as HWCModel;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
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
        View::composer(['header','topmenu','sidenav'], function ($view): void {
    		$announcements = HWCModel\Announcements::where('status', 1)->orderBy('display_order')->get();
            $marqueTXT = null;
            foreach($announcements as $announcement) $marqueTXT .= strip_tags($announcement->message);
            
			$settings = HWCModel\Settings::select('company_phone','admin_email')->first();
                        
            $menus = HWCModel\PageContent::select(['UniqueKey', 'EnTitle'])
                ->whereIn('menu_type', [2, 4])
                ->where(['parent_id' => 0, 'display_status' => 1])
                ->orderBy('display_order')
                ->get();

            $categories = HWCModel\Category::selectRaw('TypeId,LOWER(UniqueKey) as UniqueKey,EnName')->where(['TypeStatus' => 1, 'ParentLevel' => 0])->orderBy('DisplayOrder')->get();
	        $brands = HWCModel\Brand::selectRaw('Image,LOWER(UniqueKey) as UniqueKey,EnName')->where(['BrandStatus' => 1, 'ParentLevel' => 0, 'PopularBrand' => 1])->orderBy('DisplayOrder')->get();

            $view->with([
                'settings' => $settings, 
                'marqueTXT' => $marqueTXT, 
                'menus' => $menus,
                'sideNavs' => [
                    'categories' => $categories,
                    'brands' => $brands
                ]
            ]);
        });

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
