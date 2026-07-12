<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Agenda Registrants — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
</head>
<body class="bg-gray-50 font-sans antialiased">
<div class="flex min-h-screen">
@include('admin.partials.sidebar')
<main class="flex-1 lg:ml-64">
<header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
        <div>
            <h1 class="text-lg font-bold text-gray-900">
                @if(isset($track)) Track Registrants: {{ $track->title }} @else Agenda Registrants @endif
            </h1>
            <p class="text-xs text-gray-500">
                @if(isset($track)) Agenda sessions linked to this track @else Track & Workshop registrations from agenda @endif
            </p>
        </div>
        @if(isset($track))
        <a href="{{ route('admin.tracks.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">← Back to Tracks</a>
        @endif
    </div>
</header>
<div class="p-4 sm:p-6 lg:p-8">
    @include('admin.partials.notification')

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full">
            <thead><tr class="bg-gray-50/80">
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Session</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Type</th>
                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Time</th>
                <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Capacity</th>
                <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Approved</th>
                <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Pending</th>
                <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Action</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($items as $item)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-5 py-4"><p class="text-sm font-semibold text-gray-900">{{ $item->title }}</p></td>
                    <td class="px-5 py-4">
                        @php
                            $type = $item->agenda_type
                                ?: ($item->category === 'workshop' ? 'workshop' : null)
                                ?: ($item->track_id ? 'track' : null)
                                ?: ($item->workshop_id ? 'workshop' : null)
                                ?: 'session';
                        @endphp
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $type==='workshop'?'bg-amber-50 text-amber-700':'bg-indigo-50 text-indigo-700' }}">{{ ucfirst($type) }}</span>
                    </td>
                    <td class="px-5 py-4"><span class="text-sm text-gray-600">{{ $item->timeLabel() }}</span></td>
                    <td class="px-5 py-4 text-center"><span class="text-sm text-gray-600">{{ $item->capacity > 0 ? $item->capacity : '∞' }}</span></td>
                    <td class="px-5 py-4 text-center"><span class="text-sm font-bold text-emerald-600">{{ $item->approved_count }}</span></td>
                    <td class="px-5 py-4 text-center"><span class="text-sm font-bold {{ $item->pending_count > 0 ? 'text-amber-600' : 'text-gray-400' }}">{{ $item->pending_count }}</span></td>
                    <td class="px-5 py-4 text-center">
                        <a href="{{ route('admin.agenda-registrants.detail', $item) }}" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400 text-sm">No registrable agenda items yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</main>
</div>
</body>
</html>
