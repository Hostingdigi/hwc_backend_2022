<?php

namespace App\Console\Commands;

use App\Models\PaymentMethods;
use App\Services\Payments\GatewayCredentials;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Checks a server (staging or production) before and after the cutover.
 * Exits non-zero when any check fails; warnings are worth reading but do not
 * block. See docs/cutover.md.
 */
class Preflight extends Command
{
    protected $signature = 'app:preflight';

    protected $description = 'Check this server is ready to run the shop (config, database, cron, gateways)';

    /** @var list<array{string, string, string}> [status, check, detail] */
    private array $rows = [];

    public function handle(): int
    {
        $production = app()->environment('production');

        $this->environment($production);
        $database = $this->database();
        $this->runtime($production);
        if ($database) {
            $this->gateways($production);
            $this->queue();
        }

        $this->table(['', 'Check', 'Detail'], array_map(
            fn ($r) => [match ($r[0]) { 'ok' => '<info>OK</info>', 'warn' => '<comment>WARN</comment>', default => '<error>FAIL</error>' }, $r[1], $r[2]],
            $this->rows,
        ));

        $failed = count(array_filter($this->rows, fn ($r) => $r[0] === 'fail'));
        $warned = count(array_filter($this->rows, fn ($r) => $r[0] === 'warn'));
        $failed ? $this->error("$failed check(s) failed, $warned warning(s).") : $this->info("All checks passed, $warned warning(s).");

        return $failed ? self::FAILURE : self::SUCCESS;
    }

    private function environment(bool $production): void
    {
        $this->check(version_compare(PHP_VERSION, '8.3.0', '>='), 'PHP version', PHP_VERSION.' (8.3+ required)');
        $key = (string) config('app.key') !== '';
        $this->check($key, 'APP_KEY', $key ? 'set' : 'empty: php artisan key:generate');
        $this->rows[] = ['ok', 'APP_ENV', app()->environment()];

        $debug = (bool) config('app.debug');
        $this->check(!$debug, 'APP_DEBUG', $debug ? 'true: error pages show code and secrets' : 'false', $production ? 'fail' : 'warn');

        $url = (string) config('app.url');
        $this->check(str_starts_with($url, 'https://'), 'APP_URL', $url ?: '(empty)', $production ? 'fail' : 'warn');

        $mailer = (string) config('mail.default');
        if ($production) {
            $this->check(!in_array($mailer, ['log', 'array'], true), 'Mail', "MAIL_MAILER=$mailer: customers get no emails");
        } else {
            $this->check(in_array($mailer, ['log', 'array'], true), 'Mail', "MAIL_MAILER=$mailer".($mailer === 'log' ? '' : ': use log on staging, or run app:scrub-staging first'), 'warn');
        }

        foreach (['services.integration.key' => 'INTEGRATION_API_KEY', 'services.punchout.shared_secret' => 'PUNCHOUT_SHARED_SECRET'] as $key => $env) {
            $this->check((string) config($key) !== '', $env, (string) config($key) !== '' ? 'set' : 'empty: that integration is refused', 'warn');
        }

        foreach (['services.payment_verification.mode' => 'PAYMENT_VERIFICATION_MODE', 'services.mobile_pricing.mode' => 'MOBILE_PRICING_MODE'] as $key => $env) {
            $mode = (string) config($key);
            $this->check($mode === 'enforce', $env, $mode, 'warn');
        }

        $legacy = (string) config('shop.legacy_mobile_api');
        $this->rows[] = ['ok', 'Old mobile API', "LEGACY_MOBILE_API=$legacy".(config('shop.legacy_mobile_api_sunset') ? ', sunset '.config('shop.legacy_mobile_api_sunset') : '')];
    }

    private function database(): bool
    {
        try {
            DB::connection()->getPdo();
            $this->rows[] = ['ok', 'Database', DB::connection()->getDatabaseName()];
        } catch (Throwable $e) {
            $this->rows[] = ['fail', 'Database', $e->getMessage()];

            return false;
        }

        $migrator = app('migrator');
        if (!$migrator->repositoryExists()) {
            $this->rows[] = ['fail', 'Migrations', 'no migrations table: run php artisan migrate --force'];
        } else {
            $files = array_keys($migrator->getMigrationFiles([database_path('migrations')]));
            $pending = array_diff($files, $migrator->getRepository()->getRan());
            $this->check(!$pending, 'Migrations', $pending ? count($pending).' pending: php artisan migrate --force' : 'up to date');
        }

        $missing = array_values(array_filter(
            ['customer_master', 'order_master', 'paymentmethods', 'personal_access_tokens', 'password_resets', 'jobs', 'failed_jobs', 'legacy_api_hits'],
            fn ($t) => !Schema::hasTable($t),
        ));
        $this->check(!$missing, 'Tables', $missing ? 'missing: '.implode(', ', $missing) : 'present');

        return true;
    }

