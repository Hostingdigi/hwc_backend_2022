<?php

use Illuminate\Support\Facades\DB;

// Characterization tests for the cXML punchout entry point (PunchoutController).
// punchout_sessions is a reconstructed table (status defaults to 1 here).

function punchoutSetupRequest($buyerCookie)
{
    return '<?xml version="1.0" encoding="UTF-8"?>'
        . '<cXML payloadID="test-123@buyer.example" timestamp="2026-10-31T10:00:00+08:00">'
        . '<Header><From><Credential domain="DUNS"><Identity>buyer</Identity></Credential></From>'
        . '<To><Credential domain="DUNS"><Identity>hwc</Identity></Credential></To>'
        . '<Sender><Credential domain="DUNS"><Identity>buyer</Identity><SharedSecret>anything</SharedSecret></Credential>'
        . '<UserAgent>Test</UserAgent></Sender></Header>'
        . '<Request deploymentMode="test"><PunchOutSetupRequest operation="create">'
        . '<BuyerCookie>' . $buyerCookie . '</BuyerCookie>'
        . '<BrowserFormPost><URL>https://buyer.example/return</URL></BrowserFormPost>'
        . '<SupplierSetup><URL>https://hwc.example/punchout/setup</URL></SupplierSetup>'
        . '</PunchOutSetupRequest></Request></cXML>';
}

function postPunchoutSetup($test, $buyerCookie)
{
    return $test->call('POST', '/punchout/setup', [], [], [], ['CONTENT_TYPE' => 'text/xml'], punchoutSetupRequest($buyerCookie));
}

it('answers a setup request with a start-page URL', function () {
    $response = postPunchoutSetup($this, 'cookie-1')->assertOk();

    expect($response->getContent())->toContain('<Status code="200" text="OK"/>')
        ->and($response->getContent())->toContain('/punchout/login?buyer=');
});

it('KNOWN BUG: drops the cXML body because "cxml" is not in PunchoutSession::$fillable', function () {
    postPunchoutSetup($this, 'cookie-2');

    $session = DB::table('punchout_sessions')->where('buyer_cookie', 'cookie-2')->first();
    expect($session)->not->toBeNull()
        ->and($session->payload_id)->toBe('test-123@buyer.example')
        ->and($session->cxml)->toBeNull(); // checkout later parses this and fails
});

it('SECURITY GAP: anyone can open a punchout session (and its 0% tax pricing) without a shared secret', function () {
    $response = postPunchoutSetup($this, 'attacker-cookie');
    preg_match('#<URL>(.*?)</URL>#', $response->getContent(), $match);
    $loginUrl = html_entity_decode($match[1]);

    $this->get($loginUrl)
        ->assertRedirect('/')
        ->assertSessionHas('punchout', true);
});
