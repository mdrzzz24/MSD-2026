<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Check-in Log — {{ config('app.name') }}</title>
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
<button id="sidebarToggle" class="lg:hidden p-2 -ml-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100">
<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
</button>
<div><h1 class="text-lg font-bold text-gray-900">Check-in Log</h1><p class="text-xs text-gray-500">All checked-in registrants</p></div>
</div>
</div>
</header>
<div class="p-4 sm:p-6 lg:p-8 space-y-6">
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
<div class="overflow-x-auto">
<table class="w-full">
<thead><tr class="bg-gray-50/80">
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Company</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Checked In At</th>
</tr></thead>
<tbody class="divide-y divide-gray-50">
@forelse ($checkedIn as $r)
<tr class="hover:bg-gray-50/50">
<td class="px-5 py-4"><a href="{{ route('admin.registrants.show', $r) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">{{ $r->name }}</a></td>
<td class="px-5 py-4"><span class="text-sm text-gray-600">{{ $r->email }}</span></td>
<td class="px-5 py-4"><span class="text-sm text-gray-600">{{ $r->company ?? '—' }}</span></td>
<td class="px-5 py-4"><span class="text-sm font-medium text-emerald-600">{{ $r->checked_in_at->format('d M Y, H:i') }}</span></td>
</tr>
@empty
<tr><td colspan="4" class="text-center py-16 text-gray-400">No check-ins yet</td></tr>
@endforelse
</tbody>
</table>
</div>
@if ($checkedIn->hasPages())
<div class="px-5 py-4 border-t border-gray-100">{{ $checkedIn->links() }}</div>
@endif
</div>
</div>
</main>
</div>
@include('admin.partials.mobile-sidebar')
<script>
document.getElementById('sidebarToggle')?.addEventListener('click', () => {
document.getElementById('mobileSidebar')?.classList.toggle('-translate-x-full');
document.getElementById('sidebarOverlay')?.classList.toggle('hidden');
});
</script>
</body>
</html>