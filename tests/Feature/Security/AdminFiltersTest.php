<?php

use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\Support\Fixtures;

// Admin list filters quote or cast every request value (they used to splice
// them straight into SQL).

beforeEach(function () {
    $this->actingAs(Admin::find(Fixtures::insert('admins', [
        'name' => 'Boss', 'username' => 'boss', 'designation' => 'Owner', 'password' => Hash::make('x'), 'priority' => 0,
    ])), 'admin')->withSession(['admin_id' => 1, 'priority' => 0]);
});

function filterQueries(callable $request)
{
    DB::enableQueryLog();
    $request();
    return collect(DB::getQueryLog())->pluck('query')->implode("\n");
}

it('quotes text filters in the order list', function () {
    $sql = filterQueries(fn () => $this->get('/admin/orders?filter_srch_val=' . urlencode('x" OR "1"="1') . '&filter_column=1&Nametype=1'));

    expect($sql)->not->toContain('"x" OR "1"="1"');
});

it('casts numeric product filters to integers', function () {
    $sql = filterQueries(fn () => $this->get('/admin/products?status=' . urlencode('1 OR 1=1') . '&Types=' . urlencode('5) OR (1=1')));

    expect($sql)->toContain('ProdStatus = 1')
        ->and($sql)->not->toContain('OR 1=1')
        ->and($sql)->not->toContain('(1=1');
});

it('only lets the customer filter search the columns the form offers', function () {
    $sql = filterQueries(fn () => $this->get('/admin/customer?filter_srch_val=a&Nametype=1&filter_column=' . urlencode('1=1 OR cust_password')));

    expect($sql)->not->toContain('cust_password')
        ->and($sql)->toContain('cust_email = ');
});

it('searches products by a barcode with no matching options without an SQL error', function () {
    $sql = filterQueries(fn () => $this->get('/admin/products?barcode=NOPE'));

    expect($sql)->toContain('Id in(0)');
});
