<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\Support\Fixtures;

// Phase 0: every /admin route except the login form, login post and logout
// requires the admin session (EnsureAdminIsLoggedIn).

beforeEach(function () {
    $this->country = Fixtures::insert('country', ['countryid' => 999, 'countryname' => 'Testland', 'countrycode' => 'TL', 'country_status' => 1]);
});

it('redirects anonymous visitors from admin pages to the login form', function ($uri) {
    $this->get($uri)->assertRedirect('/admin/');
})->with([
    '/admin/dashboard',
    '/admin/orders',
    '/admin/products',
    '/admin/customer',
    '/admin/manageadmin',
    '/admin/payment_settings',
    '/admin/exportorders',
]);

it('blocks anonymous state-changing GET links', function () {
    $this->get('/admin/country/999/destroy')->assertRedirect('/admin/');
    $this->get('/admin/country/999/1/updatestatus')->assertRedirect('/admin/');

    $country = DB::table('country')->where('countryid', 999)->first();
    expect($country)->not->toBeNull()
        ->and((int) $country->country_status)->toBe(1);
});

it('blocks anonymous POSTs before the controller runs', function () {
    $this->post('/admin/country', ['countryname' => 'Injected', 'countrycode' => 'XX'])->assertRedirect('/admin/');

    expect(DB::table('country')->where('countryname', 'Injected')->exists())->toBeFalse();
});

it('answers anonymous AJAX calls with 401 JSON instead of a redirect', function () {
    $this->postJson('/admin/fetch_otions_and_price', [])->assertStatus(401)->assertJson(['message' => 'Unauthenticated.']);
});

it('lets a logged-in admin through', function () {
    $this->withSession(['admin_id' => 1, 'priority' => 0])
        ->get('/admin/country/999/1/updatestatus')
        ->assertRedirect();

    expect((int) DB::table('country')->where('countryid', 999)->value('country_status'))->toBe(0);
});

it('keeps the login form and login post public', function () {
    $this->get('/admin')->assertOk();

    Fixtures::insert('admins', ['name' => 'Admin', 'username' => 'boss', 'designation' => 'Owner', 'password' => Hash::make('secret-pass'), 'priority' => 0]);

    $this->post('/admin/verifylogin', ['username' => 'boss', 'password' => 'secret-pass'])
        ->assertRedirect('/admin/dashboard')
        ->assertSessionHas('admin_id');
});

it('rejects a wrong admin password', function () {
    Fixtures::insert('admins', ['name' => 'Admin', 'username' => 'boss', 'designation' => 'Owner', 'password' => Hash::make('secret-pass'), 'priority' => 0]);

    $this->from('/admin')->post('/admin/verifylogin', ['username' => 'boss', 'password' => 'wrong'])
        ->assertRedirect('/admin')
        ->assertSessionMissing('admin_id');
});
