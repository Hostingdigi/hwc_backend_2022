<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Makes a copy of the production database safe to use on staging: every
 * gateway goes to test mode and customer email addresses are replaced, so
 * staging can neither take real payments nor email real customers. Refuses to
 * run when APP_ENV=production. See docs/cutover.md.
 */
class ScrubStaging extends Command
{
    protected $signature = 'app:scrub-staging {--force : Do not ask for confirmation}';

    protected $description = 'Make a copy of production data safe for staging (test gateways, no real customer emails)';

    /** table => [primary key, email columns] */
    private const EMAILS = [
        'customer_master' => ['cust_id', ['cust_email']],
        'order_master' => ['order_id', ['ship_email', 'bill_email']],
        'order_payments' => ['id', ['ship_email', 'bill_email']],
        'subscriber' => ['id', ['email']],
    ];

    /** Rows that only make sense on the server they were created on */
    private const CLEAR = ['personal_access_tokens', 'password_resets', 'jobs', 'failed_jobs'];

    public function handle(): int
    {
        if (app()->environment('production')) {
            $this->error('APP_ENV is production. This command rewrites customer data and only runs on staging.');

            return self::FAILURE;
        }

        $database = DB::connection()->getDatabaseName();
        if (!$this->option('force') && !$this->confirm("Rewrite customer emails and switch gateways to test mode in database \"$database\"?")) {
            return self::FAILURE;
        }

        DB::transaction(function () {
            $gateways = DB::table('paymentmethods')->where(fn ($q) => $q->where('payment_mode', '!=', 'test')->orWhereNull('payment_mode'))
                ->update(['payment_mode' => 'test']);
            $this->line("Payment methods switched to test mode: $gateways");

            foreach (self::EMAILS as $table => [$key, $columns]) {
                if (!Schema::hasTable($table)) {
                    continue;
                }
                foreach ($columns as $column) {
                    if (!Schema::hasColumn($table, $column)) {
                        continue;
                    }
                    // example.invalid can never receive mail (RFC 2606)
                    $count = DB::table($table)->whereNotNull($column)->where($column, '!=', '')
                        ->where($column, 'NOT LIKE', '%@example.invalid')
                        ->update([$column => DB::raw("CONCAT('$table-', `$key`, '-$column@example.invalid')")]);
                    $this->line("$table.$column replaced: $count");
                }
            }
        });

        foreach (self::CLEAR as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->delete();
            }
        }
        $this->line('Cleared: '.implode(', ', self::CLEAR));

        $this->info('Done. Register a new customer on staging to test checkout; real customers cannot sign in by email any more.');

        return self::SUCCESS;
    }
}
