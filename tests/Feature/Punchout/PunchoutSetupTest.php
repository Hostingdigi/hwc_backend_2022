<?php

use Illuminate\Support\Facades\DB;

// cXML punchout entry point (PunchoutController). The shared secret for tests is
// "test-shared-secret" (.env.testing). punchout_sessions is a reconstructed table.

function punchoutSetupRequest($buyerCookie, $sharedSecret = 'test-shared-secret')
{
    return '<?xml version="1.0" encoding="UTF-8"?>'
        . '<cXML payloadID="test-123@buyer.example" timestamp="2026-10-31T10:00:00+08:00">'
        . '<Header><From><Credential domain="DUNS"><Identity>buyer</Identity></Credential></From>'
        . '<To><Credential domain="DUNS"><Identity>hwc</Identity></Credential></To>'
        . '<Sender><Credential domain="DUNS"><Identity>buyer</Identity><SharedSecret>' . $sharedSecret . '</SharedSecret></Credential>'
        . '<UserAgent>Test</UserAgent></Sender></Header>'
        . '<Request deploymentMode="test"><PunchOutSetupRequest operation="create">'
        . '<BuyerCookie>' . $buyerCookie . '</BuyerCookie>'
        . '<BrowserFormPost><URL>https://buyer.example/return</URL></BrowserFormPost>'
        . '<SupplierSetup><URL>https://hwc.example/punchout/setup</URL></SupplierSetup>'
        . '</PunchOutSetupRequest></Request></cXML>';
}

function postPunchoutSetup($test, $buyerCookie, $sharedSecret = 'test-shared-secret')
{
    return $test->call('POST', '/punchout/setup', [], [], [], ['CONTENT_TYPE' => 'text/xml'], punchoutSetupRequest($buyerCookie, $sharedSecret));
}

it('answers a valid setup request with a start-page URL', function () {
    $response = postPunchoutSetup($this, 'cookie-1')->assertOk();

    expect($response->getContent())->toContain('<Status code="200" text="OK"/>')
        ->and($response->getContent())->toContain('/punchout/login?buyer=');
});

it('stores the cXML of the setup request (needed later at checkout)', function () {
    // Fixed in Phase 3: "cxml" was missing from PunchoutSession::$fillable
    postPunchoutSetup($this, 'cookie-2');

    $session = DB::table('punchout_sessions')->where('buyer_cookie', 'cookie-2')->first();
    expect($session->payload_id)->toBe('test-123@buyer.example')
        ->and($session->cxml)->toContain('<BuyerCookie>cookie-2</BuyerCookie>');
});

it('refuses a setup request with the wrong shared secret', function () {
    // Fixed in Phase 3: anyone could open a punchout session (and its 0% tax pricing)
    $response = postPunchoutSetup($this, 'attacker-cookie', 'guessed');

    expect($response->getContent())->toContain('<Status code="401" text="Unauthorized"/>')
        ->and($response->getContent())->not->toContain('/punchout/login')
        ->and(DB::table('punchout_sessions')->count())->toBe(0);
});

it('refuses every setup request while no shared secret is configured', function () {
    config(['services.punchout.shared_secret' => null]);

    expect(postPunchoutSetup($this, 'cookie-3', '')->getContent())->toContain('<Status code="401"');
});

it('answers malformed XML with a cXML 400 instead of crashing', function () {
    $response = $this->call('POST', '/punchout/setup', [], [], [], ['CONTENT_TYPE' => 'text/xml'], 'not xml');

    expect($response->getContent())->toContain('<Status code="400" text="Bad Request"/>');
});

it('opens a punchout session from a valid start-page link', function () {
    $response = postPunchoutSetup($this, 'buyer-cookie');
    preg_match('#<URL>(.*?)</URL>#', $response->getContent(), $match);

    $this->get(html_entity_decode($match[1]))
        ->assertRedirect('/')
        ->assertSessionHas('punchout', true);
});

it('refuses a tampered start-page link with 401, not a 500', function () {
    $this->get('/punchout/login?buyer=not-encrypted')->assertStatus(401);
});
