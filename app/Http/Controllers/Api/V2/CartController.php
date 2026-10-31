<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V2\CartItemRequest;
use App\Http\Requests\Api\V2\CouponRequest;
use App\Http\Requests\Api\V2\QuoteRequest;
use App\Http\Requests\Api\V2\UpdateCartItemRequest;
use App\Models\Product;
use App\Models\ProductOptions;
use App\Services\CartStore;
use App\Services\CouponValidator;
use App\Services\MobileCheckoutQuote;
use App\Support\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** The signed-in customer's cart, coupons and checkout quote. */
class CartController extends Controller
{
    public function __construct(private CartStore $cart)
    {
    }

    public function show(Request $request): JsonResponse
    {
        return $this->cartResponse($request->user()->cust_id);
    }

    public function add(CartItemRequest $request): JsonResponse
    {
        $product = Product::where('Id', $request->product_id)->where('ProdStatus', 1)->first();
        if (!$product) {
            return response()->json(['message' => 'This product is not available.'], 422);
        }

        $option = null;
        if ($request->filled('option_id')) {
            $option = ProductOptions::where('Id', $request->option_id)->where('Prod', $product->Id)->where('Status', 1)->first();
            if (!$option) {
                return response()->json(['message' => 'This option is not available.', 'errors' => ['option_id' => ['This option is not available.']]], 422);
            }
        }

        $this->cart->add($request->user()->cust_id, $product, $option, (int) $request->quantity);

        return $this->cartResponse($request->user()->cust_id, 201);
    }

    public function update(UpdateCartItemRequest $request, int $line): JsonResponse
    {
        if (!$this->cart->setQuantity($request->user()->cust_id, $line, (int) $request->quantity)) {
            return response()->json(['message' => 'Cart item not found.'], 404);
        }

        return $this->cartResponse($request->user()->cust_id);
    }

    public function remove(Request $request, int $line): JsonResponse
    {
        if (!$this->cart->remove($request->user()->cust_id, $line)) {
            return response()->json(['message' => 'Cart item not found.'], 404);
        }

        return $this->cartResponse($request->user()->cust_id);
    }

    public function clear(Request $request): JsonResponse
    {
        $this->cart->clear($request->user()->cust_id);

        return $this->cartResponse($request->user()->cust_id);
    }

    /** Full price breakdown for a destination + shipping method (the total checkout will charge). */
    public function quote(QuoteRequest $request, MobileCheckoutQuote $quotes, CouponValidator $coupons): JsonResponse
    {
        $customer = $request->user();
        $lines = $this->cart->lines($customer->cust_id);
        if ($lines->isEmpty()) {
            return response()->json(['message' => 'Your cart is empty.'], 422);
        }

        // Apply the coupon only if placing the order would accept it
        $code = (string) $request->coupon_code;
        $couponError = null;
        if ($code !== '') {
            $check = $coupons->check($code, $customer->cust_id, $lines->pluck('product.Id')->all());
            if (!$check['valid']) {
                $couponError = $check['message'];
                $code = '';
            }
        }

        $data = self::quoteData($quotes->forCustomer($customer->cust_id, $request->country, $request->shipping_method_id, $code));
        $data['coupon_error'] = $couponError ?? $data['coupon_error'];

        return response()->json(['data' => $data]);
    }

    public function checkCoupon(CouponRequest $request, CouponValidator $coupons): JsonResponse
    {
        $customer = $request->user();
        $check = $coupons->check($request->coupon_code, $customer->cust_id, $this->cart->lines($customer->cust_id)->pluck('product.Id')->all());

        if (!$check['valid']) {
            return response()->json(['message' => $check['message'], 'errors' => ['coupon_code' => [$check['message']]]], 422);
        }

        return response()->json(['data' => [
            'code' => $check['coupon']->coupon_code,
            'type' => (int) $check['coupon']->discount_type === 1 ? 'percent' : 'fixed',
            'value' => (float) $check['coupon']->discount,
            'message' => $check['message'],
        ]]);
    }

    /** Turns MobileCheckoutQuote's legacy field names into the v2 shape. */
    public static function quoteData(array $quote): array
    {
        $num = fn ($v) => round((float) str_replace(',', '', (string) $v), 2);

        return [
            'subtotal_excl_tax' => $num($quote['subtotal']),
            'tax' => $num($quote['gst']),
            'tax_label' => $quote['gstText'],
            'shipping' => $num($quote['totaldeliverycost']),
            'fuel_charges' => $num($quote['fuelcharges']),
            'handling_fee' => $num($quote['handlingfee']),
            'packaging_fee' => $num($quote['packagingprice']),
            'discount' => $num($quote['discount']),
            'discount_label' => $quote['discount_text'] ?: null,
            'coupon_error' => $quote['is_discount_invalid'] ? $quote['discount_error_text'] : null,
            'total' => MobileCheckoutQuote::amount($quote),
            'currency' => 'SGD',
        ];
    }

    private function cartResponse(int $customerId, int $status = 200): JsonResponse
    {
        $lines = $this->cart->lines($customerId);

        return response()->json(['data' => [
            'items' => $lines->map(fn ($l) => [
                'id' => $l->id,
                'product_id' => (int) $l->product->Id,
                'name' => (string) $l->product->EnName,
                'option' => $l->option ? ['id' => (int) $l->option->Id, 'title' => (string) $l->option->Title] : null,
                'image' => Media::productImage($l->product),
                'quantity' => $l->quantity,
                'unit_price' => $l->unit_price,
                'line_total' => $l->line_total,
                'in_stock' => $l->in_stock,
            ]),
            'item_count' => $lines->sum('quantity'),
            'subtotal' => round($lines->sum('line_total'), 2),
            'currency' => 'SGD',
        ]], $status);
    }
}
