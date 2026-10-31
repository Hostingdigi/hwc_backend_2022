<?php

use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\Support\Fixtures;

// Every /admin route except the login form, login post and logout requires an
// admin logged in on the "admin" guard (EnsureAdminIsLoggedIn).

function superAdmin()
{
    return Admin::find(Fixtures::insert('admins', [
        'name' => 'Boss', 'username' => 'boss-' . uniqid(), 'designation' => 'Owner', 'password' => Hash::make('x'), 'priority' => 0,
    ]));
}

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

it('no longer accepts GET for state-changing admin actions', function () {
    $this->actingAs(superAdmin(), 'admin');

    $this->get('/admin/country/999/destroy')->assertStatus(405);
    $this->get('/admin/country/999/1/updatestatus')->assertStatus(405);

    $country = DB::table('country')->where('countryid', 999)->first();
    expect($country)->not->toBeNull()
        ->and((int) $country->country_status)->toBe(1);
});

it('blocks anonymous state-changing requests', function () {
    $this->post('/admin/country/999/destroy')->assertRedirect('/admin/');
    $this->post('/admin/country/999/1/updatestatus')->assertRedirect('/admin/');

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
    $this->actingAs(superAdmin(), 'admin')
        ->post('/admin/country/999/1/updatestatus')
        ->assertRedirect();

    expect((int) DB::table('country')->where('countryid', 999)->value('country_status'))->toBe(0);
});

it('no longer accepts a hand-set admin_id session key as a login', function () {
    $this->withSession(['admin_id' => 1, 'priority' => 0])
        ->get('/admin/orders')
        ->assertRedirect('/admin/');
});

it('keeps the login form and login post public', function () {
    $this->get('/admin')->assertOk();

    Fixtures::insert('admins', ['name' => 'Admin', 'username' => 'boss', 'designation' => 'Owner', 'password' => Hash::make('secret-pass'), 'priority' => 0]);

    $this->post('/admin/verifylogin', ['username' => 'boss', 'password' => 'secret-pass'])
        ->assertRedirect('/admin/dashboard')
        ->assertSessionHas('admin_id');

    $this->assertAuthenticated('admin');
});

it('gives the session a new id on admin login (no session fixation)', function () {
    Fixtures::insert('admins', ['name' => 'Admin', 'username' => 'boss', 'designation' => 'Owner', 'password' => Hash::make('secret-pass'), 'priority' => 0]);
    $this->startSession();
    $before = session()->getId();

    $this->post('/admin/verifylogin', ['username' => 'boss', 'password' => 'secret-pass']);

    expect(session()->getId())->not->toBe($before);
});

it('logs the admin out of the guard and clears the role keys', function () {
    $this->actingAs(superAdmin(), 'admin')
        ->withSession(['admin_id' => 1, 'priority' => 0, 'accessrights' => ['21']])
        ->get('/admin/logout')
        ->assertRedirect('/admin/')
        ->assertSessionMissing('accessrights');

    $this->assertGuest('admin');
});

it('rejects a wrong admin password', function () {
    Fixtures::insert('admins', ['name' => 'Admin', 'username' => 'boss', 'designation' => 'Owner', 'password' => Hash::make('secret-pass'), 'priority' => 0]);

    $this->from('/admin')->post('/admin/verifylogin', ['username' => 'boss', 'password' => 'wrong'])
        ->assertRedirect('/admin')
        ->assertSessionMissing('admin_id');
});
