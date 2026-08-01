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
            <a href="{{ route('admin.workshop-registrants.export-csv', request()->only(['profile', 'source', 'date_from', 'date_to'])) }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg border border-gray-200 text-gray-600 bg-white hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export All CSV
            </a>
        </div>
    </div>
</header>

<div class="p-4 sm:p-6 lg:p-8">
    @include('admin.partials.notification')

    {{-- Filter bar: Profile / Source / Date --}}
    <form method="GET" action="{{ route('admin.workshop-registrants.index') }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
        <div class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Profile</label>
                <select name="profile" class="px-3 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    <option value="">All profiles</option>
                    @foreach ($profiles as $p)
                        <option value="{{ $p }}" @selected(request('profile') === $p)>{{ $p }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Source</label>
                <select name="source" class="px-3 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    <option value="">All sources</option>
                    <option value="direct" @selected(request('source') === 'direct')>Direct</option>
                    @foreach ($sources as $s)
                        <option value="{{ $s }}" @selected(request('source') === $s)>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-3 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-3 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-xl bg-indigo-500 text-white hover:bg-indigo-600 transition">Apply</button>
                @if (request('profile') || request('source') || request('date_from') || request('date_to'))
                    <a href="{{ route('admin.workshop-registrants.index', request()->except(['profile', 'source', 'date_from', 'date_to'])) }}" class="px-4 py-2 text-xs font-medium rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition">Clear</a>
                @endif
            </div>
        </div>
    </form>

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
                <table class="w-full table-fixed">
                    <colgroup>
                        <col class="w-[22%]">
                        <col class="w-[30%]">
                        <col class="w-[7%]">
                        <col class="w-[7%]">
                        <col class="w-[7%]">
                        <col class="w-[7%]">
                        <col class="w-[7%]">
                        <col class="w-[13%]">
                    </colgroup>
                    <thead><tr class="bg-gray-50/80">
                        <th class="px-3 py-2.5 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Workshop</th>
                        <th class="px-3 py-2.5 text-left text-[11px] font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Schedule</th>
                        <th class="px-2 py-2.5 text-center text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Approved</th>
                        <th class="px-2 py-2.5 text-center text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Pending</th>
                        <th class="px-2 py-2.5 text-center text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Rejected</th>
                        <th class="px-2 py-2.5 text-center text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Waitlist</th>
                        <th class="px-2 py-2.5 text-center text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-2 py-2.5 text-center text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($workshops as $w)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-3 py-2.5">
                                    <p class="text-sm font-semibold text-gray-900 truncate" title="{{ $w->name ?: $w->title }}">{{ $w->name ?: $w->title }}</p>
                                    @if (!$w->registration_open)
                                        <span class="inline-flex items-center gap-1 mt-0.5 px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-500">Closed</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 hidden lg:table-cell">
                                    @php $schedTime = $w->timeRange() !== '—' ? $w->timeRange() : ($w->agendaItems->first()?->timeLabel() ?? '—'); @endphp
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $w->date ? $w->date->format('d M Y') : '—' }}</p>
                                    <p class="text-xs text-gray-500 truncate" title="{{ $schedTime }}">{{ $schedTime }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ $w->room ? $w->room . ' Room' : '—' }}</p>
                                </td>
                                <td class="px-2 py-2.5 text-center">
                                    <span class="text-sm font-bold {{ $w->approved_count > 0 ? 'text-emerald-600' : 'text-gray-300' }}">{{ $w->approved_count }}</span>
                                </td>
                                <td class="px-2 py-2.5 text-center">
                                    <span class="text-sm font-bold {{ $w->pending_count > 0 ? 'text-amber-600' : 'text-gray-300' }}">{{ $w->pending_count }}</span>
                                </td>
                                <td class="px-2 py-2.5 text-center">
                                    <span class="text-sm font-bold {{ $w->rejected_count > 0 ? 'text-red-600' : 'text-gray-300' }}">{{ $w->rejected_count }}</span>
                                </td>
                                <td class="px-2 py-2.5 text-center">
                                    <span class="text-sm font-bold {{ $w->waitlist_count > 0 ? 'text-amber-600' : 'text-gray-300' }}">{{ $w->waitlist_count }}</span>
                                </td>
                                <td class="px-2 py-2.5 text-center">
                                    <span class="text-sm font-bold text-indigo-600">{{ $w->total_count }}</span>
                                </td>
                                <td class="px-2 py-2.5 text-center">
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
