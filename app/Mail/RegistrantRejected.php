<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use App\Models\Registrant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrantRejected extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Registrant $registrant,
        public ?EmailTemplate $template = null,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->template?->subject ?? 'Pendaftaran Anda Ditolak';
        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        if ($this->template) {
            $html = $this->template->render([
                'name' => $this->registrant->name,
                'email' => $this->registrant->email,
                'status' => 'Rejected',
            ]);
            return new Content(htmlString: $html);
        }

        return new Content(view: 'emails.registrant-rejected');
    }
}
