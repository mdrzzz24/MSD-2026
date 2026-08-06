<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Track Session — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
</head>
<body class="bg-gray-50 font-sans antialiased">
<div class="flex min-h-screen">
@include('admin.partials.sidebar')
<main class="flex-1 lg:ml-64">
<header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200">
    <div class="flex items-center h-16 px-4 sm:px-6 lg:px-8 gap-4">
        <a href="{{ route('admin.track-sessions.index') }}" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>Track Sessions
        </a>
        <span class="text-gray-300">/</span>
        <h1 class="text-lg font-bold text-gray-900">Edit: {{ $agendum->title }}</h1>
    </div>
</header>
<div class="p-4 sm:p-6 lg:p-8">
<div class="max-w-xl">
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
    @if ($errors->any())
        <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <ul class="list-disc list-inside text-sm">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif
    <form action="{{ route('admin.track-sessions.update', $agendum) }}" method="POST" class="space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Track <span class="text-red-500">*</span></label>
            <select name="track_id" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                <option value="">Select track…</option>
                @foreach ($tracks as $tr)
                    <option value="{{ $tr->id }}" {{ old('track_id', $agendum->track_id) == $tr->id ? 'selected' : '' }}>{{ $tr->name ?: $tr->title }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title', $agendum->title) }}" required
                   class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="4"
                      class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"
                      placeholder="Session description…">{{ old('description', $agendum->description) }}</textarea>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Start Time</label>
                <input type="time" name="start_time" value="{{ old('start_time', $agendum->start_time ? substr($agendum->start_time, 0, 5) : '') }}"
                       class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">End Time</label>
                <input type="time" name="end_time" value="{{ old('end_time', $agendum->end_time ? substr($agendum->end_time, 0, 5) : '') }}"
                       class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Room</label>
                <select name="room" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    <option value="">No room</option>
                    @foreach ($rooms as $room)
                        <option value="{{ $room->name }}" {{ old('room', $agendum->room) == $room->name ? 'selected' : '' }}>{{ $room->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Date</label>
            <input type="date" name="date" value="{{ old('date', $agendum->date?->format('Y-m-d')) }}"
                   class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Speakers</label>
            @if ($speakers->isEmpty())
                <p class="text-sm text-gray-400">No speakers registered yet. <a href="{{ route('admin.speakers.index') }}" class="text-indigo-600 underline">Add speakers first</a>.</p>
            @else
                @php $selectedSpeakerIds = old('speaker_ids', $agendum->speakers->pluck('id')->toArray()); @endphp
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-56 overflow-y-auto">
                    @foreach ($speakers as $sp)
                        <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200 cursor-pointer hover:border-indigo-300 transition">
                            <input type="checkbox" name="speaker_ids[]" value="{{ $sp->id }}"
                                   {{ in_array($sp->id, $selectedSpeakerIds) ? 'checked' : '' }}
                                   class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $sp->name }}</p>
                                @if ($sp->title || $sp->company)
                                    <p class="text-xs text-gray-500">{{ $sp->title }}{{ $sp->company ? ' · ' . $sp->company : '' }}</p>
                                @endif
                            </div>
                        </label>
                    @endforeach
                </div>
            @endif
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Key Highlights</label>
            <textarea name="key_highlights" rows="3"
                      class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"
                      placeholder="One bullet point per line…">{{ old('key_highlights', $agendum->key_highlights ?? '') }}</textarea>
        </div>
        <div class="flex items-center gap-4 pt-2">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_full_row" value="1" {{ old('is_full_row', !$agendum->room) ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-sm text-gray-600">Full-row (timetable spanning)</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_registrable" value="1" {{ old('is_registrable', $agendum->is_registrable) ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                <span class="text-sm text-gray-600">Registrable</span>
            </label>
        </div>
        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition shadow-sm">
                Save Changes
            </button>
            <a href="{{ route('admin.track-sessions.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
            <span class="flex-1"></span>
            <form action="{{ route('admin.track-sessions.destroy', $agendum) }}" method="POST" onsubmit="return confirm('Delete this session?');" class="inline">
                @csrf @method('DELETE')
                <button class="px-4 py-2 text-sm font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition">Delete</button>
            </form>
        </div>
    </form>
</div>
</div>
</div>
</main>
</div>
</body>
</html>
