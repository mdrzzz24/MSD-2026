<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Room Accounts (Mobile App) — {{ config('app.name') }}</title>
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
<h1 class="text-lg font-bold text-gray-900">Room Accounts <span class="text-indigo-500">(Mobile App)</span></h1>
<p class="text-xs text-gray-500">One account per room — each tracks only the sessions you assign to it</p>
</div>
</div>
<button onclick="openCreateModal()" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-indigo-500 text-white hover:bg-indigo-600 transition">+ New Room Account</button>
</div>
</header>
<div class="p-4 sm:p-6 lg:p-8 space-y-6">
@include('admin.partials.notification')

{{-- How it works --}}
<div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-4 text-xs text-indigo-900">
<span class="font-semibold">How session assignment works:</span>
An account with <strong>no sessions assigned</strong> can track <strong>all sessions</strong> (default).
Once you assign sessions to an account, it can only display &amp; track <strong>those sessions</strong> in the mobile app.
The room is just a label — access is controlled by the assignment below.
</div>

{{-- Accounts table --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
<div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
<h2 class="text-base font-bold text-gray-900">Accounts</h2>
<a href="{{ route('admin.management.users') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">Manage in Admin Users →</a>
</div>
<div class="overflow-x-auto">
<table class="w-full">
<thead><tr class="bg-gray-50/80">
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Account</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Room</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Assigned Sessions</th>
<th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
</tr></thead>
<tbody class="divide-y divide-gray-50">
@forelse ($accounts as $a)
<tr class="hover:bg-gray-50/50">
<td class="px-5 py-4">
<span class="text-sm font-semibold text-gray-900">{{ $a->name }}</span>
<span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-teal-50 text-teal-700 border border-teal-200">Room</span>
</td>
<td class="px-5 py-4">
@if ($a->room)
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-700 border border-gray-200">{{ $a->room->name }}</span>
@else
<span class="text-xs text-gray-400">—</span>
@endif
</td>
<td class="px-5 py-4"><span class="text-sm text-gray-600">{{ $a->email }}</span></td>
<td class="px-5 py-4">
@if ($a->managed_agenda_items_count === 0)
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> All sessions (default)
</span>
@else
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">{{ $a->managed_agenda_items_count }} session(s)</span>
@endif
</td>
<td class="px-5 py-4 text-center whitespace-nowrap">
<a href="{{ route('admin.room-accounts.sessions', $a) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 mr-2">⚙ Assign Sessions</a>
<a href="{{ route('admin.management.users') }}" class="text-xs text-amber-600 hover:text-amber-800 font-medium mr-2">Edit</a>
@if ($a->id !== auth()->id())
<form action="{{ route('admin.management.users.destroy', $a) }}" method="POST" class="inline" onsubmit="return confirm('Delete {{ $a->name }}? This removes the mobile app account.')">
@csrf @method('DELETE')
<button type="submit" class="text-xs text-red-600 hover:text-red-800 font-medium">Delete</button>
</form>
@endif
</td>
</tr>
@empty
<tr><td colspan="5" class="px-5 py-10 text-center text-sm text-gray-400">No room accounts yet. Create one below or via <code class="text-indigo-500">php artisan app:create-room-accounts</code>.</td></tr>
@endforelse
</tbody>
</table>
</div>
</div>
</div>
</main>
</div>

{{-- Create modal --}}
<div id="createModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm p-4">
<div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
<div class="px-6 py-4 border-b border-gray-100"><h3 class="text-lg font-bold text-gray-900">New Room Account</h3></div>
<form method="POST" action="{{ route('admin.room-accounts.store') }}">
@csrf
<div class="p-6 space-y-4">
<div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Room</label>
<select id="acctRoom" name="room_id" required class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" onchange="onRoomSelect()">
<option value="">— Select room —</option>
@foreach ($rooms as $r)
<option value="{{ $r->id }}" data-name="{{ $r->name }}" data-slug="{{ \Illuminate\Support\Str::slug($r->name) }}">{{ $r->name }}</option>
@endforeach
</select>
<p class="text-xs text-gray-400 mt-1">Name &amp; email auto-fill from the room (editable).</p>
</div>
<div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Name</label><input type="text" id="acctName" name="name" required class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
<div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label><input type="email" id="acctEmail" name="email" required class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
<div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label><input type="password" id="acctPassword" name="password" required minlength="6" value="room12345" class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
</div>
<div class="flex justify-end gap-2.5 px-6 py-4 border-t border-gray-100 bg-gray-50/50">
<button type="button" onclick="closeCreateModal()" class="px-5 py-2.5 text-sm font-medium rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition">Cancel</button>
<button type="submit" class="px-5 py-2.5 text-sm font-semibold rounded-xl bg-indigo-500 text-white hover:bg-indigo-600 transition">Create Account</button>
</div>
</form>
</div>
</div>

@include('admin.partials.mobile-sidebar')
<script>
function openCreateModal() {
document.getElementById('createModal').classList.remove('hidden');
document.getElementById('createModal').classList.add('flex');
}
function closeCreateModal() {
document.getElementById('createModal').classList.add('hidden');
document.getElementById('createModal').classList.remove('flex');
}
function onRoomSelect() {
const sel = document.getElementById('acctRoom');
const opt = sel.options[sel.selectedIndex];
const nameEl = document.getElementById('acctName');
const emailEl = document.getElementById('acctEmail');
if (!opt.value) return;
if (!nameEl.value.trim()) nameEl.value = opt.dataset.name || '';
if (!emailEl.value.trim()) emailEl.value = (opt.dataset.slug || '') + '@msd26.app';
}
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
document.getElementById('mobileSidebar')?.classList.toggle('-translate-x-full');
document.getElementById('sidebarOverlay')?.classList.toggle('hidden');
});
</script>
</body>
</html>
