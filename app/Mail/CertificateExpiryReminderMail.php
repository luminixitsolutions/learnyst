<?php

namespace App\Mail;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CertificateExpiryReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Certificate $certificate,
        public int $daysUntilExpiry,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Certificate renewal reminder — {$this->daysUntilExpiry} days remaining",
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: view('emails.certificate-expiry-reminder', [
                'certificate' => $this->certificate,
                'daysUntilExpiry' => $this->daysUntilExpiry,
            ])->render(),
        );
    }
}
