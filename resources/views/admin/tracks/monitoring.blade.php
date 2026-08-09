<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="15">
    <title>Track Monitoring — {{ config('app.name') }}</title>
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
    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.tracks.index') }}" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Tracks
            </a>
            <span class="text-gray-300">/</span>
            <h1 class="text-lg font-bold text-gray-900">Track Monitoring</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.tracks.monitoring.export') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg border border-gray-200 text-gray-600 bg-white hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export CSV
            </a>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                Live · refresh 15s
            </span>
        </div>
    </div>
</header>

<div class="p-4 sm:p-6 lg:p-8">
    @include('admin.partials.notification')

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Tracks</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $totals->tracks }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Sessions</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $totals->sessions }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Scanned</p>
            <p class="text-3xl font-bold text-emerald-600 mt-1">{{ $totals->scanned }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Registrants (approved)</p>
            <p class="text-3xl font-bold text-indigo-600 mt-1">{{ $totals->registrants }}</p>
        </div>
    </div>

    {{-- Per-track cards --}}
    @forelse ($tracks as $track)
        @php
            $trackScanned = $track->agendaItems->sum('scanned_count');
            $trackRegistrants = $track->agendaItems->sum('registrants_count');
            $pct = $trackRegistrants > 0 ? round($trackScanned / $trackRegistrants * 100) : 0;
        @endphp
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-5">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-4 flex-wrap">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg font-bold">
                        {{ strtoupper(substr($track->name ?: $track->title, 0, 1)) }}
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">{{ $track->name ?: $track->title }}</h2>
                        <p class="text-xs text-gray-500">{{ $track->agendaItems->count() }} session(s) · {{ $trackScanned }} scanned · {{ $trackRegistrants }} registrants</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if ($trackRegistrants > 0)
                        <div class="w-40 h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="text-xs font-bold text-emerald-600">{{ $pct }}%</span>
                    @endif
                    <a href="{{ route('admin.tracks.visitors', $track) }}" class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition">Visitors</a>
                    <a href="{{ route('admin.tracks.registrants', $track) }}" class="px-2.5 py-1.5 text-xs font-medium rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition">Registrants</a>
                </div>
            </div>

            @if ($track->agendaItems->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead><tr class="bg-gray-50/60">
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold text-gray-500 uppercase">Session</th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold text-gray-500 uppercase hidden md:table-cell w-28">Room</th>
                        <th class="px-5 py-2.5 text-left text-[11px] font-semibold text-gray-500 uppercase w-24">Time</th>
                        <th class="px-5 py-2.5 text-center text-[11px] font-semibold text-gray-500 uppercase w-24">Scanned</th>
                        <th class="px-5 py-2.5 text-center text-[11px] font-semibold text-gray-500 uppercase w-24 hidden sm:table-cell">Registrants</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($track->agendaItems as $ai)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-5 py-3 max-w-0">
                                <a href="{{ route('admin.agenda.visitors', $ai) }}" class="text-sm font-medium text-gray-800 hover:text-indigo-600 truncate block" title="{{ $ai->title }}">{{ $ai->title }}</a>
                            </td>
                            <td class="px-5 py-3 hidden md:table-cell"><span class="text-sm text-gray-500">{{ $ai->room ?? '—' }}</span></td>
                            <td class="px-5 py-3"><span class="text-xs text-gray-500 whitespace-nowrap">{{ $ai->start_time ? substr($ai->start_time, 0, 5) : '—' }}{{ $ai->end_time ? '–' . substr($ai->end_time, 0, 5) : '' }}</span></td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full text-sm font-bold {{ $ai->scanned_count > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-50 text-gray-400' }}">{{ $ai->scanned_count }}</span>
                            </td>
                            <td class="px-5 py-3 text-center hidden sm:table-cell"><span class="text-sm text-gray-500">{{ $ai->registrants_count }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="px-5 py-8 text-center text-gray-400 text-sm"><p>No sessions linked to this track.</p></div>
            @endif
        </div>
    @empty
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-16 text-center">
            <p class="text-gray-400 font-medium">No tracks yet</p>
            <p class="text-xs text-gray-400">Create tracks and link them to agenda sessions to start monitoring.</p>
        </div>
    @endforelse
</div>
</main>
</div>
</body>
</html>
