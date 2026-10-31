<?php

use Illuminate\Support\Facades\DB;
use Tests\Support\Fixtures;

// Mobile checkout: the price quote the app shows (POST /api/get-shipping-package-price)
// and order creation (POST /api/createorder). Responses must stay byte-compatible
// with what the live app expects.

beforeEach(function () {
    Fixtures::insert('country', ['countryid' => 189, 'countryname' => 'Singapore', 'countrycode' => 'SG', 'taxtitle' => 'GST', 'taxpercentage' => 9]);
    Fixtures::insert('paymentsettings', ['id' => 1, 'currency_type' => 'SGD', 'fuelcharge_percentage' => 20, 'min_package_fee' => 5,
        'free_shipping_amount' => 100, 'discount_percentage' => 0, 'promo_discount_percentage' => 0]);
    Fixtures::insert('settings', ['id' => 1, 'company_name' => 'HardwareCity', 'admin_email' => 'admin@example.test', 'cc_email' => 'cc@example.test']);
    Fixtures::insert('local_shipping_methods', ['Status' => 1, 'DisplayOrder' => 1, 'FreeShipCost' => 100,
        'PriceRange5' => 8, 'PriceRange15' => 12, 'PriceRange30' => 20, 'PriceRangeAbove30' => 35]);
    Fixtures::insert('shipping_methods', ['Id' => 2, 'EnName' => 'Courier', 'shipping_type' => 1, 'Status' => 1]);
    Fixtures::insert('paymentmethods', ['Id' => 3, 'payment_name' => 'Credit Card', 'payment_mode' => 'test', 'test_api_signature' => 'sk_test_fake', 'status' => 1]);

    $this->customer = Fixtures::insert('customer_master', ['cust_email' => 'buyer@example.test', 'cust_status' => 1]);
    $this->drill = Fixtures::insert('products', ['EnName' => 'Drill', 'Code' => 'DR-1', 'Price' => 54.50, 'StandardPrice' => 54.50,
        'Weight' => 2, 'ShippingBox' => '', 'Quantity' => 10, 'ProdStatus' => 1]);
    DB::table('cart_details')->insert(Fixtures::fill('cart_details', ['user_id' => $this->customer, 'prod_id' => $this->drill,
        'prod_name' => 'Drill', 'prod_quantity' => 1, 'prod_unit_price' => 54.50, 'prod_option_id' => 0]));
});

function quote($test, array $overrides = [])
{
    return $test->postJson('/api/get-shipping-package-price', ['orderinfo' => [$overrides + [
        'customer_id' => $test->customer, 'country' => 'SG', 'shipmethod' => 2, 'couponcode' => '',
    ]]]);
}

function mobileOrder($test, array $overrides = [])
{
    $address = ['fname' => 'Tan', 'lname' => 'Ah Kow', 'email' => 'buyer@example.test', 'mobile' => '81234567',
        'address1' => '1 Test Road', 'address2' => '', 'city' => 'Singapore', 'state' => '', 'zip' => '123456', 'country' => 'SG'];
    $billing = ['bill_compname' => ''];
    $shipping = [];
    foreach ($address as $key => $value) {
        $billing['bill_' . $key] = $value;
        $shipping['ship_' . $key] = $value;
    }

    return $test->postJson('/api/createorder', ['orderinfo' => [$overrides + [
        'customer_id' => $test->customer, 'billing' => [$billing], 'shipping' => [$shipping],
        'products' => [['id' => $test->drill, 'name' => 'Drill', 'quantity' => 1, 'price' => 54.50, 'total' => 54.50,
            'option' => '', 'weight' => 2, 'code' => 'DR-1']],
        'subtotal' => '50.00', 'tax_collected' => '4.50', 'packagingfee' => '0.00', 'shippingcost' => '8',
        'payable_amount' => '62.50', 'paymethod' => 'Stripe Pay', 'discount' => '0', 'couponid' => '', 'discounttext' => '',
        'deliverynote' => '', 'shipmethod' => 2, 'if_items_unavailabel' => 1, 'token' => '',
    ]]]);
}

it('quotes the SG cart total the app displays', function () {
    $response = quote($this)->assertOk();

    expect($response->json())->toMatchArray([
        'response' => 'success',
        'message' => 'Shipping & Packaing Price',
        'gstText' => 'GST (9.00%)',
        'gst' => 4.5,
        'packagingprice' => '0.00',
        'shippingprice' => 8,
        'grandtotal' => '62.50',
        'subtotal' => 50,
        'discount' => '0.00',
        'is_discount_invalid' => false,
    ]);
});

it('quotes a percentage coupon on the cart total', function () {
    Fixtures::insert('coupon_code', ['coupon_code' => 'SAVE10', 'discount_type' => 1, 'discount' => 10, 'status' => 1]);

    $response = quote($this, ['couponcode' => 'SAVE10']);

    // (50 + 4.50 + 8) * 10% = 6.25
    expect($response->json('discount'))->toBe('6.25')
        ->and($response->json('grandtotal'))->toBe('56.25')
        ->and($response->json('discount_text'))->toStartWith('Coupon discount(');
});

it('flags an unknown coupon in the quote', function () {
    expect(quote($this, ['couponcode' => 'NOPE'])->json())->toMatchArray([
        'is_discount_invalid' => true, 'discount_error_text' => 'Invalid coupon code', 'grandtotal' => '62.50',
    ]);
});

