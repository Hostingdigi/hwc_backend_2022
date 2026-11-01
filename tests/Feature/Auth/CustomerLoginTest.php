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

it('logs out of the guard and clears the session keys', function () {
    $this->post('/logincheck', ['username' => 'buyer', 'password' => 'right-password']);

    $this->get('/logout')->assertRedirect('/');

    $this->assertGuest('web');
});
