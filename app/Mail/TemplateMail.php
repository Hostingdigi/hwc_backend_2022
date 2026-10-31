<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * An email whose subject and HTML body were already built from an
 * EmailTemplate row (order confirmations, status updates, enquiries, ...).
 *
 * Replaces the inline Mail::send() closures that read env() directly (which
 * stops working after config:cache). Queued: with QUEUE_CONNECTION=sync it
 * still sends immediately; with a real queue it no longer slows checkout down.
 */
class TemplateMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $subjectLine,
        public string $body,
        public ?string $fromName = null,
    ) {
    }

    public function envelope(): Envelope
    {
        // Same sender as before: the SMTP username (MAIL_USERNAME), falling back
        // to MAIL_FROM_ADDRESS when that is empty.
        $address = config('mail.mailers.smtp.username') ?: config('mail.from.address');

        return new Envelope(
            from: new Address($address, $this->fromName ?? config('app.name')),
            subject: $this->subjectLine,
        );
    }

    public function content(): Content
    {
        return new Content(htmlString: $this->body);
    }
}
