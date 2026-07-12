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
                                <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span> Total
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span> Approved
                            </span>
                        </div>
                    </div>
                    <div class="flex items-end gap-1.5 h-36" id="realtime-chart">
                        @foreach ($chartData as $bar)
                            <div class="flex-1 flex flex-col items-center gap-1 group relative chart-bar" data-date="{{ $bar['date'] }}">
                                <div class="w-full flex flex-col-reverse" style="height: 140px;">
                                    @if ($bar['approved'] > 0)
                                        <div class="w-full bg-emerald-400 rounded-t transition-all duration-500 hover:bg-emerald-500 chart-bar-approved"
                                             style="height: {{ max(2, $bar['approved'] / $maxDaily * 130) }}px"
                                             title="{{ $bar['date'] }}: {{ $bar['approved'] }} approved"></div>
                                    @endif
                                    @php $pendingBar = $bar['total'] - $bar['approved']; @endphp
                                    @if ($pendingBar > 0)
                                        <div class="w-full bg-indigo-400 rounded-t transition-all duration-500 hover:bg-indigo-500 chart-bar-pending"
                                             style="height: {{ max(2, $pendingBar / $maxDaily * 130) }}px"
                                             title="{{ $bar['date'] }}: {{ $pendingBar }} pending"></div>
                                    @endif
                                    @if ($bar['total'] === 0)
                                        <div class="w-full bg-gray-100 rounded-t chart-bar-empty" style="height: 2px"></div>
                                    @endif
                                </div>
                                <span class="text-[10px] text-gray-400 font-medium">{{ $bar['day'] }}</span>
                            </div>
                        @endforeach
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

            {{-- ===== ROW 3: Recent Registrants + Workshop Registrations ===== --}}
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
            data.chartData.forEach(function(item, i) {
                if (bars[i]) {
                    var approvedBar = bars[i].querySelector('.chart-bar-approved');
                    var pendingBar = bars[i].querySelector('.chart-bar-pending');
                    var emptyBar = bars[i].querySelector('.chart-bar-empty');
                    var maxH = data.maxDaily || 1;

                    if (item.approved > 0) {
                        if (!approvedBar) {
                            approvedBar = document.createElement('div');
                            approvedBar.className = 'w-full bg-emerald-400 rounded-t transition-all duration-500';
                            bars[i].querySelector('.w-full').appendChild(approvedBar);
                        }
                        approvedBar.style.height = Math.max(2, item.approved / maxH * 130) + 'px';
                        approvedBar.title = item.date + ': ' + item.approved + ' approved';
                    } else if (approvedBar) { approvedBar.style.height = '0px'; }

                    var pendingCount = item.total - item.approved;
                    if (pendingCount > 0) {
                        if (!pendingBar) {
                            pendingBar = document.createElement('div');
                            pendingBar.className = 'w-full bg-indigo-400 rounded-t transition-all duration-500';
                            bars[i].querySelector('.w-full').prepend(pendingBar);
                        }
                        pendingBar.style.height = Math.max(2, pendingCount / maxH * 130) + 'px';
                        pendingBar.title = item.date + ': ' + pendingCount + ' pending';
                    } else if (pendingBar) { pendingBar.style.height = '0px'; }

                    if (item.total === 0 && !emptyBar) {
                        emptyBar = document.createElement('div');
                        emptyBar.className = 'w-full bg-gray-100 rounded-t chart-bar-empty';
                        emptyBar.style.height = '2px';
                        bars[i].querySelector('.w-full').appendChild(emptyBar);
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
    }

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    function highlight(el) {
        el.classList.remove('realtime-updated', 'realtime-updated-red');
        // Force reflow
        void el.offsetWidth;
        el.classList.add('realtime-updated');
    }

    // ── Poll setiap N detik ──
    setInterval(function() {
        fetch(pollUrl)
            .then(function(r) { return r.json(); })
            .then(updateDashboard)
            .catch(function(err) { /* silently ignore polling errors */ });
    }, pollInterval);
})();
</script>

</body>
</html>
