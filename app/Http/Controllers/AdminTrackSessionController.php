<?php

namespace App\Http\Controllers;

use App\Models\AgendaItem;
use Illuminate\Http\Request;

class AdminTrackSessionController extends Controller
{
    // ── Scope: only items with a track_id (linked to a track) ──

    public function index()
    {
        $items = AgendaItem::orderBy('created_at')
            ->whereNotNull('track_id')
            ->with(['speakers', 'track'])
            ->get();

        return view('admin.track-sessions.index', compact('items'));
    }

    public function create()
    {
        $rooms    = \App\Models\Room::ordered()->get();
        $tracks   = \App\Models\Track::orderBy('title')->get(['id', 'name', 'title', 'description']);
        $speakers = \App\Models\Speaker::orderBy('name')->get(['id', 'name', 'title', 'company', 'photo']);

        return view('admin.track-sessions.create', compact('rooms', 'tracks', 'speakers'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateItem($request);

        $validated['category']    = 'track';
        $validated['workshop_id'] = null;
        $validated['is_registrable'] = $request->boolean('is_registrable');
        $validated['start_time']  = $validated['start_time'] ?: '00:00:00';
        $validated['end_time']    = $validated['end_time'] ?: '00:00:00';

        if ($request->boolean('is_full_row')) {
            $validated['room'] = null;
        }

        $agendaItem = AgendaItem::create($validated);
        $this->syncSpeakers($agendaItem, $request);

        return redirect()->route('admin.track-sessions.index')
            ->with('success', 'Track Session <strong>' . e($agendaItem->title) . '</strong> created.');
    }

    public function edit(AgendaItem $agendum)
    {
        $rooms    = \App\Models\Room::ordered()->get();
        $tracks   = \App\Models\Track::orderBy('title')->get(['id', 'name', 'title', 'description']);
        $speakers = \App\Models\Speaker::orderBy('name')->get(['id', 'name', 'title', 'company', 'photo']);
        $agendum->load(['speakers', 'track']);

        return view('admin.track-sessions.edit', compact('agendum', 'rooms', 'tracks', 'speakers'));
    }

    public function update(Request $request, AgendaItem $agendum)
    {
        $validated = $this->validateItem($request);

        $validated['category']    = 'track';
        $validated['workshop_id'] = null;
        $validated['is_registrable'] = $request->boolean('is_registrable');
        $validated['start_time']  = $validated['start_time'] ?: '00:00:00';
        $validated['end_time']    = $validated['end_time'] ?: '00:00:00';

        if ($request->boolean('is_full_row')) {
            $validated['room'] = null;
        }

        $agendum->update($validated);
        $agendum->speakers()->detach();
        $this->syncSpeakers($agendum, $request);

        return redirect()->route('admin.track-sessions.index')
            ->with('success', 'Track Session <strong>' . e($agendum->title) . '</strong> updated.');
    }

    public function destroy(AgendaItem $agendum)
    {
        $title = $agendum->title;
        $agendum->delete();

        return redirect()->route('admin.track-sessions.index')
            ->with('success', 'Track Session <strong>' . e($title) . '</strong> deleted.');
    }

    // ── Helpers ──

    private function validateItem(Request $request): array
    {
        return $request->validate([
            'title'          => ['required', 'string', 'max:255'],
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
            'track_id'       => ['required', 'exists:tracks,id'],
            'speaker_ids'    => ['nullable', 'array'],
            'speaker_ids.*'  => ['exists:speakers,id'],
            'speaker_presentation_title' => ['nullable', 'array'],
            'speaker_presentation_desc'  => ['nullable', 'array'],
        ]);
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
