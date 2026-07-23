<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tracks: {{ $workshop->name ?: $workshop->title }} — {{ config('app.name') }}</title>
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
        <a href="{{ route('admin.workshops.index') }}" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>Workshops
        </a>
        <span class="text-gray-300">/</span>
        <h1 class="text-lg font-bold text-gray-900">Tracks: {{ $workshop->name ?: $workshop->title }}</h1>
    </div>
</header>
<div class="p-4 sm:p-6 lg:p-8">
    @include('admin.partials.notification')

    {{-- Workshop info --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6 flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-teal-500 to-emerald-600 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
            {{ strtoupper(substr($workshop->name ?: $workshop->title, 0, 1)) }}
        </div>
        <div>
            <p class="text-sm font-bold text-gray-900">{{ $workshop->name ?: $workshop->title }}</p>
            @if ($workshop->name)
                <p class="text-xs text-gray-500">{{ $workshop->title }}</p>
            @endif
        </div>
    </div>

    {{-- Add Track Form --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
        <h2 class="text-sm font-bold text-gray-800 mb-4">Add Track</h2>
        <form action="{{ route('admin.workshops.tracks.store', $workshop) }}" method="POST" class="space-y-3">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Track Name *</label>
                    <input type="text" name="name" required placeholder="e.g. IT, HR" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Track Title</label>
                    <input type="text" name="title" placeholder="e.g. Information Technology Track" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Description</label>
                <textarea name="description" rows="2" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition resize-y"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Start Time</label>
                    <input type="time" name="start_time" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">End Time</label>
                    <input type="time" name="end_time" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Assign Speakers</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 max-h-40 overflow-y-auto p-3 bg-gray-50 rounded-xl border border-gray-200">
                    @forelse ($allSpeakers as $sp)
                        <label class="flex items-center gap-2 text-sm cursor-pointer hover:text-indigo-600 transition">
                            <input type="checkbox" name="speaker_ids[]" value="{{ $sp->id }}" class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span>{{ $sp->name }}</span>
                            @if ($sp->title)
                                <span class="text-xs text-gray-400">({{ $sp->title }})</span>
                            @endif
                        </label>
                    @empty
                        <p class="text-xs text-gray-400 col-span-full">No speakers available. <a href="{{ route('admin.speakers.index') }}" class="text-indigo-600 hover:underline">Add speakers first</a>.</p>
                    @endforelse
                </div>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-teal-600 text-white text-sm font-semibold rounded-xl hover:bg-teal-700 shadow-sm transition">Create Track</button>
        </form>
    </div>

    {{-- Tracks List --}}
    <div class="space-y-4">
        @forelse ($tracks as $track)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden {{ $track->is_active ? '' : 'opacity-60' }}">
                {{-- Track header --}}
                <div class="flex items-center justify-between px-5 py-4 bg-gradient-to-r from-teal-50 to-emerald-50 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-teal-500 to-emerald-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                            {{ strtoupper(substr($track->name, 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">{{ $track->name }}</h3>
                            @if ($track->title && $track->title !== $track->name)
                                <p class="text-xs text-gray-500">{{ $track->title }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if ($track->is_active)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Active</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-500 border border-gray-200">Inactive</span>
                        @endif
                        <form action="{{ route('admin.workshops.tracks.toggle', [$workshop, $track]) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-2 py-1 text-[11px] font-medium rounded-lg {{ $track->is_active ? 'bg-amber-100 text-amber-700 hover:bg-amber-200' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' }} transition">
                                {{ $track->is_active ? 'Disable' : 'Enable' }}
                            </button>
                        </form>
                        <button onclick="openEditTrack({{ $track->id }})" class="px-2 py-1 text-[11px] font-medium rounded-lg bg-indigo-100 text-indigo-700 hover:bg-indigo-200 transition">Edit</button>
                        <form action="{{ route('admin.workshops.tracks.destroy', [$workshop, $track]) }}" method="POST" class="inline" onsubmit="return confirm('Delete track {{ $track->name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="px-2 py-1 text-[11px] font-medium rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition">Delete</button>
                        </form>
                    </div>
                </div>

                {{-- Track speakers --}}
                @php $trackSpeakers = $track->speakers; @endphp
                <div class="px-5 py-3">
                    @if ($trackSpeakers->isNotEmpty())
                        <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider mb-2">Speakers</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($trackSpeakers as $sp)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-gray-50 border border-gray-200 text-xs font-medium text-gray-700">
                                    <span class="w-5 h-5 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-[9px] font-bold flex-shrink-0">{{ strtoupper(substr($sp->name, 0, 1)) }}</span>
                                    {{ $sp->name }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-gray-400 italic">No speakers assigned</p>
                    @endif
                </div>

                <div class="px-5 py-3 border-t border-gray-50 flex flex-wrap items-center gap-4">
                    @if ($track->start_time || $track->end_time)
                        <span class="text-xs text-gray-500">
                            <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                            {{ $track->start_time ? date('H:i', strtotime($track->start_time)) : '—' }} – {{ $track->end_time ? date('H:i', strtotime($track->end_time)) : '—' }}
                        </span>
                    @endif
                    @if ($track->description)
                        <span class="text-xs text-gray-500">{{ $track->description }}</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                </svg>
                <p class="text-gray-400 font-medium">No tracks yet</p>
                <p class="text-xs text-gray-400 mt-1">Add tracks for multi-track workshops like Workday.</p>
            </div>
        @endforelse
    </div>
</div>
</main>
</div>

{{-- Edit Track Modal --}}
<div id="editTrackModal" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;background:rgba(0,0,0,0.4);backdrop-filter:blur(4px);padding:16px;">
  <div style="background:#fff;border-radius:16px;box-shadow:0 25px 50px rgba(0,0,0,0.25);width:100%;max-width:520px;padding:24px;">
    <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Track</h3>
    <form id="editTrackForm" method="POST" class="space-y-3">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Track Name *</label>
                <input type="text" name="name" id="editTrackName" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Track Title</label>
                <input type="text" name="title" id="editTrackTitle" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition">
            </div>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Description</label>
            <textarea name="description" id="editTrackDescription" rows="2" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition resize-y"></textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Start Time</label>
                <input type="time" name="start_time" id="editTrackStartTime" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">End Time</label>
                <input type="time" name="end_time" id="editTrackEndTime" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition">
            </div>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Assign Speakers</label>
            <div id="editTrackSpeakers" class="grid grid-cols-2 md:grid-cols-4 gap-2 max-h-40 overflow-y-auto p-3 bg-gray-50 rounded-xl border border-gray-200">
                @foreach ($allSpeakers as $sp)
                    <label class="flex items-center gap-2 text-sm cursor-pointer hover:text-indigo-600 transition" data-speaker-id="{{ $sp->id }}">
                        <input type="checkbox" name="speaker_ids[]" value="{{ $sp->id }}" class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span>{{ $sp->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>
        <div class="flex gap-2 pt-2">
            <button type="button" onclick="closeEditTrackModal()" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200 transition">Cancel</button>
            <button type="submit" class="flex-1 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg transition">Update Track</button>
        </div>
    </form>
  </div>
</div>

<script>
const trackData = @json($trackData);

function openEditTrack(id) {
    const data = trackData.find(t => t.id === id);
    if (!data) return;

    document.getElementById('editTrackName').value = data.name;
    document.getElementById('editTrackTitle').value = data.title || '';
    document.getElementById('editTrackDescription').value = data.description || '';
    document.getElementById('editTrackStartTime').value = data.start_time || '';
    document.getElementById('editTrackEndTime').value = data.end_time || '';

    // Set form action
    const action = '{{ route('admin.workshops.tracks.update', [$workshop, '__TRACK__']) }}'.replace('__TRACK__', id);
    document.getElementById('editTrackForm').action = action;

    // Check speakers
    document.querySelectorAll('#editTrackSpeakers input[type="checkbox"]').forEach(cb => {
        cb.checked = data.speaker_ids.includes(parseInt(cb.value));
    });

    document.getElementById('editTrackModal').style.display = 'flex';
}

function closeEditTrackModal() {
    document.getElementById('editTrackModal').style.display = 'none';
}
</script>
</body>
</html>
