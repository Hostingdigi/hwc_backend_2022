<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled tasks
|--------------------------------------------------------------------------
|
| Shared cPanel hosting cannot run a long-lived queue worker, so the cron
| entry below runs the scheduler every minute and the scheduler drains the
| database queue (queued emails) on each run:
|
|   * * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
|
| With QUEUE_CONNECTION=sync (the default) the queue job simply finds nothing.
|
*/

Schedule::command('queue:work --stop-when-empty --max-time=50 --tries=3')
    ->everyMinute()
    ->withoutOverlapping(5);

// Expired mobile API tokens (SANCTUM_TOKEN_EXPIRATION, 90 days by default)
Schedule::command('sanctum:prune-expired --hours=24')->daily();

// Failed queue jobs are kept a week for investigation
Schedule::command('queue:prune-failed --hours=168')->daily();
