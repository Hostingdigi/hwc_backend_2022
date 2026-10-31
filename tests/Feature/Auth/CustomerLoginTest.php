<?php

use Illuminate\Support\Facades\Hash;
use Tests\Support\Fixtures;

// Storefront login (CustomerController@logincheck) now logs in on the "web" guard
// as well as setting the legacy session keys.

beforeEach(function () {
    $this->customer = Fixtures::insert('customer_master', [
        'cust_username' => 'buyer', 'cust_email' => 'buyer@example.test', 'cust_firstname' => 'Tan', 'cust_lastname' => 'Ah Kow',
        'cust_password' => Hash::make('right-password'), 'cust_status' => 1,
    ]);
});

it('logs a customer in on the web guard and keeps the legacy session keys', function () {
    $this->post('/logincheck', ['username' => 'buyer@example.test', 'password' => 'right-password'])
        ->assertRedirect('/customer/myorders')
        ->assertSessionHas('customer_id', $this->customer)
        ->assertSessionHas('customer_name', 'Tan Ah Kow');

    $this->assertAuthenticated('web');
    expect(auth('web')->id())->toBe($this->customer);
});

it('accepts the username as well as the email', function () {
    $this->post('/logincheck', ['username' => 'buyer', 'password' => 'right-password'])
        ->assertSessionHas('customer_id', $this->customer);
});

it('gives the session a new id on login (no session fixation)', function () {
    $this->startSession();
    $before = session()->getId();

    $this->post('/logincheck', ['username' => 'buyer', 'password' => 'right-password']);

    expect(session()->getId())->not->toBe($before);
});

it('rejects a wrong password', function () {
    $this->from('/login')->post('/logincheck', ['username' => 'buyer', 'password' => 'wrong'])
        ->assertRedirect('/login')
        ->assertSessionMissing('customer_id');

    $this->assertGuest('web');
});

it('logs out of the guard and clears the session keys', function () {
    $this->post('/logincheck', ['username' => 'buyer', 'password' => 'right-password']);

    $this->get('/logout')->assertRedirect('/')->assertSessionMissing('customer_id');

    $this->assertGuest('web');
});
