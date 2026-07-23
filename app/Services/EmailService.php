<?php

namespace App\Services;

use App\Mail\RegistrantCredentials;
use App\Mail\RegistrantRejected;
use App\Mail\TemplateMail;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\Registrant;
use App\Models\Track;
use Illuminate\Support\Facades\DB;
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
            'password'    => $registrant->plain_password ?? '',
            'status'      => $registrant->status,
            'unique_code' => $registrant->unique_code ?? '',
            'admin_notes' => $registrant->admin_notes ?? '',
            'qr_code'     => $registrant->qr_token
                ? '<img src="' . $registrant->qr_code_url . '" alt="QR Code" style="width:150px;height:150px;display:block;margin:16px auto;">'
                : '',
            'qr_checkin_url' => $registrant->qr_checkin_url ?? '',
        ], $extraData);

        $htmlContent = $template->render($renderData);
        $renderedSubject = $template->renderSubject($renderData);

        $log = EmailLog::create([
            'email_template_id' => $template->id,
            'registrant_id'     => $registrant->id,
            'template_type'     => $template->type,
            'recipient_email'   => $registrant->email,
            'recipient_name'    => $registrant->display_name,
            'subject'           => $renderedSubject,
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

    /**
     * Resend an email based on an existing EmailLog record.
     *
     * @param EmailLog $log The original email log to resend
     * @return EmailLog|null The new email log, or null on failure
     */
    public static function resend(EmailLog $log): ?EmailLog
    {
        $registrant = $log->registrant;

        if (!$registrant) {
            return null;
        }

        // ── Template-based resend ──
        if ($log->email_template_id && $log->template) {
            $extraData = [];

            // Include password for approval-type emails
            if (in_array($log->template_type, ['approval', 'registration']) && $registrant->plain_password) {
                $extraData['password'] = $registrant->plain_password;
            }

            // Include admin notes for rejection-type emails
            if (in_array($log->template_type, ['rejection', 'workshop_rejection', 'track_rejection'])) {
                $extraData['admin_notes'] = $registrant->admin_notes ?? '';
            }

            // Include workshop data for workshop-related types
            if (in_array($log->template_type, ['workshop_approval', 'workshop_rejection', 'workshop_reminder'])) {
                $workshops = $registrant->workshops()
                    ->wherePivotIn('status', ['approved', 'rejected'])
                    ->get();
                $workshop = null;

                // Match the correct workshop by comparing rendered subject
                foreach ($workshops as $ws) {
                    $testData = array_merge($extraData, $ws->emailData());
                    $rendered = $log->template->renderSubject($testData);
                    if ($rendered === $log->subject) {
                        $workshop = $ws;
                        break;
                    }
                }

                // Fallback: try matching by name/title appearing in the log subject
                if (!$workshop) {
                    foreach ($workshops as $ws) {
                        $wsName = $ws->name ?: $ws->title;
                        if (str_contains($log->subject, $wsName) || str_contains($log->subject, $ws->title)) {
                            $workshop = $ws;
                            break;
                        }
                    }
                }

                // Final fallback to first workshop
                if (!$workshop) {
                    $workshop = $workshops->first();
                }

                if ($workshop) {
                    // Check if registrant has a track_id in pivot
                    $trackId = DB::table('registrant_workshop')
                        ->where('workshop_id', $workshop->id)
                        ->where('registrant_id', $registrant->id)
                        ->value('track_id');

                    if ($trackId && ($track = Track::with('agendaItems')->find($trackId))) {
                        // Use track-specific time data
                        $trackAi = $track->agendaItems->first()
                            ?? $workshop->agendaItems()->where('track_id', $track->id)->first()
                            ?? $workshop->agendaItems()->first();

                        $start = $track->start_time ?? $trackAi?->start_time ?? $workshop->start_time;
                        $end   = $track->end_time ?? $trackAi?->end_time ?? $workshop->end_time;
                        $room  = $trackAi?->room ?? $workshop->room ?? '';
                        $date  = $trackAi?->date ?? $workshop->date;

                        $timeRange = '—';
                        if ($start && $end) {
                            $timeRange = date('H:i', strtotime($start)) . ' – ' . date('H:i', strtotime($end));
                        }

                        $extraData = array_merge($extraData, [
                            'track_name'        => $track->name,
                            'track_title'       => $track->title,
                            'workshop_name'     => $workshop->name ?: $workshop->title,
                            'workshop_title'    => $workshop->title,
                            'workshop_room'     => $room,
                            'workshop_date'     => $date ? $date->format('l, d F Y') : '',
                            'workshop_time'     => $timeRange,
                            'workshop_capacity' => (string) ($workshop->capacity ?: 0),
                            'venue_name'        => 'Shangri-La Hotel Jakarta',
                        ]);
                    } else {
                        $extraData = array_merge($extraData, $workshop->emailData());
                    }
                }
            }

            // Include track/session data for track-related types
            if (in_array($log->template_type, ['track_approval', 'track_rejection'])) {
                $agendaItems = $registrant->agendaItems()
                    ->wherePivotIn('status', ['approved', 'rejected'])
                    ->get();
                $agendaItem = null;

                // Match the correct agenda item by comparing rendered subject
                foreach ($agendaItems as $ai) {
                    $testData = array_merge($extraData, [
                        'track_name' => $ai->title,
                        'workshop_room' => $ai->room ?? '',
                        'workshop_date' => $ai->date?->format('l, d F Y') ?? '',
                        'workshop_time' => ($ai->start_time ? date('H:i', strtotime($ai->start_time)) : '') . ' – ' . ($ai->end_time ? date('H:i', strtotime($ai->end_time)) : ''),
                    ]);
                    $rendered = $log->template->renderSubject($testData);
                    if ($rendered === $log->subject) {
                        $agendaItem = $ai;
                        break;
                    }
                }

                // Fallback to first if no match found
                if (!$agendaItem) {
                    $agendaItem = $agendaItems->first();
                }

                if ($agendaItem) {
                    $extraData['track_name'] = $agendaItem->title;
                    $extraData['workshop_room'] = $agendaItem->room ?? '';
                    $extraData['workshop_date'] = $agendaItem->date?->format('l, d F Y') ?? '';
                    $extraData['workshop_time'] = ($agendaItem->start_time ? date('H:i', strtotime($agendaItem->start_time)) : '') . ' – ' . ($agendaItem->end_time ? date('H:i', strtotime($agendaItem->end_time)) : '');
                }
            }

            return self::send($registrant, $log->template, $extraData);
        }

        // ── Fallback resend (no template) ──
        try {
            if ($log->template_type === EmailTemplate::TYPE_APPROVAL && $registrant->plain_password) {
                Mail::to($registrant->email)->send(
                    new RegistrantCredentials($registrant, $registrant->plain_password)
                );
            } elseif ($log->template_type === EmailTemplate::TYPE_REJECTION) {
                Mail::to($registrant->email)->send(
                    new RegistrantRejected($registrant)
                );
            } else {
                // Generic fallback: send raw HTML
                Mail::to($registrant->email)->send(
                    new TemplateMail($registrant, $log->template ?? new EmailTemplate(), [])
                );
            }

            return EmailLog::create([
                'email_template_id' => null,
                'registrant_id'     => $registrant->id,
                'template_type'     => $log->template_type,
                'recipient_email'   => $registrant->email,
                'recipient_name'    => $registrant->display_name,
                'subject'           => $log->subject,
                'html_content'      => $log->html_content,
                'status'            => 'sent',
                'sent_at'           => now(),
            ]);
        } catch (\Throwable $e) {
            return EmailLog::create([
                'email_template_id' => null,
                'registrant_id'     => $registrant->id,
                'template_type'     => $log->template_type,
                'recipient_email'   => $registrant->email,
                'recipient_name'    => $registrant->display_name,
                'subject'           => $log->subject,
                'html_content'      => $log->html_content,
                'status'            => 'failed',
                'error_message'     => $e->getMessage(),
                'sent_at'           => now(),
            ]);
        }
    }
}
