<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Assign Sessions — {{ $user->name }} — {{ config('app.name') }}</title>
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
<div class="flex items-center gap-3">
<button id="sidebarToggle" class="lg:hidden p-2 -ml-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100">
<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
</button>
<div>
<h1 class="text-lg font-bold text-gray-900">Assign Sessions — {{ $user->name }}</h1>
<p class="text-xs text-gray-500">
Room: <span class="font-semibold text-teal-700">{{ $user->roomName() ?? '—' }}</span> ·
<span id="selCount">0</span> selected
</p>
</div>
</div>
<a href="{{ route('admin.room-accounts.index') }}" class="text-xs font-medium text-gray-500 hover:text-gray-800">← Back to Room Accounts</a>
</div>
</header>
<div class="p-4 sm:p-6 lg:p-8">
@include('admin.partials.notification')

<div class="bg-amber-50 border border-amber-100 rounded-2xl p-4 text-xs text-amber-900 mb-6">
<strong>Important:</strong> If <strong>no sessions</strong> are selected, this account can track <strong>all sessions</strong> (default).
Select the sessions below to restrict this account to only those sessions.
</div>

<form method="POST" action="{{ route('admin.room-accounts.sessions.store', $user) }}" id="assignForm">
@csrf
@foreach ($items as $roomName => $roomItems)
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-6 overflow-hidden">
<div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 bg-gray-50/60">
<h2 class="text-sm font-bold text-gray-900">{{ $roomName }}</h2>
<label class="flex items-center gap-2 text-xs font-medium text-indigo-600 cursor-pointer">
<input type="checkbox" class="room-select-all rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" data-room="{{ $roomName }}">
Select all in {{ $roomName }}
</label>
</div>
<table class="w-full">
<thead><tr class="bg-gray-50/40">
<th class="px-5 py-2 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider w-10">Pick</th>
<th class="px-5 py-2 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider w-28">Time</th>
<th class="px-5 py-2 text-left text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Session</th>
</tr></thead>
<tbody class="divide-y divide-gray-50">
@foreach ($roomItems as $item)
@php
    $company = $item->workshop ? ($item->workshop->name ?: $item->workshop->title)
        : ($item->track ? ($item->track->name ?: $item->track->title) : null);
    $typeLabel = match ($item->agenda_type ?? ($item->workshop_id ? 'workshop' : ($item->track_id ? 'track' : 'session'))) {
        'workshop' => 'Workshop',
        'track'    => 'Track',
        'general'  => 'General',
        default    => 'Session',
    };
@endphp
<tr class="hover:bg-gray-50/40">
<td class="px-5 py-2">
<input type="checkbox" name="agenda_item_ids[]" value="{{ $item->id }}" class="session-check rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ in_array($item->id, $assigned, true) ? 'checked' : '' }}>
</td>
<td class="px-5 py-2 whitespace-nowrap text-xs font-medium text-gray-700">{{ $item->start_time ? \Illuminate\Support\Str::substr($item->start_time, 0, 5) : '--' }} – {{ $item->end_time ? \Illuminate\Support\Str::substr($item->end_time, 0, 5) : '--' }}</td>
<td class="px-5 py-2">
<span class="text-sm text-gray-900">{{ $item->title }}</span>
@if ($company)
<span class="ml-2 text-xs text-gray-400">· {{ $company }}</span>
@endif
<span class="ml-2 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-600">{{ $typeLabel }}</span>
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
@endforeach

<div class="sticky bottom-4 flex items-center justify-between gap-3 bg-white rounded-2xl border border-gray-100 shadow-lg px-5 py-3">
<div class="text-sm text-gray-600"><span id="selCountFooter" class="font-bold text-indigo-600">0</span> session(s) selected</div>
<div class="flex gap-2.5">
<a href="{{ route('admin.room-accounts.index') }}" class="px-5 py-2.5 text-sm font-medium rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition">Cancel</a>
<button type="submit" class="px-5 py-2.5 text-sm font-semibold rounded-xl bg-indigo-500 text-white hover:bg-indigo-600 transition">Save Assignments</button>
</div>
</div>
</form>
</div>
</main>
</div>

@include('admin.partials.mobile-sidebar')
<script>
function updateCount() {
const n = document.querySelectorAll('.session-check:checked').length;
document.getElementById('selCount').textContent = n;
document.getElementById('selCountFooter').textContent = n;
}
document.querySelectorAll('.room-select-all').forEach(cb => {
cb.addEventListener('change', () => {
const room = cb.dataset.room;
const group = cb.closest('.bg-white');
group.querySelectorAll('.session-check').forEach(s => { s.checked = cb.checked; });
updateCount();
});
});
document.querySelectorAll('.session-check').forEach(cb => cb.addEventListener('change', updateCount));
updateCount();
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
document.getElementById('mobileSidebar')?.classList.toggle('-translate-x-full');
document.getElementById('sidebarOverlay')?.classList.toggle('hidden');
});
</script>
</body>
</html>
