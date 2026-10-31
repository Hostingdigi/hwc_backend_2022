<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V2\PlaceOrderRequest;
use App\Http\Resources\V2\OrderResource;
use App\Models\OrderMaster;
use App\Services\OrderPlacement;
use App\Services\OrderPlacementException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** Placing orders and reading the signed-in customer's orders. */
class OrderController extends Controller
{
    public function index(Request $request)
    {
        $request->validate(['type' => 'nullable|in:order,quotation']);

        $orders = OrderMaster::where('user_id', $request->user()->cust_id)
            ->when($request->type, fn ($q) => $q->where('order_type', $request->type === 'quotation' ? 2 : 1))
            ->orderBy('order_id', 'desc')
            ->paginate(20);

        return OrderResource::collection($orders);
    }

    public function show(Request $request, int $order)
    {
        return new OrderResource($this->ownOrder($request, $order)->load('details'));
    }

    /**
     * Creates an unpaid order from the cart. Errors use stable "reason" codes:
     * cart_empty, out_of_stock, coupon_invalid, price_changed (with the current
     * quote), payment_method_unavailable.
     */
    public function store(PlaceOrderRequest $request, OrderPlacement $placement): JsonResponse
    {
        try {
            $result = $placement->place($request->user(), $request->validated());
        } catch (OrderPlacementException $e) {
            return response()->json(array_filter([
                'message' => $e->getMessage(),
                'reason' => $e->reason,
                'quote' => $e->quote ? CartController::quoteData($e->quote) : null,
            ]), $e->reason === 'price_changed' ? 409 : 422);
        }

        return (new OrderResource($result['order']->load('details')))->response()->setStatusCode(201);
    }

    /** An order of the signed-in customer, or 404 (never someone else's). */
    public static function ownOrder(Request $request, int $orderId): OrderMaster
    {
        return OrderMaster::where('order_id', $orderId)->where('user_id', $request->user()->cust_id)->firstOrFail();
    }
}
