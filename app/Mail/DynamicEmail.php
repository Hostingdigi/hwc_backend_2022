<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models as HWCModel;

class DynamicEmail extends Mailable
{
    use Queueable, SerializesModels;
    public string $subjectText;
    public string $bodyHtml;
    /**
     * Create a new message instance.
     */
    public function __construct(string $subject, string $body)
    {
        $this->subjectText = $subject;
        $this->bodyHtml = $body;
    }

    /**
     * Get the message envelope.
     */
    public function build()
    {
        $setting = HWCModel\Settings::select('company_name','admin_email','cc_email')->find(1);
        $companyName = $setting->company_name ?? config('app.name');
        $fromEmail = $setting->admin_email ?? env('MAIL_FROM_ADDRESS');
		$replyTo = $fromEmail;

        return $this
            ->from($fromEmail, $companyName)
            ->replyTo($replyTo, 'Reply-To')
            ->subject($this->subjectText)
            ->view('emails.dynamic')
            ->with([
                'body' => $this->bodyHtml,
            ]);
    }
}
