<?php

use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\Support\Fixtures;

// Role rights are enforced on the server (AuthorizeAdminModule), not only by
// hiding admin menu links.

function adminWithRole(array $modules)
{
    $role = Fixtures::insert('role_rights', ['role_name' => 'Order desk', 'rights' => implode(',', $modules)]);

    return Admin::find(Fixtures::insert('admins', [
        'name' => 'Staff', 'username' => 'staff-' . uniqid(), 'designation' => 'Staff', 'password' => Hash::make('x'), 'priority' => $role,
    ]));
}

beforeEach(function () {
    Fixtures::insert('country', ['countryid' => 999, 'countryname' => 'Testland', 'countrycode' => 'TL', 'country_status' => 1]);
});

it('blocks a role from sections it has no right to', function ($uri) {
    $this->actingAs(adminWithRole([21]), 'admin')->get($uri)->assertForbidden();
})->with([
    '/admin/products',
    '/admin/customer',
    '/admin/manageadmin',
    '/admin/roleandrights',
    '/admin/payment_settings',
    '/admin/category/shipping-charges',
]);

it('blocks state changes in a forbidden section, before the controller runs', function () {
    $this->actingAs(adminWithRole([21]), 'admin')
        ->post('/admin/country/999/1/updatestatus')
        ->assertForbidden();

    expect((int) DB::table('country')->where('countryid', 999)->value('country_status'))->toBe(1);
});

it('lets a role into the sections it has', function () {
    $this->actingAs(adminWithRole([29]), 'admin')
        ->post('/admin/country/999/1/updatestatus')
        ->assertRedirect();

    expect((int) DB::table('country')->where('countryid', 999)->value('country_status'))->toBe(0);
});

it('lets every admin into pages the menu does not gate', function () {
    $this->actingAs(adminWithRole([21]), 'admin')
        ->withSession(['admin_id' => 1, 'priority' => 1, 'accessrights' => ['21']])
        ->get('/admin/logout')
        ->assertRedirect('/admin/');
});

it('gives super admins (priority 0) every section', function () {
    $admin = Admin::find(Fixtures::insert('admins', ['name' => 'Boss', 'username' => 'boss', 'designation' => 'Owner', 'password' => Hash::make('x'), 'priority' => 0]));

    expect($admin->canAccessModule(20))->toBeTrue()
        ->and($admin->canAccessModule(33))->toBeTrue();
});

it('answers forbidden AJAX calls with 403 JSON', function () {
    $this->actingAs(adminWithRole([20]), 'admin')
        ->postJson('/admin/fetch_otions_and_price', [])
        ->assertForbidden()
        ->assertJson(['message' => 'You do not have access to this section.']);
});

it('stores the role rights in the session at login, for the menu', function () {
    $admin = adminWithRole([21, 22]);
    DB::table('admins')->where('id', $admin->id)->update(['password' => Hash::make('secret-pass')]);

    $this->post('/admin/verifylogin', ['username' => $admin->username, 'password' => 'secret-pass'])
        ->assertSessionHas('accessrights', ['21', '22']);
});
