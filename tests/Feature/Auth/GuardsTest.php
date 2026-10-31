<?php

use App\Models\Admin;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\Support\Fixtures;

// Phase 2: the auth guards point at the real tables (config/auth.php). The legacy
// login code does not use them yet; these tests prove they are ready for Phase 3.

it('authenticates a customer on the web guard by email and cust_password', function () {
    $id = Fixtures::insert('customer_master', ['cust_email' => 'buyer@example.test', 'cust_password' => Hash::make('right-password'), 'cust_status' => 1]);

    expect(Auth::guard('web')->attempt(['cust_email' => 'buyer@example.test', 'password' => 'wrong']))->toBeFalse()
        ->and(Auth::guard('web')->attempt(['cust_email' => 'buyer@example.test', 'password' => 'right-password']))->toBeTrue()
        ->and(Auth::guard('web')->user())->toBeInstanceOf(Customer::class)
        ->and(Auth::guard('web')->id())->toBe($id);
});

it('rehashes an old-cost customer password into cust_password on login', function () {
    // Stored with cost 10; the test environment uses BCRYPT_ROUNDS=4, so Laravel rehashes on login
    $old = password_hash('right-password', PASSWORD_BCRYPT, ['cost' => 10]);
    Fixtures::insert('customer_master', ['cust_email' => 'buyer@example.test', 'cust_password' => $old, 'cust_status' => 1]);

    Auth::guard('web')->attempt(['cust_email' => 'buyer@example.test', 'password' => 'right-password']);

    $stored = DB::table('customer_master')->where('cust_email', 'buyer@example.test')->value('cust_password');
    expect($stored)->not->toBe($old)
        ->and(Hash::check('right-password', $stored))->toBeTrue();
});

it('returns false instead of throwing for a non-bcrypt stored hash', function () {
    Fixtures::insert('customer_master', ['cust_email' => 'old@example.test', 'cust_password' => md5('right-password'), 'cust_status' => 1]);

    expect(Auth::guard('web')->attempt(['cust_email' => 'old@example.test', 'password' => 'right-password']))->toBeFalse();
});

it('never exposes cust_password when a customer is serialized', function () {
    Fixtures::insert('customer_master', ['cust_email' => 'buyer@example.test', 'cust_password' => Hash::make('x'), 'cust_status' => 1]);

    expect(Customer::first()->toArray())->not->toHaveKey('cust_password');
});

it('authenticates an admin on the admin guard by username and password', function () {
    Fixtures::insert('admins', ['name' => 'Boss', 'username' => 'boss', 'designation' => 'Owner', 'password' => Hash::make('secret-pass'), 'priority' => 0]);

    expect(Auth::guard('admin')->attempt(['username' => 'boss', 'password' => 'nope']))->toBeFalse()
        ->and(Auth::guard('admin')->attempt(['username' => 'boss', 'password' => 'secret-pass']))->toBeTrue()
        ->and(Auth::guard('admin')->user())->toBeInstanceOf(Admin::class);
});

it('keeps the customer and admin guards separate', function () {
    Fixtures::insert('admins', ['name' => 'Boss', 'username' => 'boss', 'designation' => 'Owner', 'password' => Hash::make('secret-pass'), 'priority' => 0]);

    Auth::guard('admin')->attempt(['username' => 'boss', 'password' => 'secret-pass']);

    expect(Auth::guard('admin')->check())->toBeTrue()
        ->and(Auth::guard('web')->check())->toBeFalse();
});
