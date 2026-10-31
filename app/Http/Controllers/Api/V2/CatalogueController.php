<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\BrandResource;
use App\Http\Resources\V2\CategoryResource;
use App\Http\Resources\V2\ProductDetailResource;
use App\Http\Resources\V2\ProductResource;
use App\Models\Announcements;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Country;
use App\Models\MastheadImage;
use App\Models\MobileVersions;
use App\Models\PageContent;
use App\Models\PaymentMethods;
use App\Models\Product;
use App\Models\Promotions;
use App\Models\ShippingMethods;
use App\Support\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** Public catalogue and store data (no sign-in needed). */
class CatalogueController extends Controller
{
    private const PAGE_SIZE = 20;

    private const SORTS = [
        'newest' => ['Id', 'desc'],
        'name_asc' => ['EnName', 'asc'],
        'name_desc' => ['EnName', 'desc'],
        'price_asc' => ['Price', 'asc'],
        'price_desc' => ['Price', 'desc'],
        'featured' => ['DisplayOrder', 'asc'],
    ];

    public function categories()
    {
        $categories = Category::where('TypeStatus', 1)->where('ParentLevel', 0)->orderBy('DisplayOrder')
            ->with(['children' => fn ($q) => $q->where('TypeStatus', 1)->orderBy('DisplayOrder')])->get();

        return CategoryResource::collection($categories);
    }

    public function brands(Request $request)
    {
        $brands = Brand::where('BrandStatus', 1)
            ->when($request->boolean('popular'), fn ($q) => $q->where('PopularBrand', 1))
            ->when($request->filled('letter'), fn ($q) => $request->letter === '0-9'
                ? $q->whereRaw('EnName REGEXP ?', ['^[0-9]'])
                : $q->where('EnName', 'LIKE', substr((string) $request->letter, 0, 1) . '%'))
            ->orderBy('EnName')->get();

        return BrandResource::collection($brands);
    }

    /**
     * Product search and listing. Filters: search, category (includes its
     * sub-categories), brand, promotion=1, new=1. Sort: see SORTS.
     */
    public function products(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:100',
            'category' => 'nullable|integer',
            'brand' => 'nullable|integer',
            'sort' => 'nullable|in:' . implode(',', array_keys(self::SORTS)),
        ]);

        $query = Product::where('ProdStatus', 1);

        if ($request->filled('search')) {
            $like = '%' . $request->search . '%';
            $query->where(fn ($q) => $q->where('EnName', 'LIKE', $like)->orWhere('Code', 'LIKE', $like)->orWhere('ProdCode', 'LIKE', $like));
        }
        if ($request->filled('category')) {
            $ids = Category::where('ParentLevel', $request->category)->pluck('TypeId')->push((int) $request->category);
            $query->whereIn('Types', $ids);
        }
        if ($request->filled('brand')) {
            $query->where('Brand', (int) $request->brand);
        }
        if ($request->boolean('promotion')) {
            $query->where('IsPromotion', 1);
        }
        if ($request->boolean('new')) {
            $query->where('IsNewarrival', 1);
        }

        [$column, $direction] = self::SORTS[$request->input('sort', 'featured')];

        return ProductResource::collection($query->orderBy($column, $direction)->orderBy('Id', 'desc')->paginate(self::PAGE_SIZE));
    }

    public function product(int $id)
    {
        $product = Product::where('Id', $id)->where('ProdStatus', 1)->firstOrFail();

        return new ProductDetailResource($product);
    }

    public function banners(): JsonResponse
    {
        $map = fn ($folder) => fn ($b) => [
            'title' => (string) $b->ban_name,
            'link' => $b->ban_link ?: null,
            'caption' => strip_tags((string) $b->ban_caption),
            'image' => Media::upload($folder, $b->EnBanMobileimage ?: $b->EnBanimage),
        ];

        return response()->json(['data' => [
            'home' => Promotions::where('ban_status', 1)->orderBy('display_order')->get()->map($map('promotionsbanner')),
            'ads' => MastheadImage::where('ban_status', 1)->orderBy('display_order')->get()->map($map('bannermaster')),
            'announcements' => Announcements::where('status', 1)->orderBy('display_order')->pluck('message'),
        ]]);
    }

    public function countries(): JsonResponse
    {
        return response()->json(['data' => Country::where('country_status', 1)->orderBy('countryname')->get()->map(fn ($c) => [
            'id' => (int) $c->countryid, 'name' => $c->countryname, 'code' => $c->countrycode,
            'phone_code' => $c->phonecode, 'tax_label' => $c->taxtitle ?: 'Tax', 'tax_percentage' => (float) $c->taxpercentage,
        ])]);
    }

    /** Static pages the app shows: terms-and-conditions, faq, privacy-policy, ... */
    public function page(string $key): JsonResponse
    {
        $page = PageContent::where('UniqueKey', $key)->firstOrFail();

        return response()->json(['data' => ['key' => $page->UniqueKey, 'title' => $page->EnTitle, 'content' => $page->EnContent]]);
    }

    /** The latest app version (admins add rows to force older apps to update). */
    public function appVersion(): JsonResponse
    {
        $latest = MobileVersions::orderBy('id', 'desc')->first();

        return response()->json(['data' => ['version_id' => $latest->version_id ?? null, 'version_name' => $latest->version_name ?? null]]);
    }

    /** Shipping methods for a destination: SG uses local methods, elsewhere international ones. */
    public function shippingMethods(Request $request): JsonResponse
    {
        $request->validate(['country' => 'required|string|size:2']);

        $methods = ShippingMethods::where('Status', 1)->where('shipping_type', $request->country === 'SG' ? 0 : 1)
            ->orderBy('DisplayOrder')->get(['Id', 'EnName', 'FreeShipAvailable', 'FreeShipCost']);

        // Same rule as v1: in SG, method 5 (free delivery) replaces method 6 once the
        // signed-in customer's cart is above the free-shipping amount.
        $customer = auth('sanctum')->user();
        if ($request->country === 'SG' && $customer) {
            $subtotal = app(\App\Services\CartStore::class)->lines($customer->cust_id)->sum('line_total');
            $free = $methods->firstWhere('FreeShipAvailable', 1);
            $hide = ($free && $subtotal > $free->FreeShipCost) ? 6 : 5;
            $methods = $methods->reject(fn ($m) => (int) $m->Id === $hide)->values();
        }

        return response()->json(['data' => $methods->map(fn ($m) => ['id' => (int) $m->Id, 'name' => $m->EnName])]);
    }

    /** Payment methods the app can use (GrabPay is web-only, Hoolah removed). Never includes secrets. */
    public function paymentMethods(): JsonResponse
    {
        $methods = PaymentMethods::available()->where('status', 1)->where('Id', '!=', 5)->whereIn('type', [1, 2])->get();

        return response()->json(['data' => $methods->map(fn ($m) => [
            'id' => (int) $m->Id,
            'name' => $m->payment_name,
            'public_key' => (int) $m->Id === 3 || (int) $m->Id === 6
                ? ($m->payment_mode === 'live' ? $m->api_key : $m->test_api_key)
                : null,
            'mode' => $m->payment_mode === 'live' ? 'live' : 'test',
        ])]);
    }
}
