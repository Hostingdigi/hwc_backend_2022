<?php

use Illuminate\Support\Facades\Hash;
use Tests\Support\Fixtures;

// Storefront login (CustomerController@logincheck) logs in on the "web" guard;
// the old customer_id session key is no longer written.

beforeEach(function () {
    $this->customer = Fixtures::insert('customer_master', [
        'cust_username' => 'buyer', 'cust_email' => 'buyer@example.test', 'cust_firstname' => 'Tan', 'cust_lastname' => 'Ah Kow',
        'cust_password' => Hash::make('right-password'), 'cust_status' => 1,
    ]);
});

it('logs a customer in on the web guard', function () {
    $this->post('/logincheck', ['username' => 'buyer@example.test', 'password' => 'right-password'])
        ->assertRedirect('/customer/myorders')
        ->assertSessionHas('customer_name', 'Tan Ah Kow');

    $this->assertAuthenticated('web');
    expect(auth('web')->id())->toBe($this->customer);
});

it('accepts the username as well as the email', function () {
    $this->post('/logincheck', ['username' => 'buyer', 'password' => 'right-password']);

    $this->assertAuthenticated('web');
    expect(auth('web')->id())->toBe($this->customer);
});

it('gives the session a new id on login (no session fixation)', function () {
    $this->startSession();
    $before = session()->getId();

    $this->post('/logincheck', ['username' => 'buyer', 'password' => 'right-password']);

    expect(session()->getId())->not->toBe($before);
});

it('rejects a wrong password', function () {
    $this->from('/login')->post('/logincheck', ['username' => 'buyer', 'password' => 'wrong'])
        ->assertRedirect('/login');

    $this->assertGuest('web');
});

it('requires a username and password', function () {
    $this->from('/login')->post('/logincheck', [])
        ->assertRedirect('/login')
        ->assertSessionHasErrors(['username', 'password']);

    $this->assertGuest('web');
});

it('locks the username out after too many failed attempts', function () {
    foreach (range(1, 5) as $i) {
        $this->post('/logincheck', ['username' => 'buyer', 'password' => 'wrong']);
    }

    $this->from('/login')->post('/logincheck', ['username' => 'buyer', 'password' => 'right-password'])
        ->assertSessionHasErrors('username');

    $this->assertGuest('web');
});

it('no longer accepts a GET login', function () {
    $this->get('/logincheck?username=buyer&password=right-password')->assertMethodNotAllowed();

    $this->assertGuest('web');
});

it('merges the guest cart into the customer cart without duplicate lines', function () {
    $other = Fixtures::insert('customer_master', [
        'cust_username' => 'other', 'cust_email' => 'other@example.test', 'cust_firstname' => 'O', 'cust_lastname' => 'T',
        'cust_password' => Hash::make('x'), 'cust_status' => 1,
    ]);
    $line = fn (array $attrs) => Fixtures::insert('cart_details', $attrs + ['prod_option_id' => 0, 'row_key' => 0]);

    $line(['user_id' => $this->customer, 'user_token' => '', 'prod_id' => 10, 'prod_quantity' => 2]);       // saved earlier
    $line(['user_id' => null, 'user_token' => 'guest-token', 'prod_id' => 10, 'prod_quantity' => 3]);       // same product
    $line(['user_id' => null, 'user_token' => 'guest-token', 'prod_id' => 10, 'prod_option_id' => 7, 'prod_quantity' => 1]); // other option
    $line(['user_id' => null, 'user_token' => 'guest-token', 'prod_id' => 20, 'prod_quantity' => 4]);       // new product
    $line(['user_id' => $other, 'user_token' => 'guest-token', 'prod_id' => 20, 'prod_quantity' => 9]);    // another customer's

    \App\Models\CartDetail::mergeGuestCart($this->customer, 'guest-token');

    $mine = \App\Models\CartDetail::forCustomer($this->customer)->orderBy('prod_id')->orderBy('prod_option_id')->get();
    expect($mine->map(fn ($l) => [$l->prod_id, $l->prod_option_id, $l->prod_quantity])->all())
        ->toBe([[10, 0, 5], [10, 7, 1], [20, 0, 4]]);

    expect(\App\Models\CartDetail::forCustomer($other)->sum('prod_quantity'))->toEqual(9);
});

it('logs out of the guard and clears the session keys', function () {
    $this->post('/logincheck', ['username' => 'buyer', 'password' => 'right-password']);

    $this->get('/logout')->assertRedirect('/');

    $this->assertGuest('web');
});
