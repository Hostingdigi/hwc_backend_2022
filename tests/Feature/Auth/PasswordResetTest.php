<?php

use App\Mail\TemplateMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\Support\Fixtures;

// Customer password reset (web + mobile). The old flow let anyone set any
// customer's password: the link was base64(email) and the form posted the
// customer id with no token.

beforeEach(function () {
    Mail::fake();
    Fixtures::insert('settings', ['id' => 1, 'company_name' => 'HardwareCity', 'admin_email' => 'admin@example.test']);
    Fixtures::insert('emailtemplate', ['template_type' => 6, 'status' => 1, 'subject' => 'Reset your password', 'content' => 'Click {resetpasswordlink}']);
    $this->customer = Fixtures::insert('customer_master', ['cust_email' => 'buyer@example.test', 'cust_username' => 'buyer',
        'cust_firstname' => 'Tan', 'cust_password' => Hash::make('old-password'), 'cust_status' => 1, 'password_reset_count' => 0]);
});

function resetLinkFromMail()
{
    $link = null;
    Mail::assertQueued(TemplateMail::class, function ($mail) use (&$link) {
        preg_match('#href="([^"]+)"#', $mail->body, $m);
        $link = html_entity_decode($m[1] ?? '');
        return true;
    });
    return $link;
}

function passwordOf($id)
{
    return DB::table('customer_master')->where('cust_id', $id)->value('cust_password');
}

it('can no longer reset a password by posting a customer id', function () {
    // The exact request that used to work for any account
    $this->post('/updateresetpassword', ['id' => $this->customer, 'new_password' => 'hacked123', 'confirm_password' => 'hacked123'])
        ->assertRedirect();

    expect(Hash::check('old-password', passwordOf($this->customer)))->toBeTrue();
});

it('no longer accepts the old base64(email) link', function () {
    $this->post('/updateresetpassword', ['token' => base64_encode('buyer@example.test'), 'email' => 'buyer@example.test',
        'new_password' => 'hacked123', 'confirm_password' => 'hacked123']);

    expect(Hash::check('old-password', passwordOf($this->customer)))->toBeTrue();
});

it('emails a tokenised link and resets the password with it, once', function () {
    $this->post('/sendforgotemail', ['cust_email' => 'buyer@example.test'])->assertRedirect('/forgotpassword');

    $link = resetLinkFromMail();
    expect($link)->toContain('/resetpassword/')->and($link)->toContain('email=buyer%40example.test');

    $parts = parse_url($link);
    parse_str($parts['query'], $query);
    $token = basename($parts['path']);

    $this->get($parts['path'] . '?' . $parts['query'])->assertOk()->assertSee($token, false);

    $this->post('/updateresetpassword', ['token' => $token, 'email' => $query['email'], 'new_password' => 'new-password', 'confirm_password' => 'new-password'])
        ->assertRedirect('/login');
    expect(Hash::check('new-password', passwordOf($this->customer)))->toBeTrue();

    // Single use
    $this->post('/updateresetpassword', ['token' => $token, 'email' => $query['email'], 'new_password' => 'third-pass', 'confirm_password' => 'third-pass']);
    expect(Hash::check('new-password', passwordOf($this->customer)))->toBeTrue();
});

it('rejects a valid token used for another customer\'s email', function () {
    Fixtures::insert('customer_master', ['cust_email' => 'victim@example.test', 'cust_password' => Hash::make('victim-pass'), 'cust_status' => 1]);
    $this->post('/sendforgotemail', ['cust_email' => 'buyer@example.test']);
    $token = basename(parse_url(resetLinkFromMail(), PHP_URL_PATH));

    $this->post('/updateresetpassword', ['token' => $token, 'email' => 'victim@example.test', 'new_password' => 'hacked123', 'confirm_password' => 'hacked123']);

    expect(Hash::check('victim-pass', DB::table('customer_master')->where('cust_email', 'victim@example.test')->value('cust_password')))->toBeTrue();
});

it('sends the same tokenised link from the mobile forgot-password endpoint', function () {
    $this->postJson('/api/customerforgotpassword', ['email' => 'buyer@example.test'])
        ->assertJson(['response' => 'success', 'message' => 'Reset password Link sent']);

    expect(resetLinkFromMail())->toMatch('#/resetpassword/[A-Za-z0-9]{40,}\?email=#');
});
