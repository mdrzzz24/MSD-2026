<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Scan QR — Agenda — {{ config('app.name') }}</title>
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
            <h1 class="text-lg font-bold text-gray-900">Scan QR — Agenda</h1>
            <p class="text-xs text-gray-500">Select a session to scan registrant QR codes</p>
        </div>
    </div>
</header>

<div class="p-4 sm:p-6 lg:p-8">
    @include('admin.partials.notification')

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-900">Sessions</h2>
        </div>

        @if ($agendaItems->isEmpty())
            <div class="px-5 py-16 text-center text-gray-400 text-sm">
                <p>No agenda items available.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead><tr class="bg-gray-50/80">
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Session</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Time</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Room</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Action</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($agendaItems as $i => $item)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-4 py-3.5"><span class="text-sm text-gray-400">{{ $i + 1 }}</span></td>
                            <td class="px-4 py-3.5">
                                <span class="text-sm font-semibold text-gray-900">{{ $item->title }}</span>
                                @if ($item->agenda_type)
                                    <span class="ml-2 inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $item->agenda_type === 'workshop' ? 'bg-amber-50 text-amber-700' : 'bg-indigo-50 text-indigo-700' }}">{{ ucfirst($item->agenda_type) }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5"><span class="text-sm text-gray-600">{{ $item->date ? $item->date->format('d M Y') : '—' }}</span></td>
                            <td class="px-4 py-3.5"><span class="text-sm text-gray-600">{{ $item->timeLabel() }}</span></td>
                            <td class="px-4 py-3.5 hidden md:table-cell"><span class="text-sm text-gray-600">{{ $item->room ?? '—' }}</span></td>
                            <td class="px-4 py-3.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.agenda.scan', $item) }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                        Scan QR
                                    </a>
                                    <a href="{{ route('admin.agenda.visitors', $item) }}" class="inline-flex items-center gap-1 px-3 py-2 text-xs font-medium rounded-lg border border-gray-200 text-gray-600 bg-white hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        Check-ins
                                    </a>
                                </div>
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
