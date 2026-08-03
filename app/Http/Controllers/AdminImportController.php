<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ClientConfirmationImporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Super-admin tool to upload a client's xlsx report (approve/reject/waiting list)
 * and record those confirmations as client markings — status is NOT changed.
 *
 * Flow: upload + preview -> confirm (apply) / cancel.
 */
class AdminImportController extends Controller
{
    public function __construct(private readonly ClientConfirmationImporter $importer)
    {
    }

    /**
     * Show the import form (or the pending preview if one was uploaded).
     */
    public function index()
    {
        $this->authorizeSuperAdmin();

        $clients = User::where('role', 'client')->orderBy('name')->get(['id', 'name', 'email']);
        $pending = session('client_confirmation_import');

        // Default: the client whose report is most likely being uploaded (Rozan), else the first client.
        $selectedClientId = $clients->first(fn ($c) => str_contains(mb_strtolower($c->name), 'rozan'))?->id
            ?? $clients->first()?->id;

        return view('admin.import-client-confirmations', compact('clients', 'pending', 'selectedClientId'));
    }

    /**
     * Upload the xlsx, parse it and stage a preview in the session (no DB writes).
     */
    public function preview(Request $request)
    {
        $this->authorizeSuperAdmin();

        $validated = $request->validate([
            'file'      => ['required', 'file', 'mimes:xlsx', 'max:10240'],
            'client_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $rows = $this->importer->parseFile($validated['file']->getRealPath());

        if (count($rows) === 0) {
            return back()
                ->withInput()
                ->with('error', 'File Excel tidak memiliki baris data. Pastikan kolom <strong>BUSINESS EMAIL</strong>, <strong>FULL NAME</strong>, dan <strong>APPROVAL STATUS</strong> ada.');
        }

        $preview = $this->importer->buildPreview($rows, (int) $validated['client_id']);
        $preview['file_name'] = $validated['file']->getClientOriginalName();
        $preview['client_name'] = User::find($validated['client_id'])->name ?? 'Client';

        session(['client_confirmation_import' => $preview]);

        return redirect()->route('admin.import-client-confirmations');
    }

    /**
     * Apply the staged preview to the registrants (client markings only).
     */
    public function apply(Request $request)
    {
        $this->authorizeSuperAdmin();

        $preview = session('client_confirmation_import');

        if (! is_array($preview) || empty($preview['to_apply'])) {
            return redirect()->route('admin.import-client-confirmations')
                ->with('error', 'Tidak ada data import yang menunggu. Silakan upload file Excel terlebih dahulu.');
        }

        $clientId = (int) ($preview['client_id'] ?? $request->integer('client_id'));
        $counts = $this->importer->apply($preview['to_apply'], $clientId);

        session()->forget('client_confirmation_import');

        $clientName = User::find($clientId)->name ?? 'Client';

        return redirect()->route('admin.import-client-confirmations')->with('success',
            "Import selesai! <strong>✅ Approve:</strong> {$counts['approve']} · "
            . "<strong>❌ Reject:</strong> {$counts['reject']} · "
            . "<strong>⏳ Waiting List:</strong> {$counts['waitlist']} "
            . "— dicatat atas nama <strong>{$clientName}</strong>."
        );
    }

    /**
     * Discard the staged preview without applying anything.
     */
    public function cancel(Request $request)
    {
        $this->authorizeSuperAdmin();

        session()->forget('client_confirmation_import');

        return redirect()->route('admin.import-client-confirmations')
            ->with('info', 'Import dibatalkan, tidak ada data yang diubah.');
    }

    /**
     * This tool is only available to super admins.
     */
    private function authorizeSuperAdmin(): void
    {
        abort_unless(Auth::user()?->isSuperAdmin(), 403, 'Only super admin can import client confirmations.');
    }
}
