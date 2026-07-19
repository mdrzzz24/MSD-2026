<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\Exportable;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\Registrant;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailLogController extends Controller
{
    use Exportable;
    /**
     * Show all email logs with filtering by type.
     */
    public function index(Request $request)
    {
        if (!auth()->user()->hasPermission('email_templates')) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'You do not have permission to view email logs.');
        }

        $types = EmailTemplate::types();

        $query = EmailLog::with(['template', 'registrant']);

        // Filter by template type
        if ($request->filled('type') && isset($types[$request->type])) {
            $query->where('template_type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('sent_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('sent_at', '<=', $request->date_to);
        }

        // Search by recipient email or name
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('recipient_email', 'like', "%{$s}%")
                  ->orWhere('recipient_name', 'like', "%{$s}%")
                  ->orWhere('subject', 'like', "%{$s}%");
            });
        }

        $logs = $query->latest('sent_at')->paginate(30);

        // Stats for summary cards
        $totalSent = EmailLog::count();
        $totalSuccess = EmailLog::where('status', 'sent')->count();
        $totalFailed = EmailLog::where('status', 'failed')->count();
        $totalBounced = EmailLog::where('status', 'bounced')->count();
        $uniqueRecipients = EmailLog::distinct('recipient_email')->count('recipient_email');

        return view('admin.email-logs.index', compact(
            'logs', 'types', 'totalSent', 'totalSuccess', 'totalFailed', 'totalBounced', 'uniqueRecipients'
        ));
    }

    /**
     * Show a single log detail.
     */
    public function show(EmailLog $emailLog)
    {
        if (!auth()->user()->hasPermission('email_templates')) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'You do not have permission to view email logs.');
        }

        $emailLog->load(['template', 'registrant']);
        return view('admin.email-logs.show', compact('emailLog'));
    }

    /**
     * Resend an email based on an existing log entry.
     */
    public function resend(EmailLog $emailLog)
    {
        if (!auth()->user()->hasPermission('email_templates')) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'You do not have permission to resend emails.');
        }

        $result = EmailService::resend($emailLog);

        if (!$result) {
            return redirect()->back()
                ->with('error', 'Failed to resend email: registrant not found.');
        }

        if ($result->status === 'sent') {
            return redirect()->back()
                ->with('success', 'Email successfully resent to <strong>' . e($result->recipient_name) . '</strong>.');
        }

        return redirect()->back()
            ->with('error', 'Failed to resend email: ' . e($result->error_message ?? 'Unknown error'));
    }

    /**
     * Show send reminder form with list of approved registrants.
     */
    public function reminderForm()
    {
        if (!auth()->user()->hasPermission('email_templates')) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'You do not have permission to send reminders.');
        }

        $types = EmailTemplate::types();
        $reminderType = EmailTemplate::TYPE_REMINDER;

        $registrants = Registrant::where('status', 'approved')
            ->orderBy('name')
            ->get();

        $activeTemplate = EmailTemplate::activeOfType($reminderType);

        return view('admin.email-logs.send-reminder', compact(
            'registrants', 'types', 'reminderType', 'activeTemplate'
        ));
    }

    /**
     * Send gentle reminder to selected registrants.
     */
    public function sendReminder(Request $request)
    {
        if (!auth()->user()->hasPermission('email_templates')) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'You do not have permission to send reminders.');
        }

        $request->validate([
            'registrant_ids' => 'required|array|min:1',
            'registrant_ids.*' => 'exists:registrants,id',
        ]);

        $template = EmailTemplate::activeOfType(EmailTemplate::TYPE_REMINDER);
        $registrants = Registrant::whereIn('id', $request->registrant_ids)->get();

        if ($registrants->isEmpty()) {
            return back()->with('error', 'Pilih minimal satu peserta.');
        }

        $successCount = 0;
        $errors = [];

        foreach ($registrants as $registrant) {
            try {
                $result = EmailService::sendByType($registrant, EmailTemplate::TYPE_REMINDER);
                if ($result) {
                    $successCount++;
                } else {
                    $errors[] = $registrant->email . ': no active template';
                }
            } catch (\Exception $e) {
                $errors[] = $registrant->email . ': ' . $e->getMessage();
            }
        }

        if ($successCount > 0) {
            $msg = "Reminder berhasil dikirim ke {$successCount} peserta.";
            if (!empty($errors)) {
                $msg .= ' Gagal: ' . implode('; ', $errors);
            }
            return back()->with('success', $msg);
        }

        return back()->with('error', 'Gagal: ' . implode('; ', $errors));
    }

    /**
     * Export email logs to CSV.
     */
    public function exportCsv(Request $request)
    {
        if (!auth()->user()->hasPermission('email_templates')) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'You do not have permission to export email logs.');
        }

        $query = EmailLog::query();

        if ($request->type) {
            $query->where('template_type', $request->type);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->date_from) {
            $query->whereDate('sent_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('sent_at', '<=', $request->date_to);
        }
        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('recipient_email', 'like', "%{$s}%")
                  ->orWhere('recipient_name', 'like', "%{$s}%")
                  ->orWhere('subject', 'like', "%{$s}%");
            });
        }

        $logs = $query->latest()->get();

        $rows = $logs->map(fn($l) => [
            $l->id,
            $l->template_type,
            $l->recipient_email,
            $l->recipient_name,
            $l->subject,
            $l->status,
            $l->error_message ?? '',
            $l->sent_at ? $l->sent_at->format('Y-m-d H:i:s') : '',
        ])->toArray();

        return $this->csvDownload(
            ['ID', 'Type', 'Recipient Email', 'Recipient Name', 'Subject', 'Status', 'Error', 'Sent At'],
            $rows,
            'email-logs-' . now()->format('YmdHis') . '.csv'
        );
    }
}
