<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V2\FeedbackRequest;
use App\Http\Requests\Api\V2\QuestionRequest;
use App\Http\Requests\Api\V2\ReviewRequest;
use App\Http\Resources\V2\ProductResource;
use App\Mail\TemplateMail;
use App\Models\FavouriteProducts;
use App\Models\Product;
use App\Models\ProductReviews;
use App\Models\Settings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

/** Wishlist, product reviews and questions, and store feedback. */
class EngagementController extends Controller
{
    public function wishlist(Request $request)
    {
        $ids = FavouriteProducts::where('cust_id', $request->user()->cust_id)->pluck('prod_id');

        return ProductResource::collection(Product::whereIn('Id', $ids)->where('ProdStatus', 1)->get());
    }

    public function addToWishlist(Request $request): JsonResponse
    {
        $request->validate(['product_id' => 'required|integer|exists:products,Id']);

        $exists = FavouriteProducts::where(['cust_id' => $request->user()->cust_id, 'prod_id' => $request->product_id])->exists();
        if (!$exists) {
            FavouriteProducts::query()->insert([
                'cust_id' => $request->user()->cust_id, 'prod_id' => $request->product_id,
                'date_entered' => now(), 'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        return response()->json(['message' => 'Added to your wishlist.'], 201);
    }

    public function removeFromWishlist(Request $request, int $product): JsonResponse
    {
        FavouriteProducts::where(['cust_id' => $request->user()->cust_id, 'prod_id' => $product])->delete();

        return response()->json(['message' => 'Removed from your wishlist.']);
    }

    /** One review per customer per product; a new one replaces the old. Shown after admin approval (status 1). */
    public function review(ReviewRequest $request, int $product): JsonResponse
    {
        Product::where('Id', $product)->where('ProdStatus', 1)->firstOrFail();

        ProductReviews::query()->updateOrInsert(
            ['ProdId' => $product, 'CustomerId' => $request->user()->cust_id],
            ['rating' => $request->rating, 'comments' => (string) $request->comment, 'status' => 0, 'created_at' => now(), 'updated_at' => now()]
        );

        return response()->json(['message' => 'Thanks! Your review will appear once approved.'], 201);
    }

    public function question(QuestionRequest $request, int $product): JsonResponse
    {
        $item = Product::where('Id', $product)->where('ProdStatus', 1)->firstOrFail();
        $customer = $request->user();

        $this->toStore('Product question: ' . $item->EnName,
            '<p><b>' . e(trim($customer->cust_firstname . ' ' . $customer->cust_lastname)) . '</b> (' . e($customer->cust_email) . ') asked about '
            . e($item->EnName) . ' (' . e($item->Code) . '):</p><p>' . nl2br(e($request->question)) . '</p>');

        return response()->json(['message' => 'Your question has been sent.'], 201);
    }

    public function feedback(FeedbackRequest $request): JsonResponse
    {
        $this->toStore('App feedback from ' . $request->name,
            '<p><b>' . e($request->name) . '</b> (' . e($request->email) . ($request->phone ? ', ' . e($request->phone) : '') . ')</p><p>'
            . nl2br(e($request->message)) . '</p>');

        return response()->json(['message' => 'Thanks for your feedback.'], 201);
    }

    private function toStore(string $subject, string $html): void
    {
        $admin = Settings::where('id', 1)->value('admin_email');
        if ($admin) {
            Mail::to($admin)->send(new TemplateMail($subject, $html));
        }
    }
}
