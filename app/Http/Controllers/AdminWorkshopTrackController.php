<?php

namespace App\Http\Controllers;

use App\Models\Speaker;
use App\Models\Track;
use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminWorkshopTrackController extends Controller
{
    private function checkPermission(): void
    {
        if (!Auth::user()->hasPermission('workshops')) {
            abort(403, 'You do not have permission to manage tracks.');
        }
    }

    private function verifyOwnership(Workshop $workshop, Track $track): void
    {
        if ($track->workshop_id !== $workshop->id) {
            abort(404, 'Track not found in this workshop.');
        }
    }

    /**
     * Show tracks for a workshop.
     */
    public function index(Workshop $workshop)
    {
        $this->checkPermission();
        $tracks = $workshop->tracks()->with('speakers')->orderBy('name')->get();
        $allSpeakers = Speaker::orderBy('name')->get();
        $trackData = $tracks->map(fn($t) => [
            'id' => $t->id,
            'name' => $t->name,
            'title' => $t->title,
            'description' => $t->description,
            'start_time' => $t->start_time,
            'end_time' => $t->end_time,
            'speaker_ids' => $t->speakers->pluck('id')->toArray(),
        ])->values();
        return view('admin.workshops.tracks', compact('workshop', 'tracks', 'allSpeakers', 'trackData'));
    }

    /**
     * Store a new track within a workshop.
     */
    public function store(Request $request, Workshop $workshop)
    {
        $this->checkPermission();
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'title'       => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
            'start_time'  => ['nullable', 'string', 'max:10'],
            'end_time'    => ['nullable', 'string', 'max:10'],
            'speaker_ids' => ['nullable', 'array'],
            'speaker_ids.*' => ['exists:speakers,id'],
        ]);

        $track = $workshop->tracks()->create([
            'name'        => $validated['name'],
            'title'       => $validated['title'] ?? $validated['name'],
            'description' => $validated['description'] ?? null,
            'start_time'  => $validated['start_time'] ?? null,
            'end_time'    => $validated['end_time'] ?? null,
            'is_active'   => true,
        ]);

        // Attach speakers
        if (!empty($validated['speaker_ids'])) {
            $sync = [];
            foreach ($validated['speaker_ids'] as $i => $speakerId) {
                $sync[$speakerId] = ['order' => $i];
            }
            $track->speakers()->sync($sync);
        }

        return back()->with('success', "Track <strong>{$track->name}</strong> created.");
    }

    /**
     * Update a track.
     */
    public function update(Request $request, Workshop $workshop, Track $track)
    {
        $this->checkPermission();
        $this->verifyOwnership($workshop, $track);

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'title'       => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:65535'],
            'start_time'  => ['nullable', 'string', 'max:10'],
            'end_time'    => ['nullable', 'string', 'max:10'],
            'speaker_ids' => ['nullable', 'array'],
            'speaker_ids.*' => ['exists:speakers,id'],
        ]);

        $track->update([
            'name'        => $validated['name'],
            'title'       => $validated['title'] ?? $validated['name'],
            'description' => $validated['description'] ?? null,
            'start_time'  => $validated['start_time'] ?? null,
            'end_time'    => $validated['end_time'] ?? null,
        ]);

        // Sync speakers
        if ($request->has('speaker_ids')) {
            $sync = [];
            foreach (($validated['speaker_ids'] ?? []) as $i => $speakerId) {
                $sync[$speakerId] = ['order' => $i];
            }
            $track->speakers()->sync($sync);
        }

        return back()->with('success', "Track <strong>{$track->name}</strong> updated.");
    }

    /**
     * Toggle track active status.
     */
    public function toggle(Workshop $workshop, Track $track)
    {
        $this->checkPermission();
        $this->verifyOwnership($workshop, $track);

        $track->update(['is_active' => !$track->is_active]);

        return back()->with('success', "Track <strong>{$track->name}</strong> " . ($track->is_active ? 'activated' : 'deactivated') . ".");
    }

    /**
     * Delete a track.
     */
    public function destroy(Workshop $workshop, Track $track)
    {
        $this->checkPermission();
        $this->verifyOwnership($workshop, $track);

        $name = $track->name;
        $track->delete();

        return back()->with('success', "Track <strong>{$name}</strong> deleted.");
    }
}
