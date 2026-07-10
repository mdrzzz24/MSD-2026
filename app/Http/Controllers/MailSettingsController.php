<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class MailSettingsController extends Controller
{
    /**
     * Show mail configuration form.
     */
    public function edit()
    {
        $config = [
            'mailer'      => env('MAIL_MAILER', 'smtp'),
            'host'        => env('MAIL_HOST', ''),
            'port'        => env('MAIL_PORT', '587'),
            'username'    => env('MAIL_USERNAME', ''),
            'password'    => env('MAIL_PASSWORD', ''),
            'encryption'  => env('MAIL_ENCRYPTION', 'tls'),
            'from_address'=> env('MAIL_FROM_ADDRESS', ''),
            'from_name'   => env('MAIL_FROM_NAME', ''),
        ];

        return view('admin.mail-settings', compact('config'));
    }

    /**
     * Update mail configuration in .env file.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'mailer'       => ['required', 'in:smtp,ses,mailgun,postmark,sendmail,log,array'],
            'host'         => ['nullable', 'string', 'max:255'],
            'port'         => ['nullable', 'string', 'max:10'],
            'username'     => ['nullable', 'string', 'max:255'],
            'password'     => ['nullable', 'string', 'max:255'],
            'encryption'   => ['nullable', 'in:tls,ssl,null'],
            'from_address' => ['required', 'email', 'max:255'],
            'from_name'    => ['required', 'string', 'max:255'],
        ]);

        $envPath = base_path('.env');

        if (!file_exists($envPath) || !is_writable($envPath)) {
            return back()->with('error', 'File .env tidak dapat ditulis. Periksa permission.');
        }

        $envContent = file_get_contents($envPath);

        $envContent = $this->setEnvValue($envContent, 'MAIL_MAILER', $validated['mailer']);
        $envContent = $this->setEnvValue($envContent, 'MAIL_HOST', $validated['host'] ?? '');
        $envContent = $this->setEnvValue($envContent, 'MAIL_PORT', $validated['port'] ?? '');
        $envContent = $this->setEnvValue($envContent, 'MAIL_USERNAME', $validated['username'] ?? '');
        $envContent = $this->setEnvValue($envContent, 'MAIL_PASSWORD', $validated['password'] ?? '');
        $envContent = $this->setEnvValue($envContent, 'MAIL_ENCRYPTION', $validated['encryption'] ?? 'null');
        $envContent = $this->setEnvValue($envContent, 'MAIL_FROM_ADDRESS', $validated['from_address']);
        $envContent = $this->setEnvValue($envContent, 'MAIL_FROM_NAME', '"' . $validated['from_name'] . '"');

        file_put_contents($envPath, $envContent);

        // Clear config cache
        Artisan::call('config:clear');

        return back()->with('success', 'Mail configuration updated successfully.');
    }

    /**
     * Set or update a key-value pair in .env content.
     */
    private function setEnvValue(string $content, string $key, string $value): string
    {
        $pattern = '/^' . preg_quote($key, '/') . '=.*$/m';

        if (preg_match($pattern, $content)) {
            return preg_replace($pattern, $key . '=' . $value, $content);
        }

        // Key not found, append
        return $content . "\n" . $key . '=' . $value;
    }

    /**
     * Send a test email to verify configuration.
     */
    public function test(Request $request)
    {
        $request->validate([
            'test_email' => ['required', 'email'],
        ]);

        try {
            \Illuminate\Support\Facades\Mail::raw(
                'Ini adalah email test dari Metrodata Solution Day 2026. Konfigurasi email Anda berfungsi dengan baik!',
                function ($message) use ($request) {
                    $message->to($request->test_email)
                            ->subject('Test Email — Metrodata Solution Day 2026');
                }
            );

            return back()->with('success', "Test email sent to <strong>{$request->test_email}</strong>. Check inbox/spam.");
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }
}
