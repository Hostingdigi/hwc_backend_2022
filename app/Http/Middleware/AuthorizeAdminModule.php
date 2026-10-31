<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Enforces role rights on the server. Before, rights only decided which links
 * the admin menu showed (resources/views/admin/includes/mainmenu.blade.php);
 * any admin could still open any URL.
 *
 * The module ids are the ones the menu uses. Pages the menu does not gate
 * (dashboard, menu, banners, product tags, ...) stay open to every admin.
 */
class AuthorizeAdminModule
{
    /** First path segment after /admin => module id */
    public const MODULES = [
        'static_pages' => 7,
        'banner_master' => 8,
        'promotions' => 9,
        'banner_ads' => 10,
        'announcement' => 11,
        'emailtemplate' => 12,
        'smstemplate' => 13,
        'socialmedia' => 14,
        'customergroup' => 15,
        'customer' => 16,
        'couponcode' => 17,
        'category' => 18,
        'brands' => 19,
        'brand' => 19,
        'products' => 20,
        'product' => 20,
        'exportproducts' => 20,
        'clearproductsearch' => 20,
        'orders' => 21,
        'exportorders' => 21,
        'archiveorder' => 21,
        'remove-order-items' => 21,
        'fetch_otions_and_price' => 21,
        'fetch_otions_price' => 21,
        'add_more_items_to_order' => 21,
        'update_order_item' => 21,
        'search' => 21,
        'pendingorders' => 22,
        'exportpendingorders' => 22,
        'archives' => 23,
        'subscriber' => 24,
        'subadmin_settings' => 25,
        'payment_settings' => 26,
        'paymentmethods' => 27,
        'shipping_methods' => 28,
        'country' => 29,
        'local_shipping_methods' => 30,
        'local_shipping_method' => 30,
        'international_shipping_methods' => 31,
        'roleandrights' => 32,
        'manageadmin' => 33,
        'passwordchange' => 34,
    ];

    public static function moduleFor(Request $request): ?int
    {
        $segments = array_slice($request->segments(), 1); // drop "admin"
        $first = $segments[0] ?? '';

        // Category shipping charges sit under module 29 in the menu, not 18
        if ($first === 'category' && ($segments[1] ?? '') === 'shipping-charges') {
            return 29;
        }

        return self::MODULES[$first] ?? null;
    }

    public function handle(Request $request, Closure $next)
    {
        $module = self::moduleFor($request);
        $admin = Auth::guard('admin')->user();

        if ($module !== null && $admin && !$admin->canAccessModule($module)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'You do not have access to this section.'], 403);
            }
            abort(403, 'You do not have access to this section.');
        }

        return $next($request);
    }
}
