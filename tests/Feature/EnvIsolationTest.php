<?php

// Guards the test setup itself: tests must use .env.testing, never the real .env
// (which holds production credentials).

it('does not load the real .env', function () {
    expect(env('DB_DATABASE'))->toBe('hwc_backend_testing')
        ->and(env('PAYPAL_CLIENT_ID'))->toBeNull()
        ->and(env('GOOGLE_CAPTCHA_SECRET_KEY'))->toBeNull();
});
