<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Workshop Registrants — {{ config('app.name') }}</title>
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
        <div>
            <h1 class="text-lg font-bold text-gray-900">Workshop Registrants</h1>
            <p class="text-xs text-gray-500">View registrants for each workshop</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.workshop-registrants.export-csv') }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg border border-gray-200 text-gray-600 bg-white hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export All CSV
            </a>
        </div>
    </div>
</header>

<div class="p-4 sm:p-6 lg:p-8">
    @include('admin.partials.notification')

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-gray-900">All Workshops</h2>
                <p class="text-xs text-gray-500">Click a workshop to view its registrants</p>
            </div>
            <div class="text-xs text-gray-400">Total: <strong>{{ $workshops->count() }}</strong> workshops</div>
        </div>

        @if ($workshops->isEmpty())
            <div class="px-5 py-12 text-center text-gray-400 text-sm">No workshops available.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead><tr class="bg-gray-50/80">
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Workshop</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Linked Agenda</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Approved</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Pending</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Waitlist</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Action</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($workshops as $w)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-5 py-4">
                                    <p class="text-sm font-semibold text-gray-900">{{ $w->name ?: $w->title }}</p>
                                    @if (!$w->registration_open)
                                        <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Registration Closed</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 hidden lg:table-cell">
                                    @php $linked = $w->agendaItems; @endphp
                                    @if ($linked->isNotEmpty())
                                        @foreach ($linked as $ai)
                                            <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-indigo-50 text-indigo-700 mb-1">{{ $ai->title }} ({{ $ai->timeLabel() }})</span>
                                        @endforeach
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-bold {{ $w->approved_count > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-400' }}">
                                        {{ $w->approved_count }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @if ($w->pending_count > 0)
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-bold bg-amber-50 text-amber-700">{{ $w->pending_count }}</span>
                                    @else
                                        <span class="text-sm text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @if ($w->waitlist_count > 0)
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-bold bg-amber-50 text-amber-700">{{ $w->waitlist_count }}</span>
                                    @else
                                        <span class="text-sm text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <a href="{{ route('admin.workshops.registrants', $w) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
</main>
</div>
</body>
</html>
