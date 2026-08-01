<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans antialiased">

<div class="flex min-h-screen">

    {{-- ==================== SIDEBAR ==================== --}}
@include('admin.partials.sidebar')

    {{-- ==================== MAIN CONTENT ==================== --}}
    <main class="flex-1 lg:ml-64">

        {{-- Top bar --}}
        <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200">
            <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                {{-- Mobile hamburger + breadcrumb --}}
                <div class="flex items-center gap-4">
                    {{-- Mobile menu toggle --}}
                    <button id="sidebarToggle" class="lg:hidden p-2 -ml-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">Dashboard</h1>
                        <p class="text-xs text-gray-500">Registrant data overview</p>
                    </div>
                </div>

                {{-- Right side --}}
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.registrants.export-csv') }}"
                       class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export
                    </a>
                    <a href="{{ route('admin.registrants.index') }}"
                       class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Manage Registrants
                    </a>
                </div>
            </div>
        </header>

        {{-- Page content --}}
        <div class="p-4 sm:p-6 lg:p-8 space-y-6">

            {{-- Flash messages --}}
            @include('admin.partials.notification')

            {{-- Registration Form Toggle (Super Admin only) --}}
            @if (Auth::user()->isSuperAdmin())
            @php $forcedOpen = \Illuminate\Support\Facades\Cache::get('registration_forced_open', false); @endphp
            <div class="flex items-center gap-4 bg-white rounded-2xl border border-gray-200 px-5 py-4 shadow-sm">
                <div>
                    <p class="text-sm font-bold text-gray-900">Registration Form</p>
                    <p class="text-xs text-gray-500">
                        Status:
                        @if ($forcedOpen)
                            <span class="text-emerald-600 font-semibold">Forced OPEN</span> — form open regardless of countdown
                        @else
                            <span class="text-amber-600 font-semibold">Follows Countdown</span> — opens automatically on 13 July 2026
                        @endif
                    </p>
                </div>
                <form action="{{ route('admin.toggle-registration') }}" method="POST" class="ml-auto">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-sm font-semibold rounded-xl transition {{ $forcedOpen ? 'bg-red-500 text-white hover:bg-red-600' : 'bg-emerald-500 text-white hover:bg-emerald-600' }}">
                        {{ $forcedOpen ? 'Close Registration' : 'Force Open Registration' }}
                    </button>
                </form>
            </div>
            @endif

            {{-- ===== ROW 1: Welcome + Today's Stats ===== --}}
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                {{-- Welcome card --}}
                <div class="lg:col-span-2 bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl p-6 text-white shadow-lg">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-indigo-200 text-sm font-medium">Welcome back,</p>
                            <h2 class="text-2xl font-bold mt-1">{{ Auth::user()->name }}</h2>
                            <p class="text-indigo-200 text-sm mt-1">{{ now()->format('l, d F Y') }}</p>
                        </div>
                        <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-6 flex items-center gap-6">
                        <div>
                            <p class="text-3xl font-bold" data-stat="todayCount">{{ $todayCount }}</p>
                            <p class="text-indigo-200 text-xs">registrations today</p>
                        </div>
                        <div class="h-10 w-px bg-white/20"></div>
                        <div>
                            <p class="text-3xl font-bold {{ $trend >= 0 ? 'text-emerald-300' : 'text-red-300' }}" data-stat="trend" data-trend="{{ $trend }}">
                                {{ $trend >= 0 ? '+' : '' }}{{ $trend }}%
                            </p>
                            <p class="text-indigo-200 text-xs">vs yesterday</p>
                        </div>
                        <div class="h-10 w-px bg-white/20"></div>
                        <div>
                            <p class="text-3xl font-bold text-amber-300" data-stat="pending">{{ $pending }}</p>
                            <p class="text-indigo-200 text-xs">pending review</p>
                        </div>
                    </div>
                </div>

                {{-- Mini status distribution --}}
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Status Distribution</h3>
                    @php $grand = max($total, 1); @endphp
                    <div class="space-y-3" id="status-distribution">
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="font-medium text-gray-700">Approved</span>
                                <span class="text-gray-500"><span data-stat="approved">{{ $approved }}</span> (<span data-stat="approvedPct">{{ round($approved/$grand*100) }}</span>%)</span>
                            </div>
                            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-500 rounded-full transition-all" data-statbar="approved" style="width: {{ $approved/$grand*100 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="font-medium text-gray-700">Pending</span>
                                <span class="text-gray-500"><span data-stat="pending2">{{ $pending }}</span> (<span data-stat="pendingPct">{{ round($pending/$grand*100) }}</span>%)</span>
                            </div>
                            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-amber-400 rounded-full transition-all" data-statbar="pending2" style="width: {{ $pending/$grand*100 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="font-medium text-gray-700">Rejected</span>
                                <span class="text-gray-500"><span data-stat="rejected">{{ $rejected }}</span> (<span data-stat="rejectedPct">{{ round($rejected/$grand*100) }}</span>%)</span>
                            </div>
                            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-red-400 rounded-full transition-all" data-statbar="rejected" style="width: {{ $rejected/$grand*100 }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-gray-100 flex justify-between text-xs">
                        <span class="text-gray-400">Total</span>
                        <span class="font-bold text-gray-900"><span data-stat="total">{{ $total }}</span></span>
                    </div>
                </div>

                {{-- Quick actions --}}
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Quick Actions</h3>
                    <div class="space-y-2.5">
                        <a href="{{ route('admin.registrants.index', ['status' => 'pending']) }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-amber-700 bg-amber-50 hover:bg-amber-100 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Review Pending (<span data-stat="pending">{{ $pending }}</span>)
                            @if ($stalePending > 0)
                                <span class="ml-auto text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-bold"><span data-stat="stalePending">{{ $stalePending }}</span> stale</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.registrants.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            All Registrants
                        </a>
                        <a href="{{ route('admin.registrants.export-csv') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Export Data
                        </a>
                        @if (Auth::user()->isSuperAdmin())
                        <a href="{{ route('admin.management.backup.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-purple-700 bg-purple-50 hover:bg-purple-100 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 1.657 3.582 3 8 3s8-1.343 8-3V7M4 7c0 1.657 3.582 3 8 3s8-1.343 8-3M4 7c0-1.657 3.582-3 8-3s8 1.343 8 3m0 5c0 1.657-3.582 3-8 3s-8-1.343-8-3"/>
                            </svg>
                            Database Backup
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ===== ROW 2: Chart + Stats Cards ===== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                {{-- Bar Chart --}}
                <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Registration Trend</h3>
                            <p class="text-xs text-gray-500">Last 14 days</p>
                        </div>
                        <div class="flex items-center gap-3 text-xs">
                            <span class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span> Approved
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-indigo-400"></span> Pending
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-red-400"></span> Rejected
                            </span>
                        </div>
                    </div>
                    <div class="flex items-end gap-1.5 h-36 relative" id="realtime-chart">
                        @foreach (array_reverse($chartData) as $bar)
                            <div class="flex-1 flex flex-col items-center gap-1 group relative chart-bar"
                                 data-date="{{ $bar['date'] }}"
                                 data-total="{{ $bar['total'] }}"
                                 data-approved="{{ $bar['approved'] }}"
                                 data-pending="{{ $bar['pending'] }}"
                                 data-rejected="{{ $bar['rejected'] }}">
                                <div class="w-full flex flex-col-reverse" style="height: 140px;">
                                    @if ($bar['approved'] > 0)
                                        <div class="w-full bg-emerald-400 rounded-t transition-all duration-500 hover:bg-emerald-500 chart-bar-approved"
                                             style="height: {{ max(2, $bar['approved'] / $maxDaily * 130) }}px"></div>
                                    @endif
                                    @if ($bar['rejected'] > 0)
                                        <div class="w-full bg-red-400 rounded-t transition-all duration-500 hover:bg-red-500 chart-bar-rejected"
                                             style="height: {{ max(2, $bar['rejected'] / $maxDaily * 130) }}px"></div>
                                    @endif
                                    @if ($bar['pending'] > 0)
                                        <div class="w-full bg-indigo-400 rounded-t transition-all duration-500 hover:bg-indigo-500 chart-bar-pending"
                                             style="height: {{ max(2, $bar['pending'] / $maxDaily * 130) }}px"></div>
                                    @endif
                                    @if ($bar['total'] === 0)
                                        <div class="w-full bg-gray-100 rounded-t chart-bar-empty" style="height: 2px"></div>
                                    @endif
                                </div>
                                {{-- Label angka di atas bar (hidden default, muncul saat hover/click) --}}
                                <div class="chart-bar-label text-[9px] font-bold text-gray-700 opacity-0 group-hover:opacity-100 transition-opacity duration-200 leading-none mt-0.5">
                                    {{ $bar['total'] }}
                                </div>
                                <span class="text-[10px] text-gray-400 font-medium -mt-0.5">{{ $bar['day'] }}</span>
                            </div>
                        @endforeach
                        {{-- Tooltip --}}
                        <div id="chartTooltip" class="absolute z-50 hidden pointer-events-none bg-gray-900 text-white text-xs rounded-lg shadow-lg px-3 py-2.5 leading-relaxed transition-opacity duration-150" style="min-width: 140px;"></div>
                    </div>
                </div>

                {{-- Summary mini-cards --}}
                <div class="space-y-4">
                    <a href="{{ route('admin.registrants.index', ['status' => 'all']) }}"
                       class="block bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition group">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Registrants</p>
                                <p class="text-3xl font-bold text-gray-900 mt-1" data-stat="total">{{ $total }}</p>
                            </div>
                            <div class="w-11 h-11 bg-gray-100 rounded-xl flex items-center justify-center group-hover:bg-gray-200 transition">
                                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('admin.workshops.index') }}"
                       class="block bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition group">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Workshops</p>
                                <p class="text-3xl font-bold text-gray-900 mt-1" data-stat="workshopCount">{{ $workshopCount }}</p>
                            </div>
                            <div class="w-11 h-11 bg-indigo-50 rounded-xl flex items-center justify-center group-hover:bg-indigo-100 transition">
                                <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-2"><span data-stat="workshopRegistrations">{{ $workshopRegistrations }}</span> total registrations</p>
                    </a>
                    <a href="{{ route('admin.registrants.index', ['status' => 'pending']) }}"
                       class="block bg-white rounded-2xl p-5 border border-yellow-100 shadow-sm hover:shadow-md transition group">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold text-yellow-600 uppercase tracking-wider">Pending</p>
                                <p class="text-3xl font-bold text-yellow-700 mt-1" data-stat="pending">{{ $pending }}</p>
                            </div>
                            <div class="w-11 h-11 bg-yellow-100 rounded-xl flex items-center justify-center group-hover:bg-yellow-200 transition">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        @if ($stalePending > 0)
                            <p class="text-xs text-red-500 mt-2 font-medium">⚠ <span data-stat="stalePending">{{ $stalePending }}</span> pending for &gt;2 days</p>
                        @endif
                    </a>
                </div>
            </div>

            {{-- ===== ROW 3: Daily Registration Data Table ===== --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">Registrations Per Day</h3>
                        <p class="text-xs text-gray-500">Last {{ $dailyTotalDays }} days — detailed breakdown</p>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-gray-500">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span> Approved
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-400"></span> Pending
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-400"></span> Rejected
                        </span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100">
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Day</th>
                                <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Approved</th>
                                <th class="text-center px-5 py-3 text-xs font-semibold text-indigo-700 uppercase tracking-wider bg-indigo-50/50">Pending</th>
                                <th class="text-center px-5 py-3 text-xs font-semibold text-indigo-600 uppercase tracking-wider">Pending %</th>
                                <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Rejected</th>
                                <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Bar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50" id="daily-table-body">
                            @foreach ($dailyPage as $row)
                            @php
                                $pendingPct = $row['total'] > 0 ? round($row['pending'] / $row['total'] * 100) : 0;
                                $isHighPending = $pendingPct >= 50 && $row['pending'] > 0;
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition daily-row cursor-pointer {{ $isHighPending ? 'bg-indigo-50/30' : '' }}"
                                data-date="{{ $row['date'] }}"
                                data-total="{{ $row['total'] }}"
                                data-approved="{{ $row['approved'] }}"
                                data-pending="{{ $row['pending'] }}"
                                data-rejected="{{ $row['rejected'] }}"
                                onclick="openDailyDetail('{{ $row['date'] }}', 'pending')">
                                <td class="px-5 py-3 text-gray-900 font-medium whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($row['date'])->format('d M Y') }}
                                </td>
                                <td class="px-5 py-3 text-gray-500 whitespace-nowrap">
                                    @php
                                        $dayName = \Carbon\Carbon::parse($row['date'])->isoFormat('dddd');
                                    @endphp
                                    {{ $dayName }}
                                </td>
                                <td class="px-5 py-3 text-center font-bold text-gray-900">{{ $row['total'] }}</td>
                                <td class="px-5 py-3 text-center">
                                    @if ($row['approved'] > 0)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">{{ $row['approved'] }}</span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-center">
                                    @if ($row['pending'] > 0)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold {{ $isHighPending ? 'bg-indigo-200 text-indigo-800' : 'bg-indigo-50 text-indigo-700' }}">{{ $row['pending'] }}</span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-center">
                                    @if ($row['pending'] > 0)
                                        <div class="flex items-center gap-1.5 justify-center">
                                            <span class="text-xs font-bold {{ $isHighPending ? 'text-indigo-700' : 'text-indigo-500' }}">{{ $pendingPct }}%</span>
                                            <div class="w-12 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                                <div class="h-full rounded-full {{ $isHighPending ? 'bg-indigo-500' : 'bg-indigo-300' }}" style="width: {{ $pendingPct }}%"></div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-center">
                                    @if ($row['rejected'] > 0)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700">{{ $row['rejected'] }}</span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-0.5 h-4 w-full max-w-[80px] ml-auto mr-auto">
                                        @php
                                            $maxRow = max($dailyMax, 1);
                                            $approvedH = max(2, $row['approved'] / $maxRow * 16);
                                            $pendingH = max(2, $row['pending'] / $maxRow * 16);
                                            $rejectedH = max(2, $row['rejected'] / $maxRow * 16);
                                        @endphp
                                        @if ($row['approved'] > 0)
                                            <div class="flex-1 bg-emerald-400 rounded-t" style="height: {{ $approvedH }}px"></div>
                                        @endif
                                        @if ($row['pending'] > 0)
                                            <div class="flex-1 bg-indigo-400 rounded-t" style="height: {{ $pendingH }}px"></div>
                                        @endif
                                        @if ($row['rejected'] > 0)
                                            <div class="flex-1 bg-red-400 rounded-t" style="height: {{ $rejectedH }}px"></div>
                                        @endif
                                        @if ($row['total'] === 0)
                                            <div class="flex-1 bg-gray-100 rounded-t" style="height: 2px"></div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- Footer summary --}}
                <div class="bg-gray-50/50 border-t border-gray-100 px-5 py-3 flex flex-col lg:flex-row items-center justify-between gap-3 text-xs text-gray-500">
                    <span class="flex flex-wrap items-center gap-x-4 gap-y-1">
                        <span>Showing <strong id="dailyFooterRange">{{ $dailyPagination->total() > 0 ? $dailyPagination->firstItem() . '–' . $dailyPagination->lastItem() : '0–0' }}</strong> of <strong id="dailyFooterTotalDays">{{ $dailyPagination->total() }}</strong> days</span>
                        <span class="font-medium text-gray-700">
                            Total:
                            @php
                                $grandTotal = collect($dailyData)->sum('total');
                                $grandApproved = collect($dailyData)->sum('approved');
                                $grandPending = collect($dailyData)->sum('pending');
                                $grandRejected = collect($dailyData)->sum('rejected');
                            @endphp
                            <span class="text-gray-900 font-bold" id="dailyFooterTotal">{{ $grandTotal }}</span> registrations
                            &middot;
                            <span class="text-emerald-600" id="dailyFooterApproved">{{ $grandApproved }}</span> approved
                            &middot;
                            <span class="text-indigo-600" id="dailyFooterPending">{{ $grandPending }}</span> pending
                            &middot;
                            <span class="text-red-600" id="dailyFooterRejected">{{ $grandRejected }}</span> rejected
                        </span>
                    </span>
                    <div class="flex items-center gap-1">
                        {{ $dailyPagination->links() }}
                    </div>
                </div>
            </div>

            {{-- ===== ROW 4: Recent Registrants + Workshop Registrations ===== --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                {{-- Recent registrants (compact list, not a full table) --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Recent Registrants</h3>
                            <p class="text-xs text-gray-500">Latest 7 registrations</p>
                        </div>
                        <a href="{{ route('admin.registrants.index') }}"
                           class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition">
                            View All &rarr;
                        </a>
                    </div>
                    <div class="divide-y divide-gray-50" id="realtime-recent">
                        @forelse ($recentRegistrants as $r)
                            <a href="{{ route('admin.registrants.show', $r['id']) }}"
                               class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50/50 transition">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    {{ $r['initial'] }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $r['name'] }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $r['email'] }}</p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    @if ($r['status'] === 'approved')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700">Approved</span>
                                    @elseif ($r['status'] === 'rejected')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-50 text-red-700">Rejected</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700">Pending</span>
                                    @endif
                                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $r['timeAgo'] }}</p>
                                </div>
                            </a>
                        @empty
                            <div class="px-5 py-10 text-center">
                                <p class="text-gray-400 text-sm">No registrants yet</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Approval stats, check-in, sources --}}
                <div class="space-y-4">
                    {{-- Registration Overview --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                        <h3 class="text-sm font-bold text-gray-900 mb-4">Registration Overview</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-emerald-50 rounded-xl p-4 text-center">
                                <p class="text-2xl font-bold text-emerald-700" data-stat="approved">{{ $approved }}</p>
                                <p class="text-xs text-emerald-600 mt-1">Approved</p>
                                @if ($total > 0)
                                <div class="mt-2 h-1.5 bg-emerald-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $approved/max($total,1)*100 }}%"></div>
                                </div>
                                @endif
                            </div>
                            <div class="bg-amber-50 rounded-xl p-4 text-center">
                                <p class="text-2xl font-bold text-amber-700" data-stat="pending">{{ $pending }}</p>
                                <p class="text-xs text-amber-600 mt-1">Pending</p>
                                @if ($total > 0)
                                <div class="mt-2 h-1.5 bg-amber-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-amber-400 rounded-full" style="width: {{ $pending/max($total,1)*100 }}%"></div>
                                </div>
                                @endif
                            </div>
                            <div class="bg-red-50 rounded-xl p-4 text-center">
                                <p class="text-2xl font-bold text-red-600" data-stat="rejected">{{ $rejected }}</p>
                                <p class="text-xs text-red-500 mt-1">Rejected</p>
                                @if ($total > 0)
                                <div class="mt-2 h-1.5 bg-red-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-red-400 rounded-full" style="width: {{ $rejected/max($total,1)*100 }}%"></div>
                                </div>
                                @endif
                            </div>
                            <div class="bg-indigo-50 rounded-xl p-4 text-center">
                                <p class="text-2xl font-bold text-indigo-700" data-stat="workshopRegistrations">{{ $workshopRegistrations }}</p>
                                <p class="text-xs text-indigo-600 mt-1">Workshop Regs</p>
                                @if ($workshopCount > 0)
                                <p class="text-[10px] text-indigo-400 mt-1">{{ round($workshopRegistrations/max($workshopCount,1)) }}/workshop avg</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Source Tracking --}}
                    @if ($topSources->count() > 0)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                        <h3 class="text-sm font-bold text-gray-900 mb-3">Top Registration Sources</h3>
                        <div class="space-y-2.5" id="realtime-sources">
                            @foreach ($topSources as $src)
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-medium text-gray-600 w-24 truncate">{{ $src->utm_source ?: 'Direct' }}</span>
                                <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-indigo-400 rounded-full" style="width: {{ $src->total/max($approved,1)*100 }}%"></div>
                                </div>
                                <span class="text-xs font-semibold text-gray-700 w-10 text-right">{{ $src->total }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Referral & Waitlist --}}
                    <div class="grid grid-cols-2 gap-4">
                        @if ($referralCount > 0)
                        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                            <p class="text-xs text-gray-400 uppercase tracking-wider">Referral Codes</p>
                            <p class="text-xl font-bold text-gray-900 mt-1"><span data-stat="referralCount">{{ $referralCount }}</span></p>
                            <p class="text-xs text-gray-500">registrants with referral</p>
                        </div>
                        @endif
                        @if ($workshopWaitlistTotal > 0)
                        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                            <p class="text-xs text-gray-400 uppercase tracking-wider">Waitlist</p>
                            <p class="text-xl font-bold text-amber-600 mt-1"><span data-stat="workshopWaitlistTotal">{{ $workshopWaitlistTotal }}</span></p>
                            <p class="text-xs text-gray-500">on workshop waitlist</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

{{-- Daily Detail Modal --}}
<div id="dailyModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeDailyModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[85vh] flex flex-col animate-fade-in">
            {{-- Modal header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
                <div>
                    <h3 class="text-lg font-bold text-gray-900" id="modalTitle">Registrations</h3>
                    <p class="text-sm text-gray-500" id="modalSubtitle"></p>
                </div>
                <button onclick="closeDailyModal()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            {{-- Modal stats bar --}}
            <div class="flex items-center justify-between px-6 py-3 bg-gray-50/50 border-b border-gray-100 text-xs">
                <div class="flex items-center gap-4" id="modalStats">
                    <span>Total: <strong id="modalTotal" class="text-gray-900">0</strong></span>
                    <span class="text-emerald-600">Approved: <strong id="modalApproved">0</strong></span>
                    <span class="text-indigo-600">Pending: <strong id="modalPending">0</strong></span>
                    <span class="text-red-600">Rejected: <strong id="modalRejected">0</strong></span>
                </div>
                <div class="flex items-center gap-1" id="modalFilters">
                    <button onclick="filterDaily('all')" class="px-2.5 py-1 rounded-lg text-xs font-medium transition daily-filter daily-filter-all bg-gray-200 text-gray-700">All</button>
                    <button onclick="filterDaily('pending')" class="px-2.5 py-1 rounded-lg text-xs font-medium transition daily-filter daily-filter-pending bg-gray-100 text-gray-500 hover:bg-indigo-50 hover:text-indigo-700">Pending</button>
                    <button onclick="filterDaily('approved')" class="px-2.5 py-1 rounded-lg text-xs font-medium transition daily-filter daily-filter-approved bg-gray-100 text-gray-500 hover:bg-emerald-50 hover:text-emerald-700">Approved</button>
                    <button onclick="filterDaily('rejected')" class="px-2.5 py-1 rounded-lg text-xs font-medium transition daily-filter daily-filter-rejected bg-gray-100 text-gray-500 hover:bg-red-50 hover:text-red-700">Rejected</button>
                </div>
            </div>
            {{-- Modal body --}}
            <div class="flex-1 overflow-y-auto px-6 py-4" id="modalBody">
                <div class="text-center text-gray-400 py-10" id="modalLoading">
                    <svg class="w-8 h-8 mx-auto animate-spin mb-3" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <p>Loading registrants...</p>
                </div>
                <div id="modalContent" class="hidden"></div>
            </div>
            {{-- Modal footer --}}
            <div class="px-6 py-3 border-t border-gray-100 flex justify-end flex-shrink-0">
                <button onclick="closeDailyModal()" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Reject Reason Modal (client recommendation) --}}
<div id="rejectReasonModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeRejectReasonModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md animate-fade-in">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Reject Reason</h3>
                    <p class="text-sm text-gray-500">Select a reason for <span id="rrName" class="font-medium text-gray-700"></span></p>
                </div>
                <button onclick="closeRejectReasonModal()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="px-6 py-5">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Reason</label>
                <select id="rrSelect" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-red-100 focus:border-red-300 outline-none bg-white">
                    <option value="">— Select a reason —</option>
                    @foreach (config('client_reasons.reject') as $reason)
                        <option value="{{ $reason }}">{{ $reason }}</option>
                    @endforeach
                </select>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-2">
                <button onclick="closeRejectReasonModal()" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition">Cancel</button>
                <button onclick="confirmRejectReason()" class="px-4 py-2 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-xl transition">Reject</button>
            </div>
        </div>
    </div>
</div>

{{-- Mobile sidebar overlay --}}
<div id="sidebarOverlay" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-30 hidden lg:hidden" onclick="toggleSidebar()"></div>
<div id="mobileSidebar" class="fixed inset-y-0 left-0 w-64 bg-white border-r border-gray-200 z-40 transform -translate-x-full transition-transform lg:hidden">
    <div class="flex items-center justify-between h-16 px-6 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <span class="text-lg font-bold text-gray-900">AdminPanel</span>
        </div>
        <button onclick="toggleSidebar()" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <nav class="px-3 py-6 space-y-1">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium bg-indigo-50 text-indigo-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/>
            </svg>
            Dashboard
        </a>
        <a href="{{ route('admin.registrants.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Registrants
        </a>
        <a href="{{ route('admin.agenda.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Agenda
        </a>
        <a href="{{ route('admin.templates.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Template Email
        </a>
        <a href="{{ route('admin.workshops.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-100">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Workshop
        </a>
        <hr class="my-4">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-red-500 hover:bg-red-50 w-full">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2h5a2 2 0 012 2v1"/>
                </svg>
                Logout
            </button>
        </form>
    </nav>
</div>

<script>
    // ---- Mobile Sidebar ----
    function toggleSidebar() {
        const sidebar = document.getElementById('mobileSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const isOpen = sidebar.classList.contains('-translate-x-full');
        if (isOpen) {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }
    }
    document.getElementById('sidebarToggle').addEventListener('click', toggleSidebar);
    document.getElementById('sidebarOverlay').addEventListener('click', toggleSidebar);
</script>

<style>
    .animate-fade-in { animation: fadeIn 0.2s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    .realtime-updated { animation: pulse-green 0.6s ease-out; }
    @keyframes pulse-green {
        0% { background-color: rgba(16, 185, 129, 0.3); }
        100% { background-color: transparent; }
    }
    .realtime-updated-red { animation: pulse-red 0.6s ease-out; }
    @keyframes pulse-red {
        0% { background-color: rgba(239, 68, 68, 0.3); }
        100% { background-color: transparent; }
    }

    /* ── Chart Tooltip ── */
    #chartTooltip {
        font-family: 'Inter', system-ui, sans-serif;
    }
    #chartTooltip::after {
        content: '';
        position: absolute;
        bottom: -4px;
        left: 50%;
        transform: translateX(-50%) rotate(45deg);
        width: 8px;
        height: 8px;
        background: #111827;
    }

    /* ── Chart bar click active state ── */
    .chart-bar.show-labels .chart-bar-label {
        opacity: 1 !important;
    }
    .chart-bar.show-labels .chart-bar-approved,
    .chart-bar.show-labels .chart-bar-pending,
    .chart-bar.show-labels .chart-bar-rejected {
        filter: brightness(1.05);
    }
</style>

{{-- Real-time polling (every 8 detik) --}}
<script>
(function(){
    var pollUrl = '{{ route("admin.dashboard.data") }}';
    var pollInterval = 8000; // 8 detik

    function updateDashboard(data) {
        // ── Update semua data-stat ──
        document.querySelectorAll('[data-stat]').forEach(function(el) {
            var key = el.getAttribute('data-stat');
            var val = data[key];
            if (val === undefined) return;

            var oldVal = el.textContent.trim();
            // For trend, handle +/- prefix
            if (key === 'trend') {
                var prefix = val >= 0 ? '+' : '';
                var newText = prefix + val + '%';
                if (oldVal !== newText) {
                    el.textContent = newText;
                    el.className = el.className.replace(/text-emerald-300|text-red-300/g, '');
                    el.classList.add(val >= 0 ? 'text-emerald-300' : 'text-red-300');
                    highlight(el);
                }
                return;
            }
            // For percentages
            if (key === 'approvedPct' || key === 'pendingPct' || key === 'rejectedPct') {
                var newVal = Math.round(val) + '%';
                if (oldVal !== newVal) {
                    el.textContent = newVal;
                    highlight(el);
                }
                return;
            }
            // Numeric values
            var numVal = Number(val);
            var oldNum = Number(oldVal.replace(/[+,%]/g, ''));
            if (!isNaN(numVal) && oldNum !== numVal) {
                el.textContent = numVal;
                highlight(el);
            }
        });

        // ── Update progress bars ──
        var total = data.total || 1;
        document.querySelectorAll('[data-statbar]').forEach(function(el) {
            var key = el.getAttribute('data-statbar');
            var val = data[key === 'pending2' ? 'pending' : key === 'approved' ? 'approved' : 'rejected'];
            if (val === undefined) return;
            var pct = Math.min(100, Math.round(val / total * 100));
            el.style.width = pct + '%';
        });

        // ── Update chart bars ──
        if (data.maxDaily > 0 && data.chartData) {
            var bars = document.querySelectorAll('#realtime-chart .chart-bar');
            // Chart renders oldest → newest (newest on the right), so reverse for index mapping
            data.chartData.slice().reverse().forEach(function(item, i) {
                if (bars[i]) {
                    // Update data attributes for tooltip
                    bars[i].setAttribute('data-date', item.date);
                    bars[i].setAttribute('data-total', item.total);
                    bars[i].setAttribute('data-approved', item.approved);
                    bars[i].setAttribute('data-pending', item.pending);
                    bars[i].setAttribute('data-rejected', item.rejected);

                    // Update number label
                    var label = bars[i].querySelector('.chart-bar-label');
                    if (label) {
                        label.textContent = item.total;
                    }

                    var approvedBar = bars[i].querySelector('.chart-bar-approved');
                    var rejectedBar = bars[i].querySelector('.chart-bar-rejected');
                    var pendingBar = bars[i].querySelector('.chart-bar-pending');
                    var emptyBar = bars[i].querySelector('.chart-bar-empty');
                    var maxH = data.maxDaily || 1;
                    var container = bars[i].querySelector('.w-full');

                    // Approved (bottom of stack)
                    if (item.approved > 0) {
                        if (!approvedBar) {
                            approvedBar = document.createElement('div');
                            approvedBar.className = 'w-full bg-emerald-400 rounded-t transition-all duration-500 chart-bar-approved';
                            container.appendChild(approvedBar);
                        }
                        approvedBar.style.height = Math.max(2, item.approved / maxH * 130) + 'px';
                    } else if (approvedBar) { approvedBar.style.height = '0px'; }

                    // Rejected (middle of stack)
                    if (item.rejected > 0) {
                        if (!rejectedBar) {
                            rejectedBar = document.createElement('div');
                            rejectedBar.className = 'w-full bg-red-400 rounded-t transition-all duration-500 chart-bar-rejected';
                            // Insert after approvedBar (before pendingBar)
                            if (pendingBar) {
                                container.insertBefore(rejectedBar, pendingBar);
                            } else {
                                container.appendChild(rejectedBar);
                            }
                        }
                        rejectedBar.style.height = Math.max(2, item.rejected / maxH * 130) + 'px';
                    } else if (rejectedBar) { rejectedBar.style.height = '0px'; }

                    // Pending (top of stack)
                    if (item.pending > 0) {
                        if (!pendingBar) {
                            pendingBar = document.createElement('div');
                            pendingBar.className = 'w-full bg-indigo-400 rounded-t transition-all duration-500 chart-bar-pending';
                            container.appendChild(pendingBar);
                        }
                        pendingBar.style.height = Math.max(2, item.pending / maxH * 130) + 'px';
                    } else if (pendingBar) { pendingBar.style.height = '0px'; }

                    if (item.total === 0 && !emptyBar) {
                        emptyBar = document.createElement('div');
                        emptyBar.className = 'w-full bg-gray-100 rounded-t chart-bar-empty';
                        emptyBar.style.height = '2px';
                        container.appendChild(emptyBar);
                    } else if (item.total > 0 && emptyBar) { emptyBar.remove(); }
                }
            });
        }

        // ── Update recent registrants ──
        if (data.recentRegistrants) {
            var recentContainer = document.getElementById('realtime-recent');
            if (recentContainer) {
                var html = '';
                data.recentRegistrants.forEach(function(r) {
                    var statusClass, statusText;
                    if (r.status === 'approved') { statusClass = 'bg-emerald-50 text-emerald-700'; statusText = 'Approved'; }
                    else if (r.status === 'rejected') { statusClass = 'bg-red-50 text-red-700'; statusText = 'Rejected'; }
                    else { statusClass = 'bg-amber-50 text-amber-700'; statusText = 'Pending'; }
                    html += '<a href="/admin/registrants/' + r.id + '" class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50/50 transition">'
                        + '<div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">' + r.initial + '</div>'
                        + '<div class="flex-1 min-w-0"><p class="text-sm font-semibold text-gray-900 truncate">' + escapeHtml(r.name) + '</p><p class="text-xs text-gray-500 truncate">' + escapeHtml(r.email) + '</p></div>'
                        + '<div class="text-right flex-shrink-0"><span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold ' + statusClass + '">' + statusText + '</span><p class="text-[10px] text-gray-400 mt-0.5">' + escapeHtml(r.timeAgo) + '</p></div>'
                        + '</a>';
                });
                if (!html) html = '<div class="px-5 py-10 text-center"><p class="text-gray-400 text-sm">No registrants yet</p></div>';
                recentContainer.innerHTML = html;
            }
        }

        // ── Source tracking ──
        if (data.topSources && data.topSources.length > 0) {
            var srcContainer = document.getElementById('realtime-sources');
            if (srcContainer) {
                var srcHtml = '';
                var maxSrc = Math.max(data.approved, 1);
                data.topSources.forEach(function(src) {
                    var pct = Math.min(100, Math.round(src.total / maxSrc * 100));
                    srcHtml += '<div class="flex items-center gap-3">'
                        + '<span class="text-xs font-medium text-gray-600 w-24 truncate">' + escapeHtml(src.utm_source || 'Direct') + '</span>'
                        + '<div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden"><div class="h-full bg-indigo-400 rounded-full" style="width:' + pct + '%"></div></div>'
                        + '<span class="text-xs font-semibold text-gray-700 w-10 text-right">' + src.total + '</span></div>';
                });
                srcContainer.innerHTML = srcHtml;
            }
        }

        // ── Update daily table ──
        if (data.dailyData && data.dailyMax) {
            var tableBody = document.getElementById('daily-table-body');
            if (tableBody) {
                var maxRow = Math.max(data.dailyMax, 1);
                data.dailyData.forEach(function(item) {
                    var row = tableBody.querySelector('.daily-row[data-date="' + item.date + '"]');
                    if (!row) return;

                    var oldTotal = row.getAttribute('data-total');
                    if (oldTotal === String(item.total)) return; // no change

                    row.setAttribute('data-total', item.total);
                    row.setAttribute('data-approved', item.approved);
                    row.setAttribute('data-pending', item.pending);
                    row.setAttribute('data-rejected', item.rejected);

                    // Update total cell
                    var cells = row.querySelectorAll('td');
                    if (cells.length >= 3) cells[2].textContent = item.total;

                    // Update approved badge
                    if (cells.length >= 4) {
                        if (item.approved > 0) {
                            cells[3].innerHTML = '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">' + item.approved + '</span>';
                        } else {
                            cells[3].innerHTML = '<span class="text-gray-300">\u2014</span>';
                        }
                    }

                    // Update pending badge (col 4)
                    if (cells.length >= 5) {
                        var pct = item.total > 0 ? Math.round(item.pending / item.total * 100) : 0;
                        var isHigh = pct >= 50 && item.pending > 0;
                        if (item.pending > 0) {
                            cells[4].innerHTML = '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold ' + (isHigh ? 'bg-indigo-200 text-indigo-800' : 'bg-indigo-50 text-indigo-700') + '">' + item.pending + '</span>';
                        } else {
                            cells[4].innerHTML = '<span class="text-gray-300">\u2014</span>';
                        }

                        // Update pending % (col 5)
                        if (item.pending > 0) {
                            cells[5].innerHTML = '<div class="flex items-center gap-1.5 justify-center"><span class="text-xs font-bold ' + (isHigh ? 'text-indigo-700' : 'text-indigo-500') + '">' + pct + '%</span><div class="w-12 h-1.5 bg-gray-100 rounded-full overflow-hidden"><div class="h-full rounded-full ' + (isHigh ? 'bg-indigo-500' : 'bg-indigo-300') + '" style="width:' + pct + '%"></div></div></div>';
                        } else {
                            cells[5].innerHTML = '<span class="text-gray-300">\u2014</span>';
                        }

                        // Highlight row if high pending
                        if (isHigh) {
                            row.classList.add('bg-indigo-50/30');
                        } else {
                            row.classList.remove('bg-indigo-50/30');
                        }
                    }

                    // Update rejected badge (col 6)
                    if (cells.length >= 7) {
                        if (item.rejected > 0) {
                            cells[6].innerHTML = '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700">' + item.rejected + '</span>';
                        } else {
                            cells[6].innerHTML = '<span class="text-gray-300">\u2014</span>';
                        }
                    }

                    // Update mini bar (col 7)
                    if (cells.length >= 8) {
                        var approvedH = Math.max(2, item.approved / maxRow * 16);
                        var pendingH = Math.max(2, item.pending / maxRow * 16);
                        var rejectedH = Math.max(2, item.rejected / maxRow * 16);
                        var barHtml = '<div class="flex items-center gap-0.5 h-4 w-full max-w-[80px] ml-auto mr-auto">';
                        if (item.approved > 0) barHtml += '<div class="flex-1 bg-emerald-400 rounded-t" style="height:' + approvedH + 'px"></div>';
                        if (item.pending > 0) barHtml += '<div class="flex-1 bg-indigo-400 rounded-t" style="height:' + pendingH + 'px"></div>';
                        if (item.rejected > 0) barHtml += '<div class="flex-1 bg-red-400 rounded-t" style="height:' + rejectedH + 'px"></div>';
                        if (item.total === 0) barHtml += '<div class="flex-1 bg-gray-100 rounded-t" style="height:2px"></div>';
                        barHtml += '</div>';
                        cells[7].innerHTML = barHtml;
                    }

                    // Highlight the row
                    row.classList.remove('realtime-updated');
                    void row.offsetWidth;
                    row.classList.add('realtime-updated');
                });

                // Update footer summary (totals only — preserve pagination links)
                var el = function(id) { return document.getElementById(id); };
                if (el('dailyFooterTotal')) {
                    var grandTotal = data.dailyData.reduce(function(sum, d) { return sum + d.total; }, 0);
                    var grandApproved = data.dailyData.reduce(function(sum, d) { return sum + d.approved; }, 0);
                    var grandPending = data.dailyData.reduce(function(sum, d) { return sum + d.pending; }, 0);
                    var grandRejected = data.dailyData.reduce(function(sum, d) { return sum + d.rejected; }, 0);
                    el('dailyFooterTotal').textContent = grandTotal;
                    el('dailyFooterApproved').textContent = grandApproved;
                    el('dailyFooterPending').textContent = grandPending;
                    el('dailyFooterRejected').textContent = grandRejected;
                }
            }
        }
    }

    function highlight(el) {
        el.classList.remove('realtime-updated', 'realtime-updated-red');
        // Force reflow
        void el.offsetWidth;
        el.classList.add('realtime-updated');
    }

    // ═══════════════════════════════════════════════
    //  Chart Tooltip & Click Interaction
    // ═══════════════════════════════════════════════

    var chartEl = document.getElementById('realtime-chart');
    var tooltipEl = document.getElementById('chartTooltip');

    function initChartTooltip() {
        if (!chartEl || !tooltipEl) return;

        // Remove old listeners by cloning (to avoid duplicates after polling refresh)
        // We use event delegation on the chart container instead
    }

    // Delegate mouse events on the chart container
    chartEl.addEventListener('mouseover', function(e) {
        var bar = e.target.closest('.chart-bar');
        if (!bar) { hideTooltip(); return; }
        showTooltip(bar, e);
    });

    chartEl.addEventListener('mousemove', function(e) {
        var bar = e.target.closest('.chart-bar');
        if (!bar) { hideTooltip(); return; }
        positionTooltip(bar, e);
    });

    chartEl.addEventListener('mouseleave', function(e) {
        // Only hide if not leaving to a child of chart
        if (e.relatedTarget && chartEl.contains(e.relatedTarget)) return;
        hideTooltip();
    });

    // Click to toggle persistent number labels
    chartEl.addEventListener('click', function(e) {
        var bar = e.target.closest('.chart-bar');
        if (!bar) return;
        e.stopPropagation();

        // Toggle .show-labels on this bar
        var wasActive = bar.classList.contains('show-labels');
        // Remove from all other bars
        chartEl.querySelectorAll('.chart-bar.show-labels').forEach(function(b) {
            b.classList.remove('show-labels');
        });
        if (!wasActive) {
            bar.classList.add('show-labels');
        }
    });

    // Click outside chart to remove all active labels
    document.addEventListener('click', function(e) {
        if (chartEl && !chartEl.contains(e.target)) {
            chartEl.querySelectorAll('.chart-bar.show-labels').forEach(function(b) {
                b.classList.remove('show-labels');
            });
        }
    });

    function showTooltip(bar, event) {
        var date = bar.getAttribute('data-date') || '';
        var total = bar.getAttribute('data-total') || '0';
        var approved = bar.getAttribute('data-approved') || '0';
        var pending = bar.getAttribute('data-pending') || '0';
        var rejected = bar.getAttribute('data-rejected') || '0';

        tooltipEl.innerHTML = ''
            + '<div class="font-semibold text-gray-100 mb-1.5">' + escapeHtml(date) + '</div>'
            + '<div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-emerald-400 inline-block"></span> Approved: <span class="font-semibold">' + approved + '</span></div>'
            + '<div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-indigo-400 inline-block"></span> Pending: <span class="font-semibold">' + pending + '</span></div>'
            + '<div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-red-400 inline-block"></span> Rejected: <span class="font-semibold">' + rejected + '</span></div>'
            + '<div class="border-t border-gray-700 mt-1.5 pt-1.5 flex items-center gap-2"><span class="font-semibold">Total:</span> <span class="font-bold text-white">' + total + '</span></div>';

        tooltipEl.classList.remove('hidden');
        positionTooltip(bar, event);
    }

    function positionTooltip(bar, event) {
        var rect = bar.getBoundingClientRect();
        var chartRect = chartEl.getBoundingClientRect();
        var tooltipRect = tooltipEl.getBoundingClientRect();

        // Position: centered above the bar, but use mouse X for horizontal
        var x = event.clientX - chartRect.left - tooltipRect.width / 2;
        var y = rect.top - chartRect.top - tooltipRect.height - 8;

        // Clamp horizontal so tooltip stays within chart
        if (x < 4) x = 4;
        if (x + tooltipRect.width > chartRect.width - 4) x = chartRect.width - tooltipRect.width - 4;

        tooltipEl.style.left = x + 'px';
        tooltipEl.style.top = y + 'px';
    }

    function hideTooltip() {
        if (tooltipEl) {
            tooltipEl.classList.add('hidden');
        }
    }

    // ── Poll setiap N detik ──
    setInterval(function() {
        fetch(pollUrl)
            .then(function(r) { return r.json(); })
            .then(updateDashboard)
            .catch(function(err) { /* silently ignore polling errors */ });
    }, pollInterval);
})();

// Shared helper (outside IIFE so modal code can use it)
function escapeHtml(text) {
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(text));
    return div.innerHTML;
}

// ═══════════════════════════════════════════════
//  Daily Detail Modal
// ═══════════════════════════════════════════════
// Build URL from current page origin to avoid cross-origin issues
var dailyDetailUrlBase = '{{ url('admin/dashboard/daily') }}/';
var dailyCurrentDate = null;
var dailyCurrentFilter = 'pending';
var dailyCurrentUserName = '{{ Auth::user()->name }}';

function openDailyDetail(date, status) {
    var modal = document.getElementById('dailyModal');
    if (!modal) return;

    dailyCurrentDate = date;
    dailyCurrentFilter = status || 'pending';

    modal.classList.remove('hidden');

    // Update filter buttons
    updateFilterButtons(dailyCurrentFilter);

    // Set loading state
    document.getElementById('modalTitle').textContent = 'Registrations — ' + date;
    document.getElementById('modalSubtitle').textContent = 'Loading...';
    document.getElementById('modalTotal').textContent = '…';
    document.getElementById('modalApproved').textContent = '…';
    document.getElementById('modalPending').textContent = '…';
    document.getElementById('modalRejected').textContent = '…';
    document.getElementById('modalLoading').classList.remove('hidden');
    document.getElementById('modalContent').classList.add('hidden');
    document.getElementById('modalContent').innerHTML = '';

    // Prevent body scroll
    document.body.style.overflow = 'hidden';

    // Fetch data
    fetch(dailyDetailUrlBase + encodeURIComponent(date) + '?status=' + encodeURIComponent(dailyCurrentFilter))
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var filterLabel = dailyCurrentFilter === 'all' ? '' : ' (' + dailyCurrentFilter.charAt(0).toUpperCase() + dailyCurrentFilter.slice(1) + ')';
            document.getElementById('modalTitle').textContent = 'Registrations — ' + data.date_formatted + filterLabel;
            document.getElementById('modalSubtitle').textContent = data.day_name;

            // Update stats
            document.getElementById('modalTotal').textContent = data.stats.total;
            document.getElementById('modalApproved').textContent = data.stats.approved;
            document.getElementById('modalPending').textContent = data.stats.pending;
            document.getElementById('modalRejected').textContent = data.stats.rejected;

            // Build registrants list
            var html = '';
            var csrfToken = '{{ csrf_token() }}';
            var isClient = {{ Auth::user()->isClient() ? 'true' : 'false' }};
            var currentUserName = '{{ Auth::user()->name }}';
            if (data.registrants.length === 0) {
                html = '<div class="text-center text-gray-400 py-10"><p>No registrants for this date.</p></div>';
            } else {
                html += '<div class="divide-y divide-gray-50">';
                data.registrants.forEach(function(r) {
                    var statusClass, statusText;
                    if (r.status === 'approved') { statusClass = 'bg-emerald-50 text-emerald-700'; statusText = 'Approved'; }
                    else if (r.status === 'rejected') { statusClass = 'bg-red-50 text-red-700'; statusText = 'Rejected'; }
                    else { statusClass = 'bg-amber-50 text-amber-700'; statusText = 'Pending'; }

                    html += '<div class="flex items-center gap-3 py-3 hover:bg-gray-50/50 transition rounded-lg px-2">'
                        + '<div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">'
                        + escapeHtml(r.initial) + '</div>'
                        + '<div class="flex-1 min-w-0">'
                        + '<div class="flex items-center gap-2">'
                        + '<a href="/admin/registrants/' + r.id + '" class="text-sm font-semibold text-gray-900 hover:text-indigo-600 truncate">' + escapeHtml(r.name) + '</a>'
                        + '<span class="text-[10px] text-gray-400 font-mono">#' + escapeHtml(r.unique_code) + '</span>'
                        + '</div>'
                        + '<div class="flex items-center gap-2 text-xs text-gray-500">'
                        + '<span>' + escapeHtml(r.email) + '</span>'
                        + (r.company ? '<span class="text-gray-300">|</span><span>' + escapeHtml(r.company) + '</span>' : '')
                        + (r.job_title ? '<span class="text-gray-300">|</span><span>' + escapeHtml(r.job_title) + '</span>' : '')
                        + '</div>'
                        + '</div>'
                        + '<div class="text-right flex-shrink-0 flex flex-col items-end gap-1">'
                        + '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold ' + statusClass + '">' + statusText + '</span>'
                        + (r.has_remark ? '<div class="text-right flex flex-col items-end"><span class="text-[10px] ' + (r.remark_action === 'approve' ? 'text-emerald-600' : 'text-red-600') + '">' + (r.remark_action === 'approve' ? '✅ Approve' : '❌ Reject') + (r.remark_by ? ' by ' + escapeHtml(r.remark_by) : '') + '</span>' + (r.remark ? '<span class="text-[10px] text-gray-500 max-w-[160px] truncate" title="' + escapeHtml(r.remark) + '">' + escapeHtml(r.remark) + '</span>' : '') + (r.remark_at ? '<span class="text-[9px] text-gray-400">' + escapeHtml(r.remark_at) + '</span>' : '') + '</div>' : '')
                        + (r.status === 'pending' ? '<div class="flex items-center gap-1 mt-1.5">'
                            + (isClient
                                ? '<span class="text-[9px] font-semibold text-gray-400 uppercase tracking-wider mr-0.5">Must be:</span>'
                                    + '<button onclick="dailyMustBe(' + r.id + ', \'approve\', this)" class="daily-mustbe daily-mustbe-' + r.id + ' px-2 py-0.5 rounded text-[10px] font-semibold border ' + (r.has_remark && r.remark_action === 'approve' ? 'bg-emerald-50 text-emerald-700 border-emerald-200 cursor-default' : r.has_remark ? 'bg-gray-50 text-gray-300 border-gray-100 cursor-default' : 'bg-gray-50 text-gray-500 border-gray-200 hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-200') + '"' + (r.has_remark ? ' disabled' : '') + '>✅</button>'
                                    + '<button onclick="openRejectReason(' + r.id + ', this)" data-name="' + escapeHtml(r.name) + '" class="daily-mustbe daily-mustbe-' + r.id + ' px-2 py-0.5 rounded text-[10px] font-semibold border ' + (r.has_remark && r.remark_action === 'reject' ? 'bg-red-50 text-red-700 border-red-200 cursor-default' : r.has_remark ? 'bg-gray-50 text-gray-300 border-gray-100 cursor-default' : 'bg-gray-50 text-gray-500 border-gray-200 hover:bg-red-50 hover:text-red-600 hover:border-red-200') + '"' + (r.has_remark ? ' disabled' : '') + '>❌</button>'
                                : '<form action="/admin/registrants/' + r.id + '/approve" method="POST" style="display:inline" onsubmit="return confirm(\'Approve ' + escapeHtml(r.name) + '?\')">'
                                    + '<input type="hidden" name="_token" value="' + csrfToken + '">'
                                    + '<button type="submit" class="p-1 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="Approve">'
                                    + '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>'
                                    + '</button></form>'
                                    + '<form action="/admin/registrants/' + r.id + '/reject" method="POST" style="display:inline" onsubmit="return confirm(\'Reject ' + escapeHtml(r.name) + '?\')">'
                                    + '<input type="hidden" name="_token" value="' + csrfToken + '">'
                                    + '<button type="submit" class="p-1 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Reject">'
                                    + '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>'
                                    + '</button></form>')
                            + '</div>' : '')
                        + '</div>'
                        + '</div>';
                });
                html += '</div>';
            }

            document.getElementById('modalLoading').classList.add('hidden');
            var content = document.getElementById('modalContent');
            content.innerHTML = html;
            content.classList.remove('hidden');
        })
        .catch(function(err) {
            document.getElementById('modalLoading').classList.add('hidden');
            var errorMsg = err.message || 'Unknown error';
            document.getElementById('modalContent').innerHTML = '<div class="text-center text-red-500 py-10"><p>Failed to load registrants.</p><p class="text-xs text-gray-400 mt-2">' + escapeHtml(errorMsg) + '</p></div>';
            document.getElementById('modalContent').classList.remove('hidden');
            console.error('Daily detail error:', err);
        });
}

function updateFilterButtons(status) {
    document.querySelectorAll('.daily-filter').forEach(function(btn) {
        btn.classList.remove('bg-gray-200', 'text-gray-700', 'bg-indigo-50', 'text-indigo-700', 'bg-emerald-50', 'text-emerald-700', 'bg-red-50', 'text-red-700');
        btn.classList.add('bg-gray-100', 'text-gray-500');
    });
    var active = document.querySelector('.daily-filter-' + status);
    if (active) {
        active.classList.remove('bg-gray-100', 'text-gray-500');
        if (status === 'all') active.classList.add('bg-gray-200', 'text-gray-700');
        else if (status === 'pending') active.classList.add('bg-indigo-50', 'text-indigo-700');
        else if (status === 'approved') active.classList.add('bg-emerald-50', 'text-emerald-700');
        else if (status === 'rejected') active.classList.add('bg-red-50', 'text-red-700');
    }
}

// ═══════════════════════════════════════════════
//  Must be : Approve / Reject (for client users)
// ═══════════════════════════════════════════════
var dailyMustBeUrl = '{{ url('admin/registrants') }}/';

// Reject reason modal state
var rrPending = { id: null, btn: null, name: '' };

function openRejectReason(id, btn, name) {
    rrPending = { id: id, btn: btn, name: name || '' };
    if (!rrPending.name && btn) {
        rrPending.name = btn.getAttribute('data-name') || '';
    }
    if (!rrPending.name && btn) {
        var row = btn.closest('.flex.items-center.gap-3');
        var nameEl = row && row.querySelector('a[href*="/admin/registrants/"]');
        if (nameEl) rrPending.name = nameEl.textContent;
    }
    document.getElementById('rrName').textContent = rrPending.name;
    var sel = document.getElementById('rrSelect');
    sel.value = '';
    document.getElementById('rejectReasonModal').classList.remove('hidden');
}

function closeRejectReasonModal() {
    document.getElementById('rejectReasonModal').classList.add('hidden');
}

function confirmRejectReason() {
    var reason = document.getElementById('rrSelect').value;
    if (!reason) {
        alert('Please select a reason.');
        return;
    }
    closeRejectReasonModal();
    dailyMustBe(rrPending.id, 'reject', rrPending.btn, reason);
}

function dailyMustBe(id, action, btn, reason) {
    var allBtns = document.querySelectorAll('.daily-mustbe-' + id);
    allBtns.forEach(function(b) { b.disabled = true; });

    fetch(dailyMustBeUrl + id + '/client-remark', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ client_remark: reason || '', client_remark_action: action }),
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            allBtns.forEach(function(b) {
                var isApprove = b.getAttribute('onclick')?.includes("'approve'");
                var isChosen = (isApprove && action === 'approve') || (!isApprove && action === 'reject');
                if (isChosen) {
                    b.className = 'daily-mustbe daily-mustbe-' + id + ' px-2 py-0.5 rounded text-[10px] font-semibold border cursor-default ' + (action === 'approve' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200');
                } else {
                    b.className = 'daily-mustbe daily-mustbe-' + id + ' px-2 py-0.5 rounded text-[10px] font-semibold border cursor-default bg-gray-50 text-gray-300 border-gray-100';
                }
                b.disabled = true;
                b.onclick = null;
            });

            // Update status badge in modal without page reload
            var badgeContainer = btn.closest('.text-right');
            if (badgeContainer) {
                var oldRemark = badgeContainer.querySelector('.daily-remark-label');
                if (oldRemark) oldRemark.remove();
                var remarkEl = document.createElement('div');
                remarkEl.className = 'daily-remark-label text-[10px] ' + (action === 'approve' ? 'text-emerald-600' : 'text-red-600');
                remarkEl.textContent = (action === 'approve' ? '✅ Approve' : '❌ Reject') + ' by ' + dailyCurrentUserName + (reason ? ': ' + reason : '');
                var btnContainer = btn.parentNode;
                badgeContainer.insertBefore(remarkEl, btnContainer);
            }
        }
    })
    .catch(function() {
        allBtns.forEach(function(b) { b.disabled = false; });
    });
}

