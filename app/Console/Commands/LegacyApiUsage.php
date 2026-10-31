<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * How much the original mobile app endpoints are still used, from the daily
 * counts LegacyMobileApi keeps. Use it to decide when LEGACY_MOBILE_API=off is
 * safe (docs/cutover.md).
 */
class LegacyApiUsage extends Command
{
    protected $signature = 'api:legacy-usage {--days=14 : How many days back to show}';

    protected $description = 'Show daily calls to the original mobile app API (replaced by /api/v2)';

    public function handle(): int
    {
        $since = now()->subDays(max(1, (int) $this->option('days')) - 1)->toDateString();

        $daily = DB::table('legacy_api_hits')->where('day', '>=', $since)
            ->groupBy('day')->orderBy('day')
            ->selectRaw('day, SUM(hits) AS calls, COUNT(*) AS endpoints')->get();

        if ($daily->isEmpty()) {
            $this->info("No calls to the old mobile API since $since.");

            return self::SUCCESS;
        }

        $this->table(['Day', 'Calls', 'Endpoints'], $daily->map(fn ($d) => [$d->day, $d->calls, $d->endpoints]));

        $top = DB::table('legacy_api_hits')->where('day', '>=', $since)
            ->groupBy('path')->orderByDesc('calls')->limit(15)
            ->selectRaw('path, SUM(hits) AS calls, MAX(last_seen_at) AS last_seen')->get();
        $this->table(['Endpoint', 'Calls', 'Last call'], $top->map(fn ($t) => [$t->path, $t->calls, $t->last_seen]));

        return self::SUCCESS;
    }
}
