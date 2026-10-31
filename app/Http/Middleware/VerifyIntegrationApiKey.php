<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyIntegrationApiKey
{
    /**
     * Protects the catalogue/order sync endpoints (ApiController).
     * The caller sends the key from INTEGRATION_API_KEY as "X-Api-Key: <key>"
     * or "Authorization: Bearer <key>".
     */
    public function handle(Request $request, Closure $next)
    {
        $expected = (string) config('services.integration.key');

        if ($expected === '') {
            return response()->json(['status' => 'Failure', 'message' => 'Integration API is not configured'], 503);
        }

        $given = (string) ($request->header('X-Api-Key') ?: $request->bearerToken());

        if ($given === '' || !hash_equals($expected, $given)) {
            return response()->json(['status' => 'Failure', 'message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
