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
            'password'    => $this->registrant->plain_password ?? '',
            'status'      => $this->registrant->status,
            'unique_code' => $this->registrant->unique_code ?? '',
            'admin_notes' => $this->registrant->admin_notes ?? '',
            'qr_code'     => $this->registrant->qr_token
                ? '<img src="' . $this->registrant->qr_code_url . '" alt="QR Code" style="width:200px;height:200px;display:block;margin:16px auto;">'
                : '',
            'qr_checkin_url' => $this->registrant->qr_checkin_url ?? '',
        ], $this->extraData);

        $htmlContent = $this->template->render($data);

        return new Content(view: 'emails.html-wrapper', with: ['htmlContent' => $htmlContent]);
    }
}
