<?php

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Tests\Support\Fixtures;

// Phase 0: injectable raw SQL was replaced with bindings or sanitized values.

beforeEach(function () {
    Fixtures::insert('customer_master', [
        'cust_username' => 'buyer', 'cust_email' => 'buyer@example.test', 'cust_firstname' => 'Tan',
        'cust_password' => Hash::make('right-password'), 'cust_status' => 1,
    ]);
});

it('logs a mobile customer in with the right password', function () {
    $this->postJson('/api/customerlogin', ['username' => 'buyer', 'password' => 'right-password'])
        ->assertOk()->assertJson(['response' => 'success']);
});

it('treats SQL in the mobile login username as a literal string', function ($username) {
    $this->postJson('/api/customerlogin', ['username' => $username, 'password' => 'anything'])
        ->assertOk()->assertJson(['response' => 'failed', 'message' => 'Invalid Login Credentials']);
})->with([
    "' OR '1'='1",
    "buyer' -- ",
    "x') OR cust_status=1 OR ('",
]);

it('treats SQL in the brand filter as a literal prefix', function () {
    Fixtures::insert('brands', ['EnName' => 'Bosch', 'BrandStatus' => 1]);

    $this->getJson('/api/brandbyfilter/B')->assertOk();
    $this->getJson('/api/brandbyfilter/' . rawurlencode('" OR "1"="1'))->assertOk();
});

it('turns id-list input into integers only', function () {
    $controller = new class extends Controller {
        public function ids($value) { return $this->sqlIdList($value); }
    };

    expect($controller->ids('3,7,12'))->toBe('3,7,12')
        ->and($controller->ids(['5', '5', '9']))->toBe('5,9')
        ->and($controller->ids('1) OR (1=1'))->toBe('1')
        ->and($controller->ids('0) UNION SELECT password FROM admins -- '))->toBe('0')
        ->and($controller->ids(''))->toBe('0');
});

it('quotes LIKE search terms', function () {
    $controller = new class extends Controller {
        public function like($value) { return $this->sqlLike($value); }
    };

    expect($controller->like('drill'))->toBe("'%drill%'")
        ->and($controller->like("x' OR '1'='1"))->toBe("'%x\\' OR \\'1\\'=\\'1%'");
});

it('no longer routes /updateLatestProducts to a controller', function () {
    $routes = collect(app('router')->getRoutes())->map->uri();

    expect($routes->contains(fn ($uri) => str_contains($uri, 'updateLatestProducts')))->toBeFalse();
});
