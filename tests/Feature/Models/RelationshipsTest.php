<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Couponcode;
use App\Models\Customer;
use App\Models\OrderMaster;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Tests\Support\Fixtures;

// Phase 2: Eloquent relationships over the legacy keys (no $primaryKey changes).

beforeEach(function () {
    $this->customer = Fixtures::insert('customer_master', ['cust_email' => 'buyer@example.test', 'cust_status' => 1]);
    Fixtures::insert('types', ['TypeId' => 10, 'EnName' => 'Tools', 'ParentLevel' => 0]);
    Fixtures::insert('types', ['TypeId' => 11, 'EnName' => 'Power Tools', 'ParentLevel' => 10]);
    Fixtures::insert('brands', ['BrandId' => 7, 'EnName' => 'Bosch']);
    $this->product = Fixtures::insert('products', ['EnName' => 'Drill', 'Types' => 11, 'Brand' => 7, 'ProdStatus' => 1]);
    $this->coupon = Fixtures::insert('coupon_code', ['coupon_code' => 'SAVE10', 'discount' => 10, 'status' => 1]);
    $this->order = Fixtures::insert('order_master', ['user_id' => $this->customer, 'discount_id' => $this->coupon, 'order_status' => 1, 'date_entered' => now()]);
    Fixtures::insert('order_details', ['order_id' => $this->order, 'prod_id' => $this->product, 'prod_name' => 'Drill', 'prod_quantity' => 2]);
    Fixtures::insert('order_payments', ['order_id' => $this->order, 'payment_status' => 1]);
});

it('links orders, their lines, payments, customer and coupon', function () {
    $order = OrderMaster::where('order_id', $this->order)->first();

    expect($order->customer->cust_email)->toBe('buyer@example.test')
        ->and($order->details)->toHaveCount(1)
        ->and($order->details->first()->product->EnName)->toBe('Drill')
        ->and($order->details->first()->order->order_id)->toBe($this->order)
        ->and($order->payments)->toHaveCount(1)
        ->and($order->coupon->coupon_code)->toBe('SAVE10');
});

it('links a customer to their orders', function () {
    expect(Customer::where('cust_id', $this->customer)->first()->orders->pluck('order_id')->all())->toBe([$this->order]);
});

it('links products to category, brand, and categories to parent/children', function () {
    $product = Product::where('Id', $this->product)->first();

    expect($product->category->EnName)->toBe('Power Tools')
        ->and($product->category->parent->EnName)->toBe('Tools')
        ->and($product->brand->EnName)->toBe('Bosch')
        ->and(Category::where('TypeId', 10)->first()->children->pluck('EnName')->all())->toBe(['Power Tools'])
        ->and(Brand::where('BrandId', 7)->first()->products)->toHaveCount(1);
});

it('eager-loads order lines in one query instead of one per order (no N+1)', function () {
    foreach (range(1, 5) as $i) {
        $id = Fixtures::insert('order_master', ['user_id' => $this->customer, 'order_status' => 1, 'date_entered' => now()]);
        Fixtures::insert('order_details', ['order_id' => $id, 'prod_id' => $this->product, 'prod_name' => 'Drill', 'prod_quantity' => 1]);
    }

    DB::enableQueryLog();
    $orders = OrderMaster::with('details.product')->get();
    $orders->each(fn ($o) => $o->details->each(fn ($d) => $d->product?->EnName));

    expect(DB::getQueryLog())->toHaveCount(3); // orders, details, products
});

it('counts coupon usages', function () {
    DB::table('coupon_code_usage')->insert(['coupon_id' => $this->coupon, 'customer_id' => $this->customer, 'order_id' => $this->order]);

    expect(Couponcode::find($this->coupon)->usages)->toHaveCount(1);
});
