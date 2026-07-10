<?php

namespace App\Http\Controllers;

use App\Mail\TemplateMail;
use App\Models\EmailTemplate;
use App\Models\Registrant;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class EmailTemplateController extends Controller
{
    /**
     * Show all email templates.
     */
    public function index()
    {
        $templates = EmailTemplate::latest()->get();
        $types = EmailTemplate::types();
        return view('admin.templates.index', compact('templates', 'types'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $types = EmailTemplate::types();
        return view('admin.templates.create', compact('types'));
    }

    /**
     * Store a new template.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'type'         => ['required', Rule::in(array_keys(EmailTemplate::types()))],
            'description'  => ['nullable', 'string', 'max:500'],
            'subject'      => ['required', 'string', 'max:255'],
            'html_content' => ['required', 'string'],
        ]);

        EmailTemplate::create($validated + ['is_active' => true]);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Email template created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(EmailTemplate $template)
    {
        $types = EmailTemplate::types();
        return view('admin.templates.edit', compact('template', 'types'));
    }

    /**
     * Update a template.
     */
    public function update(Request $request, EmailTemplate $template)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'type'         => ['required', Rule::in(array_keys(EmailTemplate::types()))],
            'description'  => ['nullable', 'string', 'max:500'],
            'subject'      => ['required', 'string', 'max:255'],
            'html_content' => ['required', 'string'],
        ]);

        $template->update($validated);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Email template updated successfully.');
    }

    /**
     * Delete a template.
     */
    public function destroy(EmailTemplate $template)
    {
        $template->delete();

        return redirect()->route('admin.templates.index')
            ->with('success', 'Email template deleted successfully.');
    }

    /**
     * Toggle active status.
     */
    public function toggleActive(EmailTemplate $template)
    {
        $template->update(['is_active' => !$template->is_active]);

        $status = $template->is_active ? 'activated' : 'deactivated';
        return redirect()->route('admin.templates.index')
            ->with('success', "Template has been {$status}.");
    }

    /**
     * Show upload form for HTML file.
     */
    public function uploadForm()
    {
        $types = EmailTemplate::types();
        return view('admin.templates.upload', compact('types'));
    }

    /**
     * Handle HTML file upload.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'type'        => ['required', Rule::in(array_keys(EmailTemplate::types()))],
            'description' => ['nullable', 'string', 'max:500'],
            'subject'     => ['required', 'string', 'max:255'],
            'html_file'   => ['required', 'file', 'mimes:html,htm', 'max:2048'],
        ]);

        $htmlContent = file_get_contents($request->file('html_file')->getRealPath());

        EmailTemplate::create([
            'name'         => $request->input('name'),
            'type'         => $request->input('type'),
            'description'  => $request->input('description'),
            'subject'      => $request->input('subject'),
            'html_content' => $htmlContent,
            'is_active'    => true,
        ]);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Email template uploaded successfully.');
    }

    /**
     * Preview a template with sample data.
     */
    public function preview(EmailTemplate $template)
    {
        $html = $template->render([
            'name'          => 'John Doe',
            'email'         => 'john@example.com',
            'status'        => 'approved',
            'unique_code'   => '100724080000',
            'admin_notes'   => 'Sample admin note.',
            'password'      => '••••••••••',
            'workshop_name' => 'Sample Workshop',
            'track_name'    => 'Sample Track',
            'event_date'    => '12 Agustus 2026',
            'login_url'     => route('registrant.login'),
        ]);

        return view('admin.templates.preview', compact('template', 'html'));
    }

    /**
     * Show email send logs for a specific template.
     */
    public function logs(EmailTemplate $template)
    {
        $logs = EmailService::logsForTemplate($template);

        // Enrich logs without stored html_content by re-rendering from template + registrant data
        $logs->getCollection()->transform(function ($log) use ($template) {
            if (empty($log->html_content) && $log->registrant) {
                $log->html_content = $template->render([
                    'name'          => $log->registrant->display_name,
                    'email'         => $log->recipient_email,
                    'status'        => $log->registrant->status ?? 'approved',
                    'unique_code'   => $log->registrant->unique_code ?? '',
                    'admin_notes'   => $log->registrant->admin_notes ?? '',
                    'password'      => $log->registrant->plain_password ?? '••••••••••',
                    'workshop_name' => '',
                    'track_name'    => '',
                ]);
            }
            return $log;
        });

        return view('admin.templates.logs', compact('template', 'logs'));
    }

    /**
     * Toggle auto-email on new registration ON/OFF.
     */
    public function toggleAutoEmail()
    {
        $current = Cache::get('auto_registration_email', true);
        Cache::put('auto_registration_email', !$current);

        $status = !$current ? 'ACTIVE' : 'PAUSED';
        return redirect()->route('admin.templates.index')
            ->with('success', "Auto-registration email is now <strong>{$status}</strong>.");
    }

    /**
     * Show & handle sending gentle reminder emails to approved registrants.
     */
    public function sendReminder(Request $request)
    {
        $template = EmailTemplate::activeOfType(EmailTemplate::TYPE_REMINDER);

        if (!$template) {
            return back()->with('error', 'No active Reminder template found. Please create one first.');
        }

        if ($request->isMethod('post')) {
            $request->validate([
                'registrant_ids'   => ['nullable', 'array'],
                'registrant_ids.*' => ['exists:registrants,id'],
            ]);

            $query = Registrant::approved();
            if ($request->has('registrant_ids') && !empty($request->input('registrant_ids'))) {
                $query->whereIn('id', $request->input('registrant_ids'));
            }

            $registrants = $query->get();
            $count = 0;

            foreach ($registrants as $registrant) {
                EmailService::send($registrant, $template);
                $count++;
            }

            return back()->with('success', "Reminder sent to <strong>{$count}</strong> approved registrant(s).");
        }

        // GET: show send form
        $registrants = Registrant::approved()->orderBy('name')->get();
        return view('admin.templates.send-reminder', compact('template', 'registrants'));
    }
}
