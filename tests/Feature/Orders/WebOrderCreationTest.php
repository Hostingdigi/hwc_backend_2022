<?php

use App\Models\Customer;
use App\Services\OrderServices;
use Illuminate\Support\Facades\DB;
use Tests\Support\Fixtures;

// Characterization tests: OrderServices::createOrder, used by the web checkout
// before handing off to a payment gateway.

beforeEach(function () {
    Fixtures::insert('country', ['countryid' => 189, 'countryname' => 'Singapore', 'countrycode' => 'SG', 'taxtitle' => 'GST', 'taxpercentage' => 9]);
    Fixtures::insert('paymentsettings', ['id' => 1, 'fuelcharge_percentage' => 20, 'min_package_fee' => 5, 'free_shipping_amount' => 100]);
    Fixtures::insert('local_shipping_methods', ['Status' => 1, 'DisplayOrder' => 1, 'FreeShipCost' => 100, 'PriceRange5' => 8]);
    Fixtures::insert('paymentmethods', ['Id' => 3, 'payment_name' => 'Credit Card', 'status' => 1]);

    $this->customer = Fixtures::insert('customer_master', ['cust_email' => 'buyer@example.test', 'cust_status' => 1]);
    $this->drill = Fixtures::insert('products', ['EnName' => 'Drill', 'Types' => 50, 'Price' => 54.50, 'Quantity' => 10, 'ProdStatus' => 1]);
    $this->bits = Fixtures::insert('products', ['EnName' => 'Drill bits', 'Types' => 50, 'Price' => 10.90, 'Quantity' => 2, 'ProdStatus' => 1]);

    $address = [
        'fname' => 'Tan', 'lname' => 'Ah Kow', 'email' => 'buyer@example.test', 'mobile' => '81234567',
        'ads1' => '1 Test Road', 'ads2' => '', 'city' => 'Singapore', 'state' => '', 'zip' => '123456', 'country' => 'SG',
    ];
    $billing = ['bill_compname' => 'Test Pte Ltd'];
    foreach ($address as $key => $value) {
        $billing['bill_' . $key] = $value;
        $billing['ship_' . $key] = $value;
    }

    // Charges stored at checkout; createOrder trusts these instead of recomputing them
    Fixtures::insert('session_cart', [
        'cust_id' => $this->customer, 'billinginfo' => json_encode($billing), 'deliverymethod' => '2',
        'if_unavailable' => '1', 'delivery_instructions' => 'Leave at door',
        'gst' => 6.30, 'taxPercentage' => 9, 'taxtitle' => 'GST (9%)', 'taxLabelOnly' => 'GST',
        'deliverycost' => 8.02, 'packingfee' => 0, 'fuelcharges' => 0, 'handlingfee' => 0, 'discount' => 0,
        'created_at' => now(), 'updated_at' => now(),
    ]);

    $this->actingAs(Customer::find($this->customer), 'web');

    session([
        'paymentmethod' => 3,
        '_token' => 'cart-token',
        'cartdata' => ['cart-token' => [
            ['productId' => $this->drill, 'productName' => 'Drill', 'qty' => 1, 'price' => 54.50, 'total' => 54.50,
             'weight' => 2, 'shippingbox' => '', 'productoption' => '', 'productcode' => 'DR-1'],
            ['productId' => $this->bits, 'productName' => 'Drill bits', 'qty' => 2, 'price' => 10.90, 'total' => 21.80,
             'weight' => 0.2, 'shippingbox' => '', 'productoption' => '', 'productcode' => 'DB-1'],
        ]],
    ]);
});

it('creates an unpaid order whose total is live subtotal + stored charges, rounded up to 5 cents', function () {
    $result = app(OrderServices::class)->createOrder();

    expect($result['status'])->toBeTrue();
    $order = DB::table('order_master')->where('order_id', $result['orderId'])->first();

    // 70.00 ex-tax (live) + 6.30 GST + 8.02 delivery (stored) = 84.32 -> 84.35
    expect((float) $order->payable_amount)->toEqual(84.35)
        ->and((int) $order->order_status)->toBe(0)
        ->and((int) $order->is_fulfilled)->toBe(0)
        ->and((int) $order->user_id)->toBe($this->customer)
        ->and($order->pay_method)->toBe('Credit Card')
        ->and($order->ship_country)->toBe('SG');
});

it('stores one order_details row per cart line and a pending order_payments row', function () {
    $orderId = app(OrderServices::class)->createOrder()['orderId'];

    $lines = DB::table('order_details')->where('order_id', $orderId)->orderBy('prod_id')->get();
    expect($lines)->toHaveCount(2)
        ->and($lines->pluck('prod_quantity')->map(fn ($q) => (int) $q)->all())->toBe([1, 2])
        ->and(DB::table('order_payments')->where('order_id', $orderId)->value('payment_status'))->toEqual(0);
});

it('reduces stock at order creation, before payment, never below zero', function () {
    app(OrderServices::class)->createOrder();

    expect((int) DB::table('products')->where('Id', $this->drill)->value('Quantity'))->toBe(9)
        // 2 in stock, 2 ordered: "Quantity > qty" is false, so it is set to 0
        ->and((int) DB::table('products')->where('Id', $this->bits)->value('Quantity'))->toBe(0);
});

it('reuses the customer\'s existing unfulfilled order instead of creating a new one', function () {
    \Illuminate\Support\Carbon::setTestNow('2026-10-31 10:00:00');
    $first = app(OrderServices::class)->createOrder()['orderId'];

    \Illuminate\Support\Carbon::setTestNow('2026-10-31 10:00:05');
    $second = app(OrderServices::class)->createOrder()['orderId'];

    expect($second)->toBe($first)
        ->and(DB::table('order_master')->where('user_id', $this->customer)->count())->toBe(1)
        // Fixed in Phase 3: the lines are replaced, not appended a second time
        ->and(DB::table('order_details')->where('order_id', $first)->count())->toBe(2);
});

it('succeeds when retried within the same second', function () {
    // Fixed in Phase 3: MySQL reports 0 changed rows here, which used to fail checkout
    \Illuminate\Support\Carbon::setTestNow('2026-10-31 10:00:00');
    $first = app(OrderServices::class)->createOrder()['orderId'];

    $retry = app(OrderServices::class)->createOrder();

    expect($retry['status'])->toBeTrue()
        ->and($retry['orderId'])->toBe($first);
});

it('does not take stock a second time when checkout is retried', function () {
    DB::table('products')->where('Id', $this->drill)->update(['Quantity' => 10]);
    DB::table('products')->where('Id', $this->bits)->update(['Quantity' => 10]);

    app(OrderServices::class)->createOrder();
    app(OrderServices::class)->createOrder();

    expect((int) DB::table('products')->where('Id', $this->drill)->value('Quantity'))->toBe(9)
        ->and((int) DB::table('products')->where('Id', $this->bits)->value('Quantity'))->toBe(8);
});

it('puts stock back when a retried checkout has fewer items', function () {
    DB::table('products')->where('Id', $this->bits)->update(['Quantity' => 10]);
    app(OrderServices::class)->createOrder(); // 2 bits -> 8 left

    $cart = session('cartdata');
    $cart['cart-token'][1]['qty'] = 1;
    session(['cartdata' => $cart]);
    app(OrderServices::class)->createOrder(); // now 1 bit -> 9 left

    expect((int) DB::table('products')->where('Id', $this->bits)->value('Quantity'))->toBe(9);
});
