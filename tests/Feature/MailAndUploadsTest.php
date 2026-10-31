<?php

use App\Http\Controllers\Controller;
use App\Mail\TemplateMail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\Support\Fixtures;

// Template emails go through one queued Mailable; uploads go through the
// "uploads" disk (public/uploads).

it('sends template emails from the SMTP username with the app name', function () {
    config(['mail.mailers.smtp.username' => 'shop@example.test', 'app.name' => 'Hardcity']);

    $mail = new TemplateMail('Your order', '<p>Thanks</p>');

    $mail->assertFrom('shop@example.test', 'Hardcity');
    $mail->assertHasSubject('Your order');
    $mail->assertSeeInHtml('<p>Thanks</p>', false);
});

it('falls back to MAIL_FROM_ADDRESS when the SMTP username is empty', function () {
    config(['mail.mailers.smtp.username' => null, 'mail.from.address' => 'noreply@example.test']);

    (new TemplateMail('Hi', 'x'))->assertFrom('noreply@example.test');
});

it('queues the email (sent at once while QUEUE_CONNECTION=sync)', function () {
    expect(new TemplateMail('s', 'h'))->toBeInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class);
});

it('emails the customer the account deactivation OTP through TemplateMail', function () {
    Mail::fake();
    $id = Fixtures::insert('customer_master', ['cust_email' => 'buyer@example.test', 'cust_firstname' => 'Tan', 'cust_status' => 1, 'cust_password' => Hash::make('x')]);

    $this->postJson('/api/request-account-deactivate', ['customerid' => $id])->assertOk();

    Mail::assertQueued(TemplateMail::class, fn ($mail) => $mail->hasTo('buyer@example.test') && $mail->subjectLine === 'OTP for account deactivation');
});

it('stores uploads on the uploads disk under the destination folder', function () {
    Storage::fake('uploads');
    $controller = new class extends Controller {
        public function store($file) {
            $name = $this->safeUploadName($file);
            $this->storeUpload($file, public_path('uploads/brands'), $name);
            return $name;
        }
    };

    $name = $controller->store(UploadedFile::fake()->image('Bosch Logo.png'));

    Storage::disk('uploads')->assertExists('brands/' . $name);
});

it('serves the uploads disk from public/uploads at /uploads, as before', function () {
    expect(config('filesystems.disks.uploads.root'))->toBe(public_path('uploads'))
        ->and(config('filesystems.disks.uploads.url'))->toEndWith('/uploads');
});
