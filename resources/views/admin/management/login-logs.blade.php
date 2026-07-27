<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login Logs — {{ config('app.name') }}</title>
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
<div><h1 class="text-lg font-bold text-gray-900">Login Logs</h1><p class="text-xs text-gray-500">Monitor all user login activity</p></div>
</div>
<div class="flex items-center gap-2">
    <a href="{{ route('admin.management.login-logs.export-csv') }}?{{ http_build_query(request()->only('type', 'active')) }}"
       class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg border border-gray-200 text-gray-600 bg-white hover:bg-gray-50 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Export CSV
    </a>
</div>
</div>
</header>
<div class="p-4 sm:p-6 lg:p-8 space-y-6">

@include('admin.partials.notification')

{{-- Stats Cards --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Logins</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalLogins) }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Active Sessions</p>
        <p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($activeSessions) }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Today</p>
        <p class="text-2xl font-bold text-indigo-600 mt-1">{{ number_format($todayLogins) }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Admin/Client</p>
        <p class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($adminLogins) }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Registrants</p>
        <p class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($registrantLogins) }}</p>
    </div>
</div>

{{-- Filters --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
    <form method="GET" class="flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">User Type</label>
            <select name="type" class="px-3 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                <option value="">All Types</option>
                <option value="admin" {{ request('type') === 'admin' ? 'selected' : '' }}>Admin / Client</option>
                <option value="registrant" {{ request('type') === 'registrant' ? 'selected' : '' }}>Registrant</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">Status</label>
            <select name="active" class="px-3 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                <option value="">All Sessions</option>
                <option value="1" {{ request('active') ? 'selected' : '' }}>Active Only</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">From</label>
            <input type="date" name="from" value="{{ request('from') }}"
                   class="px-3 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 mb-1">To</label>
            <input type="date" name="to" value="{{ request('to') }}"
                   class="px-3 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
        </div>
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or email..."
                   class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
        </div>
        <div class="flex gap-2">
            <button type="submit" class="px-4 py-2 text-sm font-medium rounded-xl bg-indigo-500 text-white hover:bg-indigo-600 transition">Filter</button>
            <a href="{{ route('admin.management.login-logs') }}" class="px-4 py-2 text-sm font-medium rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 transition">Reset</a>
        </div>
    </form>
</div>

{{-- Logs Table --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
<div class="overflow-x-auto">
<table class="w-full">
<thead><tr class="bg-gray-50/80">
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">User</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">IP Address</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Login At</th>
<th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Logout At</th>
<th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
</tr></thead>
<tbody class="divide-y divide-gray-50">
@forelse ($logs as $log)
<tr class="hover:bg-gray-50/50">
    <td class="px-5 py-4">
        <div class="text-sm font-semibold text-gray-900">{{ $log->name }}</div>
        <div class="text-xs text-gray-500">{{ $log->email }}</div>
    </td>
    <td class="px-5 py-4">
        @if ($log->user_type === 'admin')
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Admin
            </span>
        @else
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Registrant
            </span>
        @endif
    </td>
    <td class="px-5 py-4">
        <span class="text-sm text-gray-600 font-mono">{{ $log->ip_address ?? '—' }}</span>
    </td>
    <td class="px-5 py-4">
        <span class="text-sm text-gray-700">{{ $log->login_at ? $log->login_at->format('d M Y, H:i:s') : '—' }}</span>
    </td>
    <td class="px-5 py-4">
        @if ($log->logout_at)
            <span class="text-sm text-gray-700">{{ $log->logout_at->format('d M Y, H:i:s') }}</span>
        @else
            <span class="text-sm text-gray-400 italic">—</span>
        @endif
    </td>
    <td class="px-5 py-4 text-center">
        @if ($log->isActive())
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
            </span>
        @else
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-500 border border-gray-200">
                Logged Out
            </span>
        @endif
    </td>
</tr>
@empty
<tr><td colspan="6" class="text-center py-16 text-gray-400">No login logs found</td></tr>
@endforelse
</tbody>
</table>
</div>
@if ($logs->hasPages())
<div class="px-5 py-4 border-t border-gray-100">{{ $logs->links() }}</div>
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
