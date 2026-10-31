<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Wraps the original mobile app endpoints during the move to /api/v2:
 * counts calls per day and endpoint (legacy_api_hits), adds Deprecation /
 * Sunset headers, and answers 410 once LEGACY_MOBILE_API=off. Response bodies
 * are not touched while the API is on.
 *
 * "legacy.mobile:always" keeps a route up when the API is off (mobileversions,
 * which the old app uses to prompt for an update).
 */
class LegacyMobileApi
{
    public function handle(Request $request, Closure $next, ?string $mode = null)
    {
        $this->record($request);

        if (config('shop.legacy_mobile_api') === 'off' && $mode !== 'always') {
            return response()->json([
                'response' => 'failed',
                'message' => 'This version of the app is no longer supported. Please update the app to continue.',
            ], 410);
        }

        $response = $next($request);

        $response->headers->set('Deprecation', 'true');
        $response->headers->set('Link', '<'.url('/api/v2').'>; rel="successor-version"');
        if ($sunset = config('shop.legacy_mobile_api_sunset')) {
            try {
                $response->headers->set('Sunset', Carbon::parse($sunset, 'UTC')->toRfc7231String());
            } catch (Throwable) {
                // a bad date in .env must not break the old app
            }
        }

        return $response;
    }

    /** One row per day and endpoint; counting must never fail the request. */
    private function record(Request $request): void
    {
        try {
            // route pattern ("api/itemdetails", "brandbyfilter/{filter}"), not the raw URL
            $path = (string) optional($request->route())->uri() ?: $request->path();

            DB::table('legacy_api_hits')->upsert(
                [['day' => now()->toDateString(), 'path' => substr($path, 0, 191), 'hits' => 1, 'last_seen_at' => now()]],
                ['day', 'path'],
                ['hits' => DB::raw('hits + 1'), 'last_seen_at' => now()],
            );
        } catch (Throwable $e) {
            Log::warning('legacy_api_hits not recorded: '.$e->getMessage());
        }
    }
}
