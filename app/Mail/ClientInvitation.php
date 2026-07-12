<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClientInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $setupUrl;

    public function __construct(User $user, string $setupUrl)
    {
        $this->user = $user;
        $this->setupUrl = $setupUrl;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You\'re invited — Set up your MSD 2026 account',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.client-invitation',
            with: [
                'name'     => $this->user->name,
                'email'    => $this->user->email,
                'setupUrl' => $this->setupUrl,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
