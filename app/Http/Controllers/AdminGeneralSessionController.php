<?php

namespace App\Http\Controllers;

use App\Models\AgendaItem;
use Illuminate\Http\Request;

class AdminGeneralSessionController extends Controller
{
    // ── Scope: only items where category='general' or agenda_type='keynote' ──

    public function index()
    {
        $items = AgendaItem::orderBy('created_at')
            ->where(function ($q) {
                $q->where('category', 'general')
                  ->orWhere('agenda_type', 'keynote');
            })
            ->with('speakers')
            ->get();

        return view('admin.general-sessions.index', compact('items'));
    }

    public function create()
    {
        $rooms   = \App\Models\Room::ordered()->get();
        $tracks  = \App\Models\Track::orderBy('title')->get(['id', 'name', 'title', 'description']);
        $speakers = \App\Models\Speaker::orderBy('name')->get(['id', 'name', 'title', 'company', 'photo']);

        return view('admin.general-sessions.create', compact('rooms', 'tracks', 'speakers'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateItem($request);

        $validated['category']    = 'general';
        $validated['workshop_id'] = null;
        $validated['track_id']    = ($validated['track_id'] ?? '') === '' ? null : $validated['track_id'];
        $validated['is_registrable'] = $request->boolean('is_registrable');
        $validated['start_time']    = $validated['start_time'] ?: '00:00:00';
        $validated['end_time']      = $validated['end_time'] ?: '00:00:00';

        // Full-row → no room
        if ($request->boolean('is_full_row')) {
            $validated['room'] = null;
        }

        $agendaItem = AgendaItem::create($validated);

        // Sync speakers with pivot data
        $this->syncSpeakers($agendaItem, $request);

        return redirect()->route('admin.general-sessions.index')
            ->with('success', 'General Session <strong>' . e($agendaItem->title) . '</strong> created.');
    }

    public function edit(AgendaItem $agendum)
    {
        $rooms   = \App\Models\Room::ordered()->get();
        $tracks  = \App\Models\Track::orderBy('title')->get(['id', 'name', 'title', 'description']);
        $speakers = \App\Models\Speaker::orderBy('name')->get(['id', 'name', 'title', 'company', 'photo']);
        $agendum->load('speakers');

        return view('admin.general-sessions.edit', compact('agendum', 'rooms', 'tracks', 'speakers'));
    }

    public function update(Request $request, AgendaItem $agendum)
    {
        $validated = $this->validateItem($request);

        $validated['category']    = 'general';
        $validated['workshop_id'] = null;
        $validated['track_id']    = ($validated['track_id'] ?? '') === '' ? null : $validated['track_id'];
        $validated['is_registrable'] = $request->boolean('is_registrable');

        // Preserve existing times if not explicitly provided (avoid 00:00:00 causing item to vanish from agenda)
        if (empty($validated['start_time']) && empty($validated['end_time'])) {
            unset($validated['start_time'], $validated['end_time']);
        } else {
            $validated['start_time'] = $validated['start_time'] ?: '00:00:00';
            $validated['end_time']   = $validated['end_time'] ?: '00:00:00';
        }

        if ($request->boolean('is_full_row')) {
            $validated['room'] = null;
        }

        $agendum->update($validated);

        // Re-sync speakers
        $agendum->speakers()->detach();
        $this->syncSpeakers($agendum, $request);

        return redirect()->route('admin.general-sessions.index')
            ->with('success', 'General Session <strong>' . e($agendum->title) . '</strong> updated.');
    }

    public function destroy(AgendaItem $agendum)
    {
        $title = $agendum->title;
        $agendum->delete();

        return redirect()->route('admin.general-sessions.index')
            ->with('success', 'General Session <strong>' . e($title) . '</strong> deleted.');
    }

    // ── Helpers ──

    private function validateItem(Request $request): array
    {
        $validated = $request->validate([
            'title'          => ['required', 'string', 'max:255'],
            'topic_headline' => ['nullable', 'string', 'max:255'],
            'description'    => ['nullable', 'string', 'max:3000'],
            'key_highlights' => ['nullable', 'string', 'max:3000'],
            'room'           => ['nullable', 'string', 'max:100'],
            'start_time'     => ['nullable'],
            'end_time'       => ['nullable'],
            'date'           => ['nullable', 'date'],
            'order'          => ['nullable', 'integer', 'min:0'],
            'rowspan'        => ['nullable', 'integer', 'min:1', 'max:12'],
            'colspan'        => ['nullable', 'integer', 'min:1', 'max:8'],
            'is_registrable' => ['boolean'],
            'capacity'       => ['nullable', 'integer', 'min:0'],
            'track_id'       => ['nullable', 'exists:tracks,id'],
            'speaker_ids'    => ['nullable', 'array'],
            'speaker_ids.*'  => ['exists:speakers,id'],
            'speaker_presentation_title' => ['nullable', 'array'],
            'speaker_presentation_desc'  => ['nullable', 'array'],
        ]);

        // Decode any over-encoded HTML entities so values stay clean
        $validated['title']          = clean_text($validated['title']);
        $validated['topic_headline'] = clean_text($validated['topic_headline'] ?? null);
        $validated['description']    = clean_text($validated['description'] ?? null);
        $validated['key_highlights'] = clean_text($validated['key_highlights'] ?? null);

        return $validated;
    }

    private function syncSpeakers(AgendaItem $agendaItem, Request $request): void
    {
        $speakerIds = $request->input('speaker_ids', []);
        if (empty($speakerIds)) return;

        $syncData = [];
        foreach ($speakerIds as $i => $speakerId) {
            $syncData[$speakerId] = [
                'order'                   => $i + 1,
                'presentation_title'      => $request->input("speaker_presentation_title.{$i}"),
                'presentation_description' => $request->input("speaker_presentation_desc.{$i}"),
            ];
        }

        $agendaItem->speakers()->sync($syncData);
    }
}
