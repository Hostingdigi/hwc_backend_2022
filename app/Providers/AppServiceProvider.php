<?php

namespace App\Providers;

use App\Models as HWCModel;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Services\CartServices;

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
        View::composer('layouts.app', function($view) : void {
            $metaData = [
                'title' => 'Your Default Meta Title here',
                'description' => 'Your default meta description here',
                'keywords' => 'default, keywords, here',
                'canonical' => 0,
                'seo_url' => '',
            ];
            $selectFields = ['meta_title', 'meta_keywords', 'meta_description'];

            $seo = HWCModel\PageContent::where('UniqueKey', 'home')->select($selectFields)->first();

            if($seo) {
                $metaData['title'] = $seo->meta_title;
                $metaData['description'] = $seo->meta_description;
                $metaData['keywords'] = $seo->meta_keywords;
            }

            $actionname = Route::currentRouteAction();
            $routename = Route::currentRouteName();
            $currenturl = url()->current();
            
            $seourl = '';
            $urlarray = @explode('/', $currenturl);
            if($urlarray) {
                $seourl = end($urlarray);
                $seourl = trim(str_replace('%20', ' ', $seourl));
            }
            $metaData['seo_url'] = $seourl;
            $uniqueKeyCondition = ['UniqueKey' => $seourl];

            if(request()->is('prod/*')) {		
                
                if(!empty($seourl)) {
                    $seo = HWCModel\Product::where($uniqueKeyCondition)->select('MetaTitle', 'MetaKey', 'MetaDesc')->first();
                    if($seo) {
                        $metaData['title'] = $seo->MetaTitle;
                        $metaData['keywords'] = $seo->MetaKey;
                        $metaData['description'] = $seo->MetaDesc;
                    }
                }
                $metaData['canonical'] = 1;

            } else {
                
                if(request()->is('promotions')) {
                    $seo = HWCModel\PageContent::where('UniqueKey', 'promotions')->select($selectFields)->first();
                } elseif(request()->is('brand/*')) {
                    if(!empty($seourl)) $seo = HWCModel\Brand::where($uniqueKeyCondition)->select($selectFields)->first();				
                } elseif(request()->is('type/*') || request()->is('category/*')) {
                    if(!empty($seourl)) $seo = HWCModel\Category::where($uniqueKeyCondition)->select($selectFields)->first();
                } elseif(strpos($actionname, '@staticpages') !== false) {
                    if(!empty($seourl)) $seo = HWCModel\PageContent::where($uniqueKeyCondition)->select($selectFields)->first();
                }

                if($seo) {
                    $metaData['title'] = $seo->meta_title;
                    $metaData['keywords'] = $seo->meta_keywords;
                    $metaData['description'] = $seo->meta_description;
                }
            }

            $announcements = HWCModel\Announcements::where('status', 1)->orderBy('display_order')
                ->get()->map(fn($i) => trim(strip_tags($i->message)))->toArray();
            
			$settings = HWCModel\Settings::select('company_phone','admin_email')->first();
                        
            $mainMenus = HWCModel\PageContent::select(['UniqueKey', 'EnTitle'])
                ->whereIn('menu_type', [2, 4])
                ->where(['parent_id' => 0, 'display_status' => 1])
                ->orderBy('display_order')
                ->get();

	        $categories = HWCModel\Category::selectRaw('TypeId,LOWER(UniqueKey) as UniqueKey,EnName')->where(['TypeStatus' => 1, 'ParentLevel' => 0])->orderBy('DisplayOrder')->get()
            ->map(function($category) {
                $subcategories = HWCModel\Category::selectRaw('TypeId,LOWER(UniqueKey) as UniqueKey,EnName')->where(['TypeStatus' => 1, 'ParentLevel' => $category->TypeId])
                    ->orderBy('DisplayOrder')->get()
                    ->map(function($subcategory) {
                        $subSubCategories = HWCModel\Category::selectRaw('TypeId,LOWER(UniqueKey) as UniqueKey,EnName')->where(['ParentLevel' => $subcategory->TypeId, 'TypeStatus' => 1])->orderBy('DisplayOrder')->get();
                        return [
                            'id' => $subcategory->TypeId,
                            'name' => $subcategory->EnName,
                            'unique_key' => $subcategory->UniqueKey,
                            'subcategories' => $subSubCategories->map(function($subSubCategory) {
                                return [
                                    'id' => $subSubCategory->TypeId,
                                    'name' => $subSubCategory->EnName,
                                    'unique_key' => $subSubCategory->UniqueKey,
                                ];
                            }),
                        ];
                    });

                return [
                    'id' => $category->TypeId,
                    'name' => $category->EnName,
                    'unique_key' => $category->UniqueKey,
                    'subcategories' => $subcategories
                ];
            });

            $brands = HWCModel\Brand::selectRaw('Image,LOWER(UniqueKey) as UniqueKey,EnName')->where(['BrandStatus' => 1, 'ParentLevel' => 0, 'PopularBrand' => 1])->orderBy('DisplayOrder')->get();

            if(auth()->check()) {
                $cartServices = new CartServices();
                $cartItems = $cartServices->cartItems();
                $cartCount = count($cartItems['cartItems']);
            } else {
                $cartCount = 0;
                if(session()->has('cartdata')) {
                    $sessid = session()->getId(); 
                    $cartitems = session()->get('cartdata');
                    if(count($cartitems) && isset($cartitems[$sessid])) $cartCount = count($cartitems[$sessid]);
                }
            }

            $footerMenus = HWCModel\PageContent::where([
                'menu_type' => 5,
                'display_status' => 1,
                'parent_id' => 0
            ])
            ->orderBy('display_order')->get()->map(function($menu) {

                $subMenus = HWCModel\PageContent::select(['id','UniqueKey','EnTitle'])->where([
                    'display_status' => 1,
                    'parent_id' => $menu->Id
                ])->orderBy('display_order')->get()->toArray();
                return [
                    'id' => $menu->Id,
                    'title' => $menu->EnTitle,
                    'sub_menus' => $subMenus
                ];
            });

            $topBar = [
                'settings' => [
                    'company_phone' => $settings->company_phone ?? '',
                    'admin_email' => $settings->admin_email ?? '',
                ],
                'announcements' => implode('',$announcements),
                'main_menus' => $mainMenus,
                'brands' => $brands,
                'categories' => $categories,
                'cart_count' => $cartCount > 0 ? $cartCount : null,
            ];

            $view->with([
                'meta' => $metaData,
                'topBar' => $topBar, 
                'footerBar' => [
                    'socialMedia' => HWCModel\Socialmedia::where('status', 1)->orderBy('display_order')->get(),
                    'menus' => $footerMenus,
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
