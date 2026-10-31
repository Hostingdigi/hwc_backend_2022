<?php

namespace Tests\Feature\ApiV2;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\Support\Fixtures;

/**
 * Shared store data for the API v2 tests: Singapore with 9% GST, one local
 * shipping method, card + PayPal + Atome payment methods, a S$54.50 drill.
 */
trait ApiV2Fixtures
{
    protected function seedStore(): void
    {
        Fixtures::insert('country', ['countryid' => 189, 'countryname' => 'Singapore', 'countrycode' => 'SG', 'taxtitle' => 'GST', 'taxpercentage' => 9, 'country_status' => 1, 'phonecode' => '65']);
        Fixtures::insert('paymentsettings', ['id' => 1, 'currency_type' => 'SGD', 'fuelcharge_percentage' => 20, 'min_package_fee' => 5,
            'free_shipping_amount' => 100, 'discount_percentage' => 0, 'promo_discount_percentage' => 0]);
        Fixtures::insert('settings', ['id' => 1, 'company_name' => 'HardwareCity', 'admin_email' => 'admin@example.test']);
        Fixtures::insert('local_shipping_methods', ['Status' => 1, 'DisplayOrder' => 1, 'FreeShipCost' => 100, 'PriceRange5' => 8, 'PriceRange15' => 12]);
        Fixtures::insert('shipping_methods', ['Id' => 2, 'EnName' => 'Courier', 'shipping_type' => 1, 'Status' => 1]);
        Fixtures::insert('paymentmethods', ['Id' => 3, 'payment_name' => 'Credit Card', 'type' => 1, 'status' => 1, 'payment_mode' => 'test',
            'test_api_key' => 'pk_test_public', 'test_api_signature' => 'sk_test_secret']);
        Fixtures::insert('paymentmethods', ['Id' => 6, 'payment_name' => 'Paypal', 'type' => 1, 'status' => 1, 'payment_mode' => 'test',
            'test_api_key' => 'pp-client', 'test_api_signature' => 'pp-secret']);
        Fixtures::insert('paymentmethods', ['Id' => 7, 'payment_name' => 'Atome', 'type' => 1, 'status' => 1, 'payment_mode' => 'test',
            'test_api_key' => 'at-key', 'test_api_signature' => 'at-secret', 'testing_url' => 'https://atome.test/v2']);
        Fixtures::insert('types', ['TypeId' => 10, 'EnName' => 'Tools', 'UniqueKey' => 'tools', 'ParentLevel' => 0, 'TypeStatus' => 1, 'DisplayOrder' => 1]);
        Fixtures::insert('types', ['TypeId' => 11, 'EnName' => 'Power Tools', 'UniqueKey' => 'power-tools', 'ParentLevel' => 10, 'TypeStatus' => 1, 'DisplayOrder' => 1]);
        Fixtures::insert('brands', ['BrandId' => 7, 'EnName' => 'Bosch', 'UniqueKey' => 'bosch', 'BrandStatus' => 1, 'PopularBrand' => 1]);
        Fixtures::insert('emailtemplate', ['template_type' => 2, 'status' => 1, 'subject' => 'Order {orderid} confirmed', 'content' => 'Hi {customername} {orderdetails}']);

        $this->drill = Fixtures::insert('products', ['EnName' => 'Bosch Drill', 'UniqueKey' => 'bosch-drill', 'Code' => 'DR-1', 'Price' => 54.50,
            'StandardPrice' => 54.50, 'Weight' => 2, 'ShippingBox' => '', 'Quantity' => 10, 'ProdStatus' => 1, 'Types' => 11, 'Brand' => 7]);
    }

    protected function makeCustomer(array $overrides = []): Customer
    {
        return Customer::find(Fixtures::insert('customer_master', $overrides + [
            'cust_firstname' => 'Tan', 'cust_lastname' => 'Ah Kow', 'cust_email' => 'buyer' . uniqid() . '@example.test',
            'cust_password' => Hash::make('right-password'), 'cust_status' => 1, 'group_admin' => 0, 'password_reset_count' => 0,
        ]));
    }

    protected function signIn(?Customer $customer = null): Customer
    {
        $customer ??= $this->makeCustomer();
        $this->withToken($customer->createToken('test')->plainTextToken);

        return $customer;
    }

    protected function putInCart(Customer $customer, int $productId, int $qty = 1): void
    {
        DB::table('cart_details')->insert(Fixtures::fill('cart_details', [
            'user_id' => $customer->cust_id, 'prod_id' => $productId, 'prod_name' => 'x', 'prod_quantity' => $qty,
            'prod_unit_price' => 0, 'prod_option_id' => 0,
        ]));
    }

    protected function orderPayload(array $overrides = []): array
    {
        return $overrides + [
            'shipping' => ['first_name' => 'Tan', 'last_name' => 'Ah Kow', 'phone' => '81234567', 'address1' => '1 Test Road',
                'postcode' => '123456', 'country' => 'SG'],
            'shipping_method_id' => 2,
            'payment_method_id' => 3,
        ];
    }
}