    private function runtime(bool $production): void
    {
        foreach ([storage_path('framework/cache'), storage_path('framework/sessions'), storage_path('framework/views'), storage_path('logs'), base_path('bootstrap/cache')] as $dir) {
            if (!is_dir($dir) || !is_writable($dir)) {
                $this->rows[] = ['fail', 'Writable folders', str_replace(base_path().DIRECTORY_SEPARATOR, '', $dir).' is not writable'];

                return;
            }
        }
        $this->rows[] = ['ok', 'Writable folders', 'storage/ and bootstrap/cache'];

        $link = file_exists(public_path('storage'));
        $this->check($link, 'Storage link', $link ? 'public/storage' : 'missing: php artisan storage:link', 'warn');
        $build = file_exists(public_path('build/manifest.json'));
        $this->check($build, 'Front-end build', $build ? 'public/build' : 'missing: npm run build', $production ? 'fail' : 'warn');

        $cached = app()->configurationIsCached() && app()->routesAreCached();
        $this->check($cached, 'Config/route cache', $cached ? 'cached' : 'run php artisan optimize after each deploy', $production ? 'warn' : 'ok');

        $last = Cache::get('scheduler:last_run');
        $age = $last ? now()->timestamp - (int) $last : null;
        $this->check($age !== null && $age < 300, 'Cron (scheduler)',
            $age === null ? 'never ran: add the cron entry (docs/cutover.md)' : 'last ran '.$age.'s ago', 'warn');
    }

    private function gateways(bool $production): void
    {
        $methods = PaymentMethods::available()->where('status', 1)->get(['Id', 'payment_name', 'payment_mode']);
        $this->check($methods->isNotEmpty(), 'Payment methods', $methods->isEmpty() ? 'none active' : $methods->pluck('payment_name')->implode(', '));

        foreach ($methods as $method) {
            $live = $method->payment_mode === 'live';
            $mode = $method->payment_mode ?: '(not set)';
            $name = $method->payment_name.' mode';
            if ($production) {
                $this->check($live, $name, $mode.($live ? '' : ': payments go to the test gateway'));
            } else {
                $this->check(!$live, $name, $mode.($live ? ': staging would take real payments (php artisan app:scrub-staging)' : ''));
            }

            if (in_array((int) $method->Id, [GatewayCredentials::STRIPE, GatewayCredentials::PAYPAL, GatewayCredentials::ATOME], true)) {
                $creds = GatewayCredentials::for((int) $method->Id);
                $this->check($creds['key'] !== '' && $creds['secret'] !== '', $method->payment_name.' keys',
                    ($live ? 'live' : 'test').' key and secret '.($creds['key'] !== '' && $creds['secret'] !== '' ? 'set' : 'missing (admin > Payment Methods)'));
            }

            if ((int) $method->Id === GatewayCredentials::STRIPE) {
                $set = (string) config('services.stripe.webhook_secret') !== '';
                $this->check($set, 'Stripe webhook', $set ? 'STRIPE_WEBHOOK_SECRET set' : 'STRIPE_WEBHOOK_SECRET empty: app payments rely on it', $production ? 'fail' : 'warn');
            }
        }
    }

    private function queue(): void
    {
        $connection = (string) config('queue.default');
        $this->check($connection === 'database', 'Queue', "QUEUE_CONNECTION=$connection".($connection === 'sync' ? ': emails are sent during the request' : ''), 'warn');

        if (!Schema::hasTable('jobs') || !Schema::hasTable('failed_jobs')) {
            return; // already reported under Tables
        }

        $stuck = DB::table('jobs')->where('created_at', '<', now()->subMinutes(10)->timestamp)->count();
        $this->check($stuck === 0, 'Queued jobs', $stuck ? "$stuck older than 10 minutes: is the cron running?" : 'none waiting', 'warn');

        $failed = DB::table('failed_jobs')->count();
        $this->check($failed === 0, 'Failed jobs', $failed ? "$failed: php artisan queue:failed" : 'none', 'warn');
    }

    /** Adds a row: ok when $pass, otherwise $severity ("fail" or "warn"). */
    private function check(bool $pass, string $name, string $detail, string $severity = 'fail'): void
    {
        $this->rows[] = [$pass ? 'ok' : $severity, $name, $detail];
    }
}
