<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Agenda Item — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
</head>
<body class="bg-gray-50 font-sans antialiased">
<div class="flex min-h-screen">
@include('admin.partials.sidebar')
<main class="flex-1 lg:ml-64">
<header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200"><div class="flex items-center h-16 px-4 sm:px-6 lg:px-8 gap-4"><a href="{{ route('admin.agenda.index') }}" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>Agenda</a><span class="text-gray-300">/</span><h1 class="text-lg font-bold text-gray-900">Edit: {{ $agendum->title }}</h1></div></header>
<div class="p-4 sm:p-6 lg:p-8"><div class="max-w-xl"><div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
    @if ($errors->any())
        <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6"><svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><ul class="list-disc list-inside text-sm">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    <form action="{{ route('admin.agenda.update', $agendum) }}" method="POST" class="space-y-4">
        @csrf @method('PUT')
        <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Title</label><input type="text" name="title" value="{{ old('title', $agendum->title) }}" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition"></div>

        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Start Time</label><input type="time" name="start_time" value="{{ old('start_time', $agendum->start_time) }}" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition"></div>
            <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">End Time</label><input type="time" name="end_time" value="{{ old('end_time', $agendum->end_time) }}" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition"></div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Room</label>
                <select name="room" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition">
                    <option value="">— Full Row (all rooms) —</option>
                    @foreach ($rooms as $room)
                        <option value="{{ $room->name }}" {{ old('room', $agendum->room) === $room->name ? 'selected' : '' }}>{{ $room->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Category</label>
                <select name="category" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition">
                    <option value="">— None —</option>
                    <option value="general" {{ old('category', $agendum->category)==='general'?'selected':'' }}>General</option>
                    <option value="workshop" {{ old('category', $agendum->category)==='workshop'?'selected':'' }}>Workshop</option>
                    <option value="platinum" {{ old('category', $agendum->category)==='platinum'?'selected':'' }}>Platinum</option>
                    <option value="gold" {{ old('category', $agendum->category)==='gold'?'selected':'' }}>Gold</option>
                    <option value="break" {{ old('category', $agendum->category)==='break'?'selected':'' }}>Break</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Sort Order</label><input type="number" name="order" value="{{ old('order', $agendum->order) }}" min="0" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition"></div>
            <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Rowspan <span class="text-xs text-gray-400 font-normal">(↓ merge rows)</span></label><input type="number" name="rowspan" value="{{ old('rowspan', $agendum->rowspan) }}" min="1" max="12" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition"></div>
            <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Colspan <span class="text-xs text-gray-400 font-normal">(→ merge columns)</span></label><input type="number" name="colspan" value="{{ old('colspan', $agendum->colspan) }}" min="1" max="8" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition"></div>
        </div>

        <button type="submit" class="w-full py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/25 transition-all text-sm">Update Item</button>
    </form>
</div></div></div>
</main>
</div>
</body>
</html>
