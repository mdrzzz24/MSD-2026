<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
<title>Time Slots — {{ config('app.name') }}</title>
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
            <div class="flex items-center h-16 px-4 sm:px-6 lg:px-8">
                <div><h1 class="text-lg font-bold text-gray-900">Time Slots</h1><p class="text-xs text-gray-500">Manage schedule rows</p></div>
            </div>
        </header>
        <div class="p-4 sm:p-6 lg:p-8">
    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl mb-4 text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <table class="w-full">
            <thead><tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase">
                <th class="px-4 py-3">Start</th><th class="px-4 py-3">End</th><th class="px-4 py-3">Order</th><th class="px-4 py-3 text-center">Actions</th>
            </tr></thead>
            <tbody class="divide-y">
                @forelse($slots as $s)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-4 py-3 text-sm font-mono">{{ $s->start_time }}</td>
                    <td class="px-4 py-3 text-sm font-mono">{{ $s->end_time }}</td>
                    <td class="px-4 py-3 text-sm">{{ $s->order }}</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex justify-center gap-1">
                            <form action="{{ route('admin.time-slots.update', $s) }}" method="POST" class="flex items-center gap-1">
                                @csrf @method('PUT')
                                <input type="time" name="start_time" value="{{ $s->start_time }}" required class="w-24 px-2 py-1 text-xs border rounded-lg">
                                <input type="time" name="end_time" value="{{ $s->end_time }}" required class="w-24 px-2 py-1 text-xs border rounded-lg">
                                <input type="number" name="order" value="{{ $s->order }}" min="0" class="w-14 px-2 py-1 text-xs border rounded-lg">
                                <button class="px-2 py-1 text-xs bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200">Save</button>
                            </form>
                            <form action="{{ route('admin.time-slots.destroy', $s) }}" method="POST" onsubmit="return confirm('Delete this time slot?')">
                                @csrf @method('DELETE')
                                <button class="px-2 py-1 text-xs bg-red-100 text-red-600 rounded-lg hover:bg-red-200">×</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">No time slots.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Add new --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-3">Add New Time Slot</h3>
        <form action="{{ route('admin.time-slots.store') }}" method="POST" class="flex items-end gap-2">
            @csrf
            <div><label class="block text-xs text-gray-500 mb-1">Start</label><input type="time" name="start_time" required class="px-3 py-2 border rounded-xl text-sm"></div>
            <div><label class="block text-xs text-gray-500 mb-1">End</label><input type="time" name="end_time" required class="px-3 py-2 border rounded-xl text-sm"></div>
            <div><label class="block text-xs text-gray-500 mb-1">Order</label><input type="number" name="order" value="0" min="0" class="px-3 py-2 border rounded-xl text-sm w-20"></div>
            <button class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm hover:bg-indigo-700">Add</button>
        </form>
    </div>
</div>
</main>
</div>
</body>
</html>
