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

        // Determine which types have an active template and which use fallback
        $activeTypes = $templates->where('is_active', true)->pluck('type')->toArray();
        $fallbackViews = [
            'registration'       => 'emails.registration-clean',
            'approval'           => 'emails.registrant-approved',
            'rejection'          => 'emails.registrant-rejected',
            'workshop_approval'  => 'emails.workshop-approved',
            'workshop_rejection' => 'emails.workshop-rejected',
            'track_approval'     => 'emails.track-approved',
            'track_rejection'    => 'emails.track-rejected',
            'reminder'           => 'emails.reminder',
        ];

        return view('admin.templates.index', compact('templates', 'types', 'activeTypes', 'fallbackViews'));
    }

    /**
     * Show create form.
     */
    public function create(Request $request)
    {
        $types = EmailTemplate::types();

        // Pre-fill type and subject from query params (e.g. from fallback "Create template" link)
        $presetType = $request->query('type');
        $presetSubject = $request->query('subject');
        $presetHtml = $request->query('html'); // base64-encoded HTML from fallback

        if ($presetHtml) {
            $presetHtml = base64_decode($presetHtml);
        }

        return view('admin.templates.create', compact('types', 'presetType', 'presetSubject', 'presetHtml'));
    }

    /**
     * Show create form pre-filled from a Blade fallback view.
     */
    public function createFromFallback(Request $request)
    {
        $types = EmailTemplate::types();

        $typeKey = $request->query('type');
        if (!$typeKey || !isset($types[$typeKey])) {
            return redirect()->route('admin.templates.create')
                ->with('error', 'Invalid template type.');
        }

        // Map types to Blade views
        $viewMap = $this->getFallbackViewMap();
        $viewName = $viewMap[$typeKey] ?? null;
        if (!$viewName || !view()->exists($viewName)) {
            return redirect()->route('admin.templates.create', ['type' => $typeKey])
                ->with('error', 'Fallback view not found for this type.');
        }

        // Render the Blade view with sample placeholder data
        $sampleRegistrant = new \App\Models\Registrant();
        $sampleRegistrant->name = '{{ name }}';
        $sampleRegistrant->display_name = '{{ name }}';
        $sampleRegistrant->email = '{{ email }}';

        $rendered = view($viewName, [
            'registrant'       => $sampleRegistrant,
            'plainPassword'    => '{{ password }}',
            'workshopName'     => '{{ workshop_name }}',
            'workshop_title'   => '{{ workshop_title }}',
            'workshop_room'    => '{{ workshop_room }}',
            'workshop_time'    => '{{ workshop_time }}',
            'workshop_capacity'=> '{{ workshop_capacity }}',
            'sessionName'      => '{{ track_name }}',
            'adminNotes'       => '{{ admin_notes }}',
            'name'             => '{{ name }}',
        ])->render();

        $presetHtml    = $rendered;
        $presetSubject = match ($typeKey) {
            'workshop_approval',
            'workshop_rejection' => '[CONFIRMATION] Thank you for your Registration : MSD 2026 | {{ workshop_name }} | {{ workshop_date }} | Shangri-La Hotel Jakarta | {{ workshop_room }}',
            default              => $types[$typeKey]['label'] ?? '',
        };
        $presetType    = $typeKey;

        return view('admin.templates.create', compact('types', 'presetType', 'presetSubject', 'presetHtml'));
    }

    /**
     * Map template types to fallback Blade view names.
     */
    private function getFallbackViewMap(): array
    {
        return [
            'registration'       => 'emails.registration-clean',
            'approval'           => 'emails.registrant-approved',
            'rejection'          => 'emails.registrant-rejected',
            'workshop_approval'  => 'emails.workshop-approved',
            'workshop_rejection' => 'emails.workshop-rejected',
            'track_approval'     => 'emails.track-approved',
            'track_rejection'    => 'emails.track-rejected',
            'reminder'           => 'emails.reminder',
        ];
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
            'html_file'   => ['required', 'file', 'mimes:html,htm,zip,eml', 'max:10240'],
        ]);

        $file = $request->file('html_file');
        $ext = strtolower($file->getClientOriginalExtension());

        // ── ZIP upload (Word HTML export with _files folder) ──
        if ($ext === 'zip') {
            return $this->handleZipUpload($request, $file);
        }

        // ── .eml file upload (email export from Outlook, Thunderbird, etc.) ──
        if ($ext === 'eml') {
            return $this->handleEmlUpload($request, $file);
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
     * Handle .eml file upload — extract HTML body and inline images.
     */
    private function handleEmlUpload(Request $request, $file)
    {
        $raw = file_get_contents($file->getRealPath());

        // Parse MIME headers and body
        $parsed = $this->parseEml($raw);

        if (empty($parsed['html'])) {
            // Fallback: if Content-Type is text/html, try using raw body directly
            $ct = $parsed['_debug_content_type'] ?? '';
            if (stripos($ct, 'text/html') === 0) {
                $htmlContent = file_get_contents($file->getRealPath());
                // Remove headers (everything before first blank line)
                $htmlContent = preg_replace('/^.*?\r?\n\r?\n/s', '', $htmlContent);
            } else {
                return back()->with('error', 'No HTML content found in the .eml file. Detected Content-Type: "' . $ct . '"');
            }
        } else {
            $htmlContent = $parsed['html'];
        }

        // If subject was not manually provided, use the one from the .eml
        $subject = $request->input('subject') ?: $parsed['subject'];

        // Handle inline images (cid: references) — save to storage and use absolute URLs
        // (Gmail blocks data: URIs, so we must serve images from a web-accessible path)
        if (!empty($parsed['images'])) {
            $storageDir = 'email-templates/' . uniqid();
            $publicDir = storage_path('app/public/' . $storageDir);
            if (!is_dir($publicDir)) {
                mkdir($publicDir, 0755, true);
            }

            foreach ($parsed['images'] as $cid => $imageData) {
                // Determine file extension from MIME type
                $ext = match ($imageData['type']) {
                    'image/jpeg', 'image/jpg' => 'jpg',
                    'image/png'                => 'png',
                    'image/gif'                => 'gif',
                    'image/webp'               => 'webp',
                    'image/bmp'                => 'bmp',
                    'image/svg+xml'            => 'svg',
                    default                    => 'bin',
                };
                $imgName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $cid) . '.' . $ext;
                file_put_contents($publicDir . '/' . $imgName, $imageData['data']);
                $publicUrl = \Illuminate\Support\Facades\Storage::url($storageDir . '/' . $imgName);

                // Replace cid: references with the public URL
                $htmlContent = preg_replace(
                    '/src="\s*cid:\s*' . preg_quote($cid, '/') . '\s*"/i',
                    'src="' . $publicUrl . '"',
                    $htmlContent
                );
                $htmlContent = preg_replace(
                    "/src='\s*cid:\s*" . preg_quote($cid, '/') . "\s*'/i",
                    "src='" . $publicUrl . "'",
                    $htmlContent
                );
            }
        }

        // Remove any remaining unmatched cid: references (images without embedded data)
        $htmlContent = preg_replace('/src="\s*cid:[^"]*"/i', 'src=""', $htmlContent);
        $htmlContent = preg_replace("/src='\s*cid:[^']*'/i", "src=''", $htmlContent);
        // Also handle url(cid:...) in CSS
        $htmlContent = preg_replace('/url\(\s*cid:[^)]+\)/i', 'url()', $htmlContent);

        EmailTemplate::create([
            'name'         => $request->input('name'),
            'type'         => $request->input('type'),
            'description'  => $request->input('description'),
            'subject'      => $subject,
            'html_content' => $htmlContent,
            'is_active'    => true,
        ]);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Email template uploaded from .eml successfully. Inline images have been embedded.');
    }

    /**
     * Parse a raw .eml (MIME) message and extract HTML body, subject, and inline images.
     */
    private function parseEml(string $raw): array
    {
        $result = [
            'subject' => '',
            'html'    => '',
            'images'  => [], // cid => ['data' => ..., 'type' => ...]
            '_debug_content_type' => '',
        ];

        // Split headers and body at the first blank line (before unfolding)
        $parts = preg_split("/\r\n\r\n|\n\n/", $raw, 2);
        if (count($parts) < 2) {
            return $result;
        }

        $headers = $parts[0];
        $body    = $parts[1];

        // Unfold MIME headers (join continuation lines — lines starting with space/tab)
        $headers = preg_replace('/\r?\n[ \t]+/', ' ', $headers);

        // Detect the main Content-Type from email headers
        $mainContentType = 'text/plain';
        if (preg_match('/^Content-Type:\s*(.+)$/im', $headers, $m)) {
            $mainContentType = trim($m[1]);
        }
        $result['_debug_content_type'] = $mainContentType;

        // Detect boundary for multipart emails
        $boundary = '';
        if (preg_match('/boundary="?([^"\s;]+)"?\s*$/im', $mainContentType, $bm)) {
            $boundary = $bm[1];
        }

        if ($boundary) {
            $this->parseMimePartsByBoundary($body, $boundary, $result);
        } elseif (stripos($mainContentType, 'text/html') === 0) {
            // Simple email with direct HTML body (no multipart)
            $encoding = '7bit';
            if (preg_match('/^Content-Transfer-Encoding:\s*(.+)$/im', $headers, $m)) {
                $encoding = strtolower(trim($m[1]));
            }
            switch ($encoding) {
                case 'base64':
                    $decoded = base64_decode($body);
                    break;
                case 'quoted-printable':
                    $decoded = quoted_printable_decode($body);
                    break;
                default:
                    $decoded = $body;
            }
            $result['html'] = $decoded;
        } else {
            // Try parsing as a single MIME part (with its own headers)
            $this->parseSinglePart($body, $result);
        }

        return $result;
    }

    /**
     * Split a MIME body by boundary and recursively parse each part.
     */
    private function parseMimePartsByBoundary(string $data, string $boundary, array &$result): void
    {
        $parts = preg_split("/--" . preg_quote($boundary, '/') . "(?:--)?\s*\r?\n?/", $data);
        // Remove preamble (anything before first boundary)
        if (!empty($parts)) {
            array_shift($parts);
        }
        // Remove trailing part after closing boundary (if any)
        if (!empty($parts) && trim(end($parts)) === '') {
            array_pop($parts);
        }
        foreach ($parts as $part) {
            $this->parseSinglePart($part, $result);
        }
    }

    /**
     * Parse a single MIME part (headers + body) and extract HTML/images.
     */
    private function parseSinglePart(string $data, array &$result): void
    {
        // Split part into headers and body
        $split = preg_split("/\r\n\r\n|\n\n/", $data, 2);
        $partHeaders = $split[0] ?? '';
        $partBody   = $split[1] ?? '';

        if (empty($partHeaders)) {
            return;
        }

        // Unfold part headers (join continuation lines)
        $partHeaders = preg_replace('/\r?\n[ \t]+/', ' ', $partHeaders);

        // Extract Content-Type
        $contentType = 'text/plain';
        if (preg_match('/^Content-Type:\s*(.+)$/im', $partHeaders, $m)) {
            $contentType = trim($m[1]);
        }

        // If multipart, recurse with its own boundary
        if (preg_match('/^multipart\/\w+;\s*boundary="?([^"\s;]+)"?\s*$/im', $contentType, $bm)) {
            $this->parseMimePartsByBoundary($partBody, $bm[1], $result);
            return;
        }

        // Extract Content-Transfer-Encoding
        $encoding = '7bit';
        if (preg_match('/^Content-Transfer-Encoding:\s*(.+)$/im', $partHeaders, $m)) {
            $encoding = strtolower(trim($m[1]));
        }

        // Extract Content-ID for inline images
        $contentId = '';
        if (preg_match('/^Content-ID:\s*<([^>]+)>/im', $partHeaders, $m)) {
            $contentId = trim($m[1]);
        }

        // Decode the body
        switch ($encoding) {
            case 'base64':
                $decoded = base64_decode($partBody);
                break;
            case 'quoted-printable':
                $decoded = quoted_printable_decode($partBody);
                break;
            default:
                $decoded = $partBody;
        }

        // Image part with Content-ID → store for inline embedding
        if ($contentId && strpos($contentType, 'image/') === 0) {
            // Extract clean MIME type (strip parameters like ; name="...")
            $cleanType = preg_replace('/\s*;.*$/', '', $contentType);
            $result['images'][$contentId] = [
                'data' => $decoded,
                'type' => trim($cleanType),
            ];
            return;
        }

        // HTML part → store as main content
        if (strpos($contentType, 'text/html') === 0) {
            $result['html'] = $decoded;
            return;
        }
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
            'name'              => 'John Doe',
            'email'             => 'john@example.com',
            'status'            => 'approved',
            'unique_code'       => '100724080000',
            'admin_notes'       => 'Sample admin note.',
            'password'          => '123456789',
            'workshop_name'     => 'Sample Workshop Name',
            'workshop_title'    => 'Sample Workshop Title',
            'workshop_room'     => 'Meeting Room A',
            'workshop_date'     => 'Thursday, 20 August 2026',
            'workshop_time'     => '09:00 – 12:00',
            'workshop_capacity' => '35',
            'venue_name'        => 'Shangri-La Hotel Jakarta',
            'track_name'        => 'Sample Track',
            'event_date'        => '12 Agustus 2026',
            'login_url'         => route('registrant.login'),
            'qr_code'           => '<img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=sample-qr-data" alt="QR Code" style="width:200px;height:200px;display:block;margin:16px auto;">',
            'qr_checkin_url'    => route('registrant.login'),
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
