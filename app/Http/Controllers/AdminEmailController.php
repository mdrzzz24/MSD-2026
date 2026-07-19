<?php

namespace App\Http\Controllers;

use App\Models\AdminEmail;
use App\Models\EmailTemplate;
use App\Models\Registrant;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminEmailController extends Controller
{
    /**
     * Display list of admin email recipients.
     */
    public function index()
    {
        $adminEmails = AdminEmail::orderBy('name')->paginate(20);
        return view('admin.admin-emails.index', compact('adminEmails'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('admin.admin-emails.create');
    }

    /**
     * Store a new admin email.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admin_emails,email',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        AdminEmail::create($data);

        return redirect()->route('admin.admin-emails.index')
            ->with('success', 'Admin email ' . $data['email'] . ' berhasil ditambahkan.');
    }

    /**
     * Show edit form.
     */
    public function edit(AdminEmail $adminEmail)
    {
        return view('admin.admin-emails.edit', compact('adminEmail'));
    }

    /**
     * Update an admin email.
     */
    public function update(Request $request, AdminEmail $adminEmail)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admin_emails,email,' . $adminEmail->id,
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $adminEmail->update($data);

        return redirect()->route('admin.admin-emails.index')
            ->with('success', 'Admin email berhasil diperbarui.');
    }

    /**
     * Delete an admin email.
     */
    public function destroy(AdminEmail $adminEmail)
    {
        $adminEmail->delete();

        return redirect()->route('admin.admin-emails.index')
            ->with('success', 'Admin email berhasil dihapus.');
    }

    /**
     * Send a test email to selected admin email(s) for a specific template type.
     * Sends directly (synchronous).
     */
    public function sendTest(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:email_templates,id',
            'admin_email_ids' => 'required|array|min:1',
            'admin_email_ids.*' => 'exists:admin_emails,id',
        ]);

        $template = EmailTemplate::findOrFail($request->template_id);
        $adminEmails = AdminEmail::whereIn('id', $request->admin_email_ids)->get();

        if ($adminEmails->isEmpty()) {
            return back()->with('error', 'Pilih minimal satu admin email penerima.');
        }

        $successCount = 0;
        $errors = [];

        foreach ($adminEmails as $ae) {
            try {
                $sampleToken = 'TEST' . strtolower(substr(md5(uniqid((string) mt_rand(), true)), 0, 12));
                $sampleQrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($sampleToken);
                $sampleCheckinUrl = route('registrant.qr-scan', $sampleToken);

                $renderData = [
                    'name' => $ae->name,
                    'email' => $ae->email,
                    'status' => 'approved',
                    'password' => 'TestPass123',
                    'unique_code' => 'TEST123456',
                    'admin_notes' => 'This is a test email sent from admin panel.',
                    'workshop_name' => 'Sample Workshop Session',
                    'workshop_title' => 'Sample Workshop Topic',
                    'workshop_room' => 'Meeting Room A',
                    'workshop_date' => 'Thursday, 20 August 2026',
                    'workshop_time' => '09:00 – 12:00',
                    'workshop_capacity' => '35',
                    'venue_name' => 'Shangri-La Hotel Jakarta',
                    'track_name' => 'Sample Track',
                    'event_date' => now()->format('d F Y'),
                    'login_url' => route('login'),
                    'qr_code' => '<img src="' . $sampleQrUrl . '" alt="QR Code" style="width:150px;height:150px;display:block;margin:16px auto;">',
                    'qr_checkin_url' => $sampleCheckinUrl,
                ];

                $htmlContent = $template->render($renderData);
                $renderedSubject = $template->renderSubject($renderData);

                Mail::send('emails.html-wrapper', ['htmlContent' => $htmlContent], function ($message) use ($ae, $renderedSubject) {
                    $message->to($ae->email, $ae->name)
                        ->subject('[TEST] ' . $renderedSubject);
                });

                $successCount++;
            } catch (\Exception $e) {
                $errors[] = $ae->email . ': ' . $e->getMessage();
            }
        }

        if ($successCount > 0) {
            $msg = "Test email berhasil dikirim ke {$successCount} penerima.";
            if (!empty($errors)) {
                $msg .= ' Namun ' . count($errors) . ' gagal: ' . implode('; ', $errors);
            }
            return back()->with('success', $msg);
        }

        return back()->with('error', 'Gagal mengirim test email: ' . implode('; ', $errors));
    }
}
