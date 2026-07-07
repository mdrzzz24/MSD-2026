<?php

namespace App\Mail;

use App\Models\Registrant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrantCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Registrant $registrant,
        public string $plainPassword,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Akun Anda Telah Disetujui — Login Credentials');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.registrant-credentials');
    }
}
