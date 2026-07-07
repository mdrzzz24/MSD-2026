<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Registrant — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
</head>
<body class="bg-gray-50 font-sans antialiased">
<div class="flex min-h-screen">
    {{-- Sidebar --}}
    <aside class="hidden lg:flex lg:flex-col w-64 bg-white border-r border-gray-200 fixed inset-y-0 z-40">
        <div class="flex items-center gap-3 h-16 px-6 border-b border-gray-100">
            <div class="w-9 h-9 bg-gradient-to-br from-emerald-600 to-teal-600 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <span class="text-lg font-bold text-gray-900 tracking-tight">Registrant</span>
        </div>
        <nav class="flex-1 px-3 py-6 space-y-1">
            <a href="{{ route('registrant.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium bg-emerald-50 text-emerald-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/></svg>
                Dashboard
            </a>
            <div class="pt-4">
                <form action="{{ route('registrant.logout') }}" method="POST">
                    @csrf
                    <button class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-red-500 hover:bg-red-50 transition w-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2h5a2 2 0 012 2v1"/></svg>
                        Logout
                    </button>
                </form>
            </div>
        </nav>
        <div class="p-4 border-t border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-full flex items-center justify-center text-white text-sm font-bold">{{ strtoupper(substr($registrant->display_name, 0, 1)) }}</div>
                <div class="flex-1 min-w-0"><p class="text-sm font-semibold text-gray-900 truncate">{{ $registrant->display_name }}</p><p class="text-xs text-gray-500 truncate">{{ $registrant->email }}</p></div>
            </div>
        </div>
    </aside>

    {{-- Main --}}
    <main class="flex-1 lg:ml-64">
        <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200">
            <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                <div><h1 class="text-lg font-bold text-gray-900">Dashboard</h1><p class="text-xs text-gray-500">Workshop & Sesi Anda</p></div>
                <span class="text-xs text-gray-400">{{ $registrant->email }}</span>
            </div>
        </header>

        <div class="p-4 sm:p-6 lg:p-8 space-y-6">

            @if (session('success'))
                <div class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm">{!! session('success') !!}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-2xl">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm">{{ session('error') }}</span>
                </div>
            @endif

            {{-- My Workshops --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="text-base font-bold text-gray-900">My Workshops</h2>
                    <p class="text-xs text-gray-500">Workshops you have registered for</p>
                </div>
                @if ($myWorkshops->isEmpty())
                    <div class="px-5 py-12 text-center text-gray-400 text-sm">You haven't registered for any workshops yet.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead><tr class="bg-gray-50/80">
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Workshop</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase hidden sm:table-cell">Room</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Time</th>
                                <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Action</th>
                            </tr></thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach ($myWorkshops as $w)
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-5 py-4"><p class="text-sm font-semibold text-gray-900">{{ $w->title }}</p></td>
                                        <td class="px-5 py-4 hidden sm:table-cell"><span class="text-sm text-gray-600">{{ $w->room ?? '—' }}</span></td>
                                        <td class="px-5 py-4"><span class="text-sm text-gray-600">{{ $w->date->format('d M Y') }}</span></td>
                                        <td class="px-5 py-4"><span class="text-sm text-gray-600">{{ $w->timeRange() }}</span></td>
                                        <td class="px-5 py-4 text-center">
                                            <form action="{{ route('registrant.workshop.unregister', $w) }}" method="POST" onsubmit="return confirm('Cancel registration for {{ $w->title }}?')">
                                                @csrf
                                                <button class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-red-100 text-red-600 hover:bg-red-200 transition">Cancel</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Available Workshops --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="text-base font-bold text-gray-900">Available Workshops</h2>
                    <p class="text-xs text-gray-500">Workshops you can join</p>
                </div>
                @if ($availableWorkshops->isEmpty())
                    <div class="px-5 py-12 text-center text-gray-400 text-sm">No workshops available at this time.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead><tr class="bg-gray-50/80">
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Workshop</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase hidden sm:table-cell">Room</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Time</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Capacity</th>
                                <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Register</th>
                            </tr></thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach ($availableWorkshops as $w)
                                    <tr class="hover:bg-gray-50/50">
                                        <td class="px-5 py-4"><p class="text-sm font-semibold text-gray-900">{{ $w->title }}</p></td>
                                        <td class="px-5 py-4 hidden sm:table-cell"><span class="text-sm text-gray-600">{{ $w->room ?? '—' }}</span></td>
                                        <td class="px-5 py-4"><span class="text-sm text-gray-600">{{ $w->date->format('d M Y') }}</span></td>
                                        <td class="px-5 py-4"><span class="text-sm text-gray-600">{{ $w->timeRange() }}</span></td>
                                        <td class="px-5 py-4 hidden lg:table-cell">
                                            @if ($w->capacity > 0)
                                                <span class="text-sm {{ $w->isFull() ? 'text-red-500' : 'text-gray-600' }}">{{ $w->registrationsCount() }}/{{ $w->capacity }}</span>
                                            @else
                                                <span class="text-sm text-gray-400">∞</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-4 text-center">
                                            @if ($w->isFull())
                                                <span class="text-xs text-red-500 font-medium">Penuh</span>
                                            @else
                                                <form action="{{ route('registrant.workshop.register', $w) }}" method="POST">
                                                    @csrf
                                                    <button class="px-3 py-1.5 text-xs font-medium rounded-lg bg-emerald-500 text-white hover:bg-emerald-600 transition">Daftar</button>
                                                </form>
                                            @endif
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
