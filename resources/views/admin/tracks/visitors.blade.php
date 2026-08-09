<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Scans — {{ $track->name ?: $track->title }} — {{ config('app.name') }}</title>
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
            <h1 class="text-lg font-bold text-gray-900">Scans — {{ $track->name ?: $track->title }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.tracks.export', $track) }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg border border-gray-200 text-gray-600 bg-white hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export CSV
            </a>
            <a href="{{ route('admin.tracks.registrants', $track) }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg border border-gray-200 text-gray-600 bg-white hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Registrants
            </a>
        </div>
    </div>
</header>

<div class="p-4 sm:p-6 lg:p-8">
    @include('admin.partials.notification')

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-gray-900">Scan / Check-in Records</h2>
                <p class="text-xs text-gray-500">Total: <strong>{{ $visits->total() }}</strong> scan(s)</p>
            </div>
        </div>

        @if ($visits->isEmpty())
            <div class="px-5 py-16 text-center text-gray-400 text-sm"><p>No scans yet for this track.</p></div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full table-fixed">
                    <thead><tr class="bg-gray-50/80">
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase w-10">#</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell w-48">Session</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell w-36">Company</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Scanned At</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Tracked Out At</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($visits as $i => $visit)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-4 py-3.5"><span class="text-sm text-gray-400">{{ $visits->firstItem() + $i }}</span></td>
                            <td class="px-4 py-3.5 max-w-0">
                                <a href="{{ route('admin.registrants.show', $visit->registrant) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 hover:underline truncate block" title="{{ $visit->registrant->display_name ?: $visit->registrant->name }}">
                                    {{ $visit->registrant->display_name ?: $visit->registrant->name }}
                                </a>
                                <span class="text-xs text-gray-400 truncate block" title="{{ $visit->registrant->email }}">{{ $visit->registrant->email }}</span>
                            </td>
                            <td class="px-4 py-3.5 hidden md:table-cell max-w-0">
                                @if ($visit->agendaItem)
                                    <span class="text-sm text-gray-600 truncate block" title="{{ $visit->agendaItem->title }}">{{ $visit->agendaItem->title }}</span>
                                @else
                                    <span class="text-sm text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 hidden lg:table-cell max-w-0"><span class="text-sm text-gray-600 truncate block" title="{{ $visit->registrant->company ?? '' }}">{{ $visit->registrant->company ?? '—' }}</span></td>
                            <td class="px-4 py-3.5"><span class="text-sm text-gray-600 whitespace-nowrap">{{ $visit->visited_at ? $visit->visited_at->format('d M Y, H:i') : '—' }}</span></td>
                            <td class="px-4 py-3.5">
                                @if ($visit->left_at)
                                    <span class="inline-flex items-center gap-1 text-sm font-medium text-red-600 whitespace-nowrap">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        {{ $visit->left_at->format('d M Y, H:i') }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 border-t border-gray-100">{{ $visits->links() }}</div>
        @endif
    </div>
</div>
</main>
</div>
</body>
</html>