function filterDaily(status) {
    if (!dailyCurrentDate) return;
    dailyCurrentFilter = status;
    updateFilterButtons(status);
    openDailyDetail(dailyCurrentDate, status);
}

function closeDailyModal() {
    var modal = document.getElementById('dailyModal');
    if (modal) modal.classList.add('hidden');
    document.body.style.overflow = '';
}

// Close modals on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (!document.getElementById('rejectReasonModal').classList.contains('hidden')) {
            closeRejectReasonModal();
        } else {
            closeDailyModal();
        }
    }
});

// ── AJAX pagination for "Registrations Per Day" table (keep scroll position) ──
(function() {
    var tableBody = document.getElementById('daily-table-body');
    if (!tableBody) return;

    var card = tableBody.closest('.rounded-2xl');
    if (!card) return;

    card.addEventListener('click', function(e) {
        var link = e.target.closest('a[href*="page="]');
        if (!link) return;

        e.preventDefault();
        var url = link.getAttribute('href');

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.text(); })
            .then(function(html) {
                var doc = new DOMParser().parseFromString(html, 'text/html');
                var newBody = doc.getElementById('daily-table-body');
                var newCard = newBody ? newBody.closest('.rounded-2xl') : null;
                var newFooter = newCard ? newCard.querySelector('.border-t') : null;
                var currentFooter = card.querySelector('.border-t');

                if (newBody) {
                    tableBody.innerHTML = newBody.innerHTML;
                    if (newFooter && currentFooter) currentFooter.innerHTML = newFooter.innerHTML;
                    window.history.pushState({}, '', url);
                }
            })
            .catch(function() {
                window.location.href = url;
            });
    });

    // Make back/forward buttons work after AJAX navigation
    window.addEventListener('popstate', function() {
        window.location.reload();
    });
})();
</script>

</body>
</html>
