<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>General Sessions — {{ config('app.name') }}</title>
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
            <h1 class="text-lg font-bold text-gray-900">General Sessions</h1>
            <p class="text-xs text-gray-500">Keynotes, opening sessions, and main-stage presentations</p>
        </div>
        <a href="{{ route('admin.general-sessions.create') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium bg-indigo-500 text-white rounded-xl hover:bg-indigo-600 shadow-sm shadow-indigo-200 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Add General Session
        </a>
    </div>
</header>
<div class="p-4 sm:p-6 lg:p-8">
    @include('admin.partials.notification')

    @if ($items->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
            <div class="text-4xl mb-4">🎤</div>
            <p class="text-gray-500 font-medium">No general sessions yet.</p>
            <p class="text-gray-400 text-sm mt-1">Click the button above to add your first keynote or main-stage session.</p>
        </div>
    @else
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50/80">
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Session</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Time</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Room</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Speakers</th>
                        <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($items as $item)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-4">
                            <p class="text-sm font-semibold text-gray-900">{{ $item->title }}</p>
                            @if ($item->description)
                                <p class="text-xs text-gray-400 mt-0.5 line-clamp-2">{{ \Illuminate\Support\Str::limit(strip_tags($item->description), 100) }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-600">{{ date('H:i', strtotime($item->start_time)) }} – {{ date('H:i', strtotime($item->end_time)) }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-sm text-gray-600">{{ $item->room ?? '—' }}</span>
                        </td>
                        <td class="px-5 py-4">
                            @if ($item->speakers->isNotEmpty())
                                <div class="flex flex-col gap-1">
                                    @foreach ($item->speakers as $sp)
                                        <span class="text-sm text-gray-700">{{ $sp->name }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-sm text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.general-sessions.edit', $item) }}" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition">Edit</a>
                                <form action="{{ route('admin.general-sessions.destroy', $item) }}" method="POST" onsubmit="return confirm('Delete this session?');">
                                    @csrf @method('DELETE')
                                    <button class="px-3 py-1.5 text-xs font-medium rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
</main>
</div>
</body>
</html>
