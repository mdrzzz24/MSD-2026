<?php

namespace App\Http\Controllers;

use App\Mail\TemplateMail;
use App\Models\EmailTemplate;
use App\Models\Registrant;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
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
     * Handle HTML file or ZIP (Word export) upload.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'type'        => ['required', Rule::in(array_keys(EmailTemplate::types()))],
            'description' => ['nullable', 'string', 'max:500'],
            'subject'     => ['required', 'string', 'max:255'],
            'html_file'   => ['required', 'file', 'mimes:html,htm,zip', 'max:10240'],
        ]);

        $file = $request->file('html_file');

        // ── ZIP upload (Word HTML export with _files folder) ──
        if ($file->getClientOriginalExtension() === 'zip') {
            return $this->handleZipUpload($request, $file);
        }

        // ── Single HTML/HTM file upload ──
        $htmlContent = file_get_contents($file->getRealPath());

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
     * Handle ZIP file containing Word HTML export (.htm + _files/ folder).
     */
    private function handleZipUpload(Request $request, $file)
    {
        $zip = new \ZipArchive();
        $res = $zip->open($file->getRealPath());

        if ($res !== true) {
            return back()->with('error', 'Cannot open ZIP file. Please ensure it is a valid archive.');
        }

        // Extract to a temporary directory
        $tempDir = storage_path('app/tmp/email_upload_' . uniqid());
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        $zip->extractTo($tempDir);
        $zip->close();

        // Find the .htm/.html file (search recursively)
        $htmFiles = $this->findHtmFiles($tempDir);
        if (empty($htmFiles)) {
            $this->cleanupTempDir($tempDir);
            return back()->with('error', 'No .htm file found in the ZIP archive. Pastikan Anda meng-"Compress" file .htm beserta folder _files-nya.');
        }

        $htmPath = $htmFiles[0];
        $htmDir = dirname($htmPath);
        $htmlContent = file_get_contents($htmPath);

        // Word HTML export uses Windows-1252 encoding.
        // Convert to UTF-8 before storing in database (which uses utf8mb4).
        $htmlContent = mb_convert_encoding($htmlContent, 'UTF-8', 'Windows-1252');
        // Remove any leftover invalid UTF-8 sequences
        $htmlContent = iconv('UTF-8', 'UTF-8//IGNORE', $htmlContent);

        // Find the _files folder (search in same dir and subdirs)
        $filesDir = null;
        $baseName = pathinfo($htmPath, PATHINFO_FILENAME);
        $candidate = $htmDir . '/' . $baseName . '_files';
        if (is_dir($candidate)) {
            $filesDir = $candidate;
        } else {
            // Try any directory ending in _files
            $dirs = glob($htmDir . '/*_files', GLOB_ONLYDIR);
            if (!empty($dirs)) {
                $filesDir = $dirs[0];
            } else {
                // Search recursively for any _files directory
                $allDirs = $this->findDirsSuffix($tempDir, '_files');
                if (!empty($allDirs)) {
                    $filesDir = $allDirs[0];
                }
            }
        }

        if ($filesDir) {
            // Copy images to public storage and rewrite paths
            $storageDir = 'email-templates/' . uniqid();
            $publicDir = storage_path('app/public/' . $storageDir);
            if (!is_dir($publicDir)) {
                mkdir($publicDir, 0755, true);
            }

            // Copy all images from _files to public storage
            $imageFiles = glob($filesDir . '/*.{jpg,jpeg,png,gif,bmp,webp}', GLOB_BRACE);
            $imageMap = [];

            foreach ($imageFiles as $imgPath) {
                $imgName = basename($imgPath);
                copy($imgPath, $publicDir . '/' . $imgName);
                $imageMap[$imgName] = Storage::url($storageDir . '/' . $imgName);
            }

            // Rewrite ALL image paths (both <img> and VML imagedata) to absolute URLs
            foreach ($imageMap as $imgName => $publicUrl) {
                $htmlContent = preg_replace(
                    '/src="[^"]*' . preg_quote($imgName, '#') . '"/i',
                    'src="' . $publicUrl . '"',
                    $htmlContent
                );
            }

            // Clean up files dir references in links (filelist.xml, themedata, etc.)
            // These Word XML references are not needed for rendering
            $htmlContent = preg_replace(
                '/href="[^"]*_files\/[^"]*"/i',
                'href="#"',
                $htmlContent
            );
        }

        // Clean up temp directory
        $this->cleanupTempDir($tempDir);

        EmailTemplate::create([
            'name'         => $request->input('name'),
            'type'         => $request->input('type'),
            'description'  => $request->input('description'),
            'subject'      => $request->input('subject'),
            'html_content' => $htmlContent,
            'is_active'    => true,
        ]);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Email template uploaded from ZIP successfully. Images have been stored and linked.');
    }

    /**
     * Recursively find all .htm/.html files in a directory.
     */
    private function findHtmFiles(string $dir): array
    {
        $result = [];
        $it = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($it);
        foreach ($files as $f) {
            $ext = strtolower($f->getExtension());
            if ($ext === 'htm' || $ext === 'html') {
                $result[] = $f->getRealPath();
            }
        }
        return $result;
    }

    /**
     * Recursively find all directories ending with a given suffix.
     */
    private function findDirsSuffix(string $dir, string $suffix): array
    {
        $result = [];
        $it = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
        $dirs = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::SELF_FIRST);
        foreach ($dirs as $d) {
            if ($d->isDir() && str_ends_with($d->getFilename(), $suffix)) {
                $result[] = $d->getRealPath();
            }
        }
        return $result;
    }

    /**
     * Recursively delete a temporary directory.
     */
    private function cleanupTempDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $it = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $f) {
            $f->isDir() ? @rmdir($f->getRealPath()) : @unlink($f->getRealPath());
        }
        @rmdir($dir);
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
