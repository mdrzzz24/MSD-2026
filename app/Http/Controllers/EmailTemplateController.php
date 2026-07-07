<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    /**
     * Show all email templates.
     */
    public function index()
    {
        $templates = EmailTemplate::latest()->get();
        return view('admin.templates.index', compact('templates'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('admin.templates.create');
    }

    /**
     * Store a new template.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'type'         => ['required', 'in:approval,rejection'],
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
        return view('admin.templates.edit', compact('template'));
    }

    /**
     * Update a template.
     */
    public function update(Request $request, EmailTemplate $template)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'type'         => ['required', 'in:approval,rejection'],
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
        return view('admin.templates.upload');
    }

    /**
     * Handle HTML file upload.
     */
    public function upload(Request $request)
    {
        $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'type'    => ['required', 'in:approval,rejection'],
            'subject' => ['required', 'string', 'max:255'],
            'html_file' => ['required', 'file', 'mimes:html,htm', 'max:2048'],
        ]);

        $htmlContent = file_get_contents($request->file('html_file')->getRealPath());

        EmailTemplate::create([
            'name'         => $request->input('name'),
            'type'         => $request->input('type'),
            'subject'      => $request->input('subject'),
            'html_content' => $htmlContent,
            'is_active'    => true,
        ]);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Email template uploaded successfully.');
    }
}
