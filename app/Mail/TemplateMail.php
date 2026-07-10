<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use App\Models\Registrant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TemplateMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param Registrant $registrant
     * @param EmailTemplate $template
     * @param array $extraData Additional placeholder data (e.g. workshop_name, password, etc.)
     */
    public function __construct(
        public Registrant $registrant,
        public EmailTemplate $template,
        public array $extraData = [],
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->template->subject);
    }

    public function content(): Content
    {
        $data = array_merge([
            'name'        => $this->registrant->display_name,
            'email'       => $this->registrant->email,
            'status'      => $this->registrant->status,
            'unique_code' => $this->registrant->unique_code ?? '',
            'admin_notes' => $this->registrant->admin_notes ?? '',
        ], $this->extraData);

        $html = $this->template->render($data);

        return new Content(htmlString: $html);
    }
}