it('creates a pending mobile order and returns the success/cancel URLs', function () {
    $response = mobileOrder($this)->assertOk();
    $orderId = $response->json('orderid');

    expect($response->json())->toMatchArray([
        'response' => 'success', 'message' => 'Order Created', 'paymenturl' => '', 'apikey' => '',
        'successurl' => url('/') . '/successorder?orderid=' . $orderId . '&transactionid=',
        'cancelurl' => url('/') . '/cancelorder?orderid=' . $orderId . '&transactionid=',
    ]);

    $order = DB::table('order_master')->where('order_id', $orderId)->first();
    expect((float) $order->payable_amount)->toEqual(62.50)
        ->and((int) $order->order_status)->toBe(0)
        ->and((int) $order->user_id)->toBe($this->customer)
        ->and(DB::table('order_details')->where('order_id', $orderId)->count())->toBe(1);
});

it('lowers stock when the mobile order is created', function () {
    mobileOrder($this);

    expect((int) DB::table('products')->where('Id', $this->drill)->value('Quantity'))->toBe(9);
});

// Server-side totals (Phase 3)

it('refuses an order whose total does not match the server quote', function () {
    $response = mobileOrder($this, ['payable_amount' => '1.00'])->assertOk();

    expect($response->json())->toBe(['response' => 'failed', 'message' => 'Your cart has changed. Please review your cart and try again.'])
        ->and(DB::table('order_master')->count())->toBe(0)
        ->and((int) DB::table('products')->where('Id', $this->drill)->value('Quantity'))->toBe(10);
});

it('accepts a mismatched total in log mode (old behaviour) but logs it', function () {
    config(['services.mobile_pricing.mode' => 'log']);
    \Illuminate\Support\Facades\Log::spy();

    $orderId = mobileOrder($this, ['payable_amount' => '1.00'])->json('orderid');

    expect((float) DB::table('order_master')->where('order_id', $orderId)->value('payable_amount'))->toEqual(1.0);
    \Illuminate\Support\Facades\Log::shouldHaveReceived('warning')->withArgs(fn ($m) => $m === 'MOBILE ORDER TOTAL MISMATCH');
});

it('stores the server total, so formatting differences in the app total do not matter', function () {
    $orderId = mobileOrder($this, ['payable_amount' => '62.5'])->json('orderid');

    expect(DB::table('order_master')->where('order_id', $orderId)->value('payable_amount'))->toEqual('62.50');
});

it('prices a guest order from the database, not from the prices the app sent', function () {
    DB::table('cart_details')->delete();

    $tampered = mobileOrder($this, ['customer_id' => 0, 'products' => [['id' => $this->drill, 'name' => 'Drill', 'quantity' => 1,
        'price' => 1.00, 'total' => 1.00, 'option' => '', 'weight' => 2, 'code' => 'DR-1']], 'payable_amount' => '9.00']);
    expect($tampered->json('response'))->toBe('failed');

    $honest = mobileOrder($this, ['customer_id' => 0]);
    expect($honest->json('response'))->toBe('success');
});

it('creates a guest customer with the billing country (guest checkout used to crash)', function () {
    DB::table('cart_details')->delete();
    $billing = ['bill_compname' => '', 'bill_fname' => 'New', 'bill_lname' => 'Guest', 'bill_email' => 'guest@example.test',
        'bill_mobile' => '8', 'bill_address1' => 'A', 'bill_address2' => '', 'bill_city' => 'S', 'bill_state' => '',
        'bill_zip' => '1', 'bill_country' => 'SG'];

    mobileOrder($this, ['customer_id' => 0, 'billing' => [$billing]])->assertOk()->assertJson(['response' => 'success']);

    $guest = DB::table('customer_master')->where('cust_email', 'guest@example.test')->first();
    expect($guest)->not->toBeNull()
        ->and((int) $guest->cust_country)->toBe(189)
        ->and((int) DB::table('order_master')->value('user_id'))->toBe((int) $guest->cust_id);
});

it('replaces the order lines when the app retries with existorderid', function () {
    $orderId = mobileOrder($this)->json('orderid');

    mobileOrder($this, ['existorderid' => $orderId])->assertJson(['response' => 'success']);

    expect(DB::table('order_details')->where('order_id', $orderId)->count())->toBe(1)
        ->and((int) DB::table('products')->where('Id', $this->drill)->value('Quantity'))->toBe(9);
});

it('charges the order\'s stored total in the iOS payment intent, not the amount sent', function () {
    $orderId = mobileOrder($this)->json('orderid');
    $sent = [];
    \Stripe\ApiRequestor::setHttpClient(new class($sent) implements \Stripe\HttpClient\ClientInterface {
        public function __construct(private array &$sent) {}
        public function request($method, $absUrl, $headers, $params, $hasFile, $apiMode = 'v1', $maxNetworkRetries = null)
        {
            $this->sent = $params;
            return ['{"id":"pi_test","object":"payment_intent","client_secret":"pi_test_secret"}', 200, []];
        }
    });

    $this->withoutExceptionHandling();
    $this->postJson('/api/ios-create-payment-intend', ['order_id' => $orderId, 'orderinfo' => [['amount' => '1.00']]])
        ->assertOk()->assertJson(['status' => true, 'clientSecret' => 'pi_test_secret']);

    expect($sent['amount'])->toBe(6250);
    \Stripe\ApiRequestor::setHttpClient(null);
});

it('answers a Stripe error with the JSON the app expects, not a 500', function () {
    \Stripe\ApiRequestor::setHttpClient(new class implements \Stripe\HttpClient\ClientInterface {
        public function request($method, $absUrl, $headers, $params, $hasFile)
        {
            return ['{"error":{"message":"Your card was declined.","type":"card_error"}}', 402, []];
        }
    });

    $this->postJson('/api/ios-create-payment-intend', ['orderinfo' => [['amount' => '10.00']]])
        ->assertOk()
        ->assertJson(['status' => false, 'message' => 'Your card was declined.']);

    \Stripe\ApiRequestor::setHttpClient(null);
});
