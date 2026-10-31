<?php

use App\Mail\TemplateMail;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

// cPanel setup: QUEUE_CONNECTION=database, drained by the scheduler every minute.

it('queues template emails in the database and sends them when the worker runs', function () {
    config(['queue.default' => 'database']);

    Mail::to('buyer@example.test')->send(new TemplateMail('Your order', '<p>Thanks</p>'));
    expect(DB::table('jobs')->count())->toBe(1)
        ->and(app('mailer')->getSymfonyTransport()->messages())->toHaveCount(0);

    Artisan::call('queue:work', ['--stop-when-empty' => true, '--once' => true]);

    expect(DB::table('jobs')->count())->toBe(0)
        ->and(app('mailer')->getSymfonyTransport()->messages())->toHaveCount(1);
});

it('schedules the queue worker every minute so cPanel cron can drain the queue', function () {
    $events = collect(app(Schedule::class)->events())->map(fn ($e) => [$e->expression, $e->command]);

    expect($events->first(fn ($e) => str_contains($e[1], 'queue:work') && $e[0] === '* * * * *'))->not->toBeNull()
        ->and($events->first(fn ($e) => str_contains($e[1], 'sanctum:prune-expired')))->not->toBeNull();
});
