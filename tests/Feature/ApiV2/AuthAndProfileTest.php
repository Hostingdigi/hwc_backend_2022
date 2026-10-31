<?php

use App\Mail\TemplateMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\Feature\ApiV2\ApiV2Fixtures;
use Tests\Support\Fixtures;

uses(ApiV2Fixtures::class);

beforeEach(fn () => $this->seedStore());

it('registers a customer and returns a working token', function () {
    $response = $this->postJson('/api/v2/auth/register', [
        'first_name' => 'New', 'last_name' => 'Buyer', 'email' => 'new@example.test',
        'password' => 'long-password', 'password_confirmation' => 'long-password', 'device_name' => 'iPhone',
    ])->assertCreated()->assertJsonPath('customer.email', 'new@example.test');

    $this->withToken($response->json('token'))->getJson('/api/v2/me')
        ->assertOk()->assertJsonPath('data.first_name', 'New');
});

it('refuses to register an email that already has an account', function () {
    $this->makeCustomer(['cust_email' => 'taken@example.test']);

    $this->postJson('/api/v2/auth/register', ['first_name' => 'A', 'last_name' => 'B', 'email' => 'taken@example.test',
        'password' => 'long-password', 'password_confirmation' => 'long-password', 'device_name' => 'x'])
        ->assertStatus(422)->assertJsonValidationErrors('email');
});

it('logs in by email or username and rejects wrong or inactive accounts', function () {
    $this->makeCustomer(['cust_email' => 'buyer@example.test', 'cust_username' => 'buyer']);
    $this->makeCustomer(['cust_email' => 'gone@example.test', 'cust_status' => 0]);

    $this->postJson('/api/v2/auth/login', ['login' => 'buyer@example.test', 'password' => 'right-password', 'device_name' => 'x'])->assertOk()->assertJsonStructure(['token']);
    $this->postJson('/api/v2/auth/login', ['login' => 'buyer', 'password' => 'right-password', 'device_name' => 'x'])->assertOk();
    $this->postJson('/api/v2/auth/login', ['login' => 'buyer', 'password' => 'wrong', 'device_name' => 'x'])->assertStatus(422);
    $this->postJson('/api/v2/auth/login', ['login' => 'gone@example.test', 'password' => 'right-password', 'device_name' => 'x'])->assertStatus(422);
});

it('rate-limits sign-in attempts', function () {
    foreach (range(1, 10) as $i) {
        $this->postJson('/api/v2/auth/login', ['login' => 'x@example.test', 'password' => 'nope', 'device_name' => 'x']);
    }

    $this->postJson('/api/v2/auth/login', ['login' => 'x@example.test', 'password' => 'nope', 'device_name' => 'x'])->assertStatus(429);
});

it('requires a token for account endpoints', function ($method, $uri) {
    $this->json($method, $uri)->assertUnauthorized();
})->with([['GET', '/api/v2/me'], ['GET', '/api/v2/cart'], ['GET', '/api/v2/orders'], ['POST', '/api/v2/orders'], ['GET', '/api/v2/wishlist']]);

it('revokes the token on logout', function () {
    $customer = $this->makeCustomer();
    $token = $customer->createToken('phone')->plainTextToken;

    $this->withToken($token)->postJson('/api/v2/auth/logout')->assertOk();

    expect($customer->tokens()->count())->toBe(0);
});

it('updates the profile', function () {
    $this->signIn();

    $this->patchJson('/api/v2/me', ['first_name' => 'Changed', 'country' => 'SG'])
        ->assertOk()->assertJsonPath('data.first_name', 'Changed')->assertJsonPath('data.address.country_id', 189);
});

it('changes the password and signs out other devices', function () {
    $customer = $this->makeCustomer();
    $customer->createToken('tablet');
    $this->withToken($customer->createToken('phone')->plainTextToken);

    $this->putJson('/api/v2/me/password', ['current_password' => 'wrong', 'password' => 'new-long-pass', 'password_confirmation' => 'new-long-pass'])->assertStatus(422);
    $this->putJson('/api/v2/me/password', ['current_password' => 'right-password', 'password' => 'new-long-pass', 'password_confirmation' => 'new-long-pass'])->assertOk();

    expect(Hash::check('new-long-pass', $customer->fresh()->cust_password))->toBeTrue()
        ->and($customer->tokens()->pluck('name')->all())->toBe(['phone']);
});

it('deactivates the account with the emailed code and revokes all tokens', function () {
    Mail::fake();
    $customer = $this->signIn();

    $this->postJson('/api/v2/me/deactivation')->assertOk();
    $otp = DB::table('account_deactivate_history')->where('user_id', $customer->cust_id)->value('otp_number');

    $this->postJson('/api/v2/me/deactivation/confirm', ['otp' => '000000' === $otp ? '111111' : '000000'])->assertStatus(422);
    $this->postJson('/api/v2/me/deactivation/confirm', ['otp' => $otp])->assertOk();

    expect((int) $customer->fresh()->cust_status)->toBe(0)
        ->and($customer->tokens()->count())->toBe(0);
    Mail::assertQueued(TemplateMail::class, fn ($m) => $m->subjectLine === 'Account Deactivated');
});

it('answers forgot-password the same way whether or not the account exists', function () {
    Mail::fake();
    Fixtures::insert('emailtemplate', ['template_type' => 6, 'status' => 1, 'subject' => 'Reset', 'content' => '{resetpasswordlink}']);
    $this->makeCustomer(['cust_email' => 'buyer@example.test']);

    $a = $this->postJson('/api/v2/auth/forgot-password', ['email' => 'buyer@example.test'])->assertOk()->json('message');
    $b = $this->postJson('/api/v2/auth/forgot-password', ['email' => 'nobody@example.test'])->assertOk()->json('message');

    expect($a)->toBe($b);
    Mail::assertQueuedCount(1);
});
