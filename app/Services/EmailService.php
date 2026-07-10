<?php

namespace App\Services;

use App\Mail\TemplateMail;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\Registrant;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    /**
     * Send an email using a template and log it.
     *
     * @param Registrant $registrant
     * @param EmailTemplate $template
     * @param array $extraData Additional placeholder data
     * @return EmailLog
     */
    public static function send(Registrant $registrant, EmailTemplate $template, array $extraData = []): EmailLog
    {
        // Render HTML content for logging
        $renderData = array_merge([
            'name'        => $registrant->display_name,
            'email'       => $registrant->email,
            'status'      => $registrant->status,
            'unique_code' => $registrant->unique_code ?? '',
            'admin_notes' => $registrant->admin_notes ?? '',
        ], $extraData);

        $htmlContent = $template->render($renderData);

        $log = EmailLog::create([
            'email_template_id' => $template->id,
            'registrant_id'     => $registrant->id,
            'template_type'     => $template->type,
            'recipient_email'   => $registrant->email,
            'recipient_name'    => $registrant->display_name,
            'subject'           => $template->subject,
            'html_content'      => $htmlContent,
            'status'            => 'sent',
            'sent_at'           => now(),
        ]);

        try {
            Mail::to($registrant->email)->send(
                new TemplateMail($registrant, $template, $extraData)
            );
        } catch (\Throwable $e) {
            $log->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }

        return $log;
    }

    /**
     * Send using a template type — finds the active template for that type.
     * Returns null if no active template found or if sending failed.
     */
    public static function sendByType(Registrant $registrant, string $type, array $extraData = []): ?EmailLog
    {
        $template = EmailTemplate::activeOfType($type);

        if (!$template) {
            return null;
        }

        return self::send($registrant, $template, $extraData);
    }

    /**
     * Get logs for a specific template, paginated.
     */
    public static function logsForTemplate(EmailTemplate $template, int $perPage = 30)
    {
        return EmailLog::where('email_template_id', $template->id)
            ->with('registrant')
            ->latest('sent_at')
            ->paginate($perPage);
    }

    /**
     * Get all logs, paginated.
     */
    public static function allLogs(int $perPage = 30)
    {
        return EmailLog::with(['template', 'registrant'])
            ->latest('sent_at')
            ->paginate($perPage);
    }
}
