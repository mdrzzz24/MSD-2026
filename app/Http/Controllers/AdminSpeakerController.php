<?php

namespace App\Http\Controllers;

use App\Models\Speaker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSpeakerController extends Controller
{
    public function index()
    {
        $speakers = Speaker::orderBy('name')->get();
        return view('admin.speakers.index', compact('speakers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'title'   => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'photo'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'bio'     => ['nullable', 'string', 'max:2000'],
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('speakers', 'public');
        }

        Speaker::create($validated + ['is_active' => true]);

        return redirect()->route('admin.speakers.index')
            ->with('success', 'Speaker <strong>' . e($validated['name']) . '</strong> created.');
    }

    public function update(Request $request, Speaker $speaker)
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'title'   => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'photo'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'bio'     => ['nullable', 'string', 'max:2000'],
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($speaker->photo && Storage::disk('public')->exists($speaker->photo)) {
                Storage::disk('public')->delete($speaker->photo);
            }
            $validated['photo'] = $request->file('photo')->store('speakers', 'public');
        } elseif ($request->input('remove_photo')) {
            // Remove photo
            if ($speaker->photo && Storage::disk('public')->exists($speaker->photo)) {
                Storage::disk('public')->delete($speaker->photo);
            }
            $validated['photo'] = null;
        }

        $speaker->update($validated);

        return redirect()->route('admin.speakers.index')
            ->with('success', 'Speaker <strong>' . e($speaker->name) . '</strong> updated.');
    }

    public function destroy(Speaker $speaker)
    {
        $name = $speaker->name;
        // Delete photo file
        if ($speaker->photo && Storage::disk('public')->exists($speaker->photo)) {
            Storage::disk('public')->delete($speaker->photo);
        }
        $speaker->agendaItems()->detach();
        $speaker->delete();

        return redirect()->route('admin.speakers.index')
            ->with('success', 'Speaker <strong>' . e($name) . '</strong> deleted.');
    }

    public function toggle(Speaker $speaker)
    {
        $speaker->update(['is_active' => !$speaker->is_active]);
        return back()->with('success', 'Speaker status toggled.');
    }
}
