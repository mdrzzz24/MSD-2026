<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrants — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
    <style>
        .table-fixed td, .table-fixed th { min-width: 0; }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">

<div class="flex min-h-screen">
    @include('admin.partials.sidebar')

    <main class="flex-1 lg:ml-64">
        {{-- Top bar --}}
        <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200">
            <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-4">
                    <button id="sidebarToggle" class="lg:hidden p-2 -ml-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">Registrants</h1>
                        <p class="text-xs text-gray-500">Manage all registrant applications</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.registrants.export-csv', request()->only(['status', 'utm_source', 'utm_medium', 'utm_campaign', 'direct', 'search', 'profile', 'source', 'marking', 'my', 'date_from', 'date_to'])) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export CSV
                    </a>
                    <a href="{{ route('admin.dashboard') }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/>
                        </svg>
                        Dashboard
                    </a>
                </div>
            </div>
        </header>

        <div class="p-4 sm:p-6 lg:p-8 space-y-6">

            @include('admin.partials.notification')

            {{-- Status summary — registrant status counts --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex flex-wrap items-center gap-x-8 gap-y-3">
                <a href="{{ route('admin.registrants.index', array_merge(request()->except(['status', 'my', 'page']), ['status' => 'all', 'my' => 'all'])) }}" class="flex items-center gap-2 group">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</span>
                    <span class="text-2xl font-bold text-gray-900 group-hover:text-indigo-600 transition">{{ $total }}</span>
                </a>
                <a href="{{ route('admin.registrants.index', array_merge(request()->except(['status', 'my', 'page']), ['status' => 'pending', 'my' => 'all'])) }}" class="flex items-center gap-2 group">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Pending</span>
                    <span class="text-2xl font-bold text-amber-600 group-hover:text-amber-700 transition">{{ $pending }}</span>
                </a>
                <a href="{{ route('admin.registrants.index', array_merge(request()->except(['status', 'my', 'page']), ['status' => 'approved', 'my' => 'all'])) }}" class="flex items-center gap-2 group">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Approved</span>
                    <span class="text-2xl font-bold text-emerald-600 group-hover:text-emerald-700 transition">{{ $approved }}</span>
                </a>
                <a href="{{ route('admin.registrants.index', array_merge(request()->except(['status', 'my', 'page']), ['status' => 'rejected', 'my' => 'all'])) }}" class="flex items-center gap-2 group">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Rejected</span>
                    <span class="text-2xl font-bold text-red-600 group-hover:text-red-700 transition">{{ $rejected }}</span>
                </a>
            </div>

            {{-- UTM Filter Badge --}}
            @if (request('utm_source') || request('direct'))
            <div class="flex items-center gap-3 bg-indigo-50 border border-indigo-200 text-indigo-800 px-5 py-3 rounded-2xl">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                <span class="text-sm">
                    Filtered by UTM:
                    @if (request('direct'))
                        <strong>Direct</strong> (no UTM source)
                    @else
                        <strong>{{ request('utm_source') }}</strong>
                        @if (request('utm_medium')) / <strong>{{ request('utm_medium') }}</strong> @endif
                        @if (request('utm_campaign')) / <strong>{{ request('utm_campaign') }}</strong> @endif
                    @endif
                </span>
                <a href="{{ route('admin.registrants.index') }}" class="ml-auto text-xs font-medium text-indigo-600 hover:text-indigo-800 hover:underline">Clear filter</a>
            </div>
            @endif

            {{-- Filter bar: Profile / Source / Date --}}
            <form method="GET" action="{{ route('admin.registrants.index') }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                @if (request('status') && request('status') !== 'all')
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                @if (request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                @if (request('my'))
                    <input type="hidden" name="my" value="{{ request('my') }}">
                @endif
                <div class="flex flex-wrap items-end gap-3">
                    <div>
                        @include('admin.partials.profile-filter')
                    </div>
                    <div>
                        @include('admin.partials.source-filter')
                    </div>
                    <div>
                        @include('admin.partials.marking-filter')
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">From</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-3 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">To</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-3 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-xl bg-indigo-500 text-white hover:bg-indigo-600 transition">Apply</button>
                        @if (request('profile') || request('source') || request('marking') || request('my') || request('date_from') || request('date_to'))
                            <a href="{{ route('admin.registrants.index', request()->except(['profile', 'source', 'marking', 'my', 'date_from', 'date_to'])) }}" class="px-4 py-2 text-xs font-medium rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition">Clear</a>
                        @endif
                    </div>
                </div>
            </form>

            {{-- Client-only: My Markings — quick summary & filter of THIS client's own markings --}}
            @if (Auth::user()->isClient())
            <div class="bg-white rounded-2xl border border-indigo-100 shadow-sm p-5">
                <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Markings</h3>
                            <p class="text-xs text-gray-500">Client recommendations (marking) — click to filter</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 lg:ml-auto">
                        <a href="{{ route('admin.registrants.index', array_merge(request()->except(['my', 'page']), ['my' => 'all'])) }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold border transition
                           {{ $my === 'all' ? 'bg-gray-900 text-white border-gray-900' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100' }}">
                            All
                        </a>
                        <a href="{{ route('admin.registrants.index', array_merge(request()->except(['my', 'status', 'page']), ['my' => 'approve', 'status' => 'all'])) }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold border transition
                           {{ $my === 'approve' ? 'bg-emerald-500 text-white border-emerald-500' : 'bg-white text-emerald-700 border-emerald-200 hover:bg-emerald-50' }}">
                            Marked ✅ Approve
                            <span id="myCountApprove" class="px-1.5 py-0.5 rounded-md text-[10px] {{ $my === 'approve' ? 'bg-white/25' : 'bg-emerald-100' }}">{{ $myCounts['approve'] ?? 0 }}</span>
                        </a>
                        <a href="{{ route('admin.registrants.index', array_merge(request()->except(['my', 'status', 'page']), ['my' => 'reject', 'status' => 'all'])) }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold border transition
                           {{ $my === 'reject' ? 'bg-red-500 text-white border-red-500' : 'bg-white text-red-700 border-red-200 hover:bg-red-50' }}">
                            Marked ❌ Reject
                            <span id="myCountReject" class="px-1.5 py-0.5 rounded-md text-[10px] {{ $my === 'reject' ? 'bg-white/25' : 'bg-red-100' }}">{{ $myCounts['reject'] ?? 0 }}</span>
                        </a>
                        <a href="{{ route('admin.registrants.index', array_merge(request()->except(['my', 'status', 'page']), ['my' => 'waitlist', 'status' => 'all'])) }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold border transition
                           {{ $my === 'waitlist' ? 'bg-orange-500 text-white border-orange-500' : 'bg-white text-orange-700 border-orange-200 hover:bg-orange-50' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Marked 🕐 Waiting List
                            <span id="myCountWaitlist" class="px-1.5 py-0.5 rounded-md text-[10px] {{ $my === 'waitlist' ? 'bg-white/25' : 'bg-orange-100' }}">{{ $myCounts['waitlist'] ?? 0 }}</span>
                        </a>
                        <a href="{{ route('admin.registrants.index', array_merge(request()->except(['my', 'status', 'page']), ['my' => 'unmarked', 'status' => 'all'])) }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold border transition
                           {{ $my === 'unmarked' ? 'bg-gray-800 text-white border-gray-800' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-100' }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 9h.01M9 13h6"/></svg>
                            Unmarked
                            <span id="myCountUnmarked" class="px-1.5 py-0.5 rounded-md text-[10px] {{ $my === 'unmarked' ? 'bg-white/25' : 'bg-gray-200' }}">{{ $myCounts['unmarked'] ?? 0 }}</span>
                        </a>
                    </div>
                </div>
                {{-- Registrant STATUS (separate from markings) --}}
                <div class="mt-4 pt-4 border-t border-gray-100 flex flex-wrap items-center gap-2">
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mr-1">Status</span>
                    <a href="{{ route('admin.registrants.index', array_merge(request()->except(['status', 'my', 'page']), ['status' => 'all', 'my' => 'all'])) }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold border transition {{ $status === 'all' ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-100' }}">
                        All
                    </a>
                    <a href="{{ route('admin.registrants.index', array_merge(request()->except(['status', 'my', 'page']), ['status' => 'pending', 'my' => 'all'])) }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold border transition {{ $status === 'pending' ? 'bg-amber-500 text-white border-amber-500' : 'bg-white text-amber-700 border-amber-200 hover:bg-amber-50' }}">
                        Pending
                    </a>
                    <a href="{{ route('admin.registrants.index', array_merge(request()->except(['status', 'my', 'page']), ['status' => 'approved', 'my' => 'all'])) }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold border transition {{ $status === 'approved' ? 'bg-emerald-500 text-white border-emerald-500' : 'bg-white text-emerald-700 border-emerald-200 hover:bg-emerald-50' }}">
                        Approved
                    </a>
                    <a href="{{ route('admin.registrants.index', array_merge(request()->except(['status', 'my', 'page']), ['status' => 'rejected', 'my' => 'all'])) }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold border transition {{ $status === 'rejected' ? 'bg-red-500 text-white border-red-500' : 'bg-white text-red-700 border-red-200 hover:bg-red-50' }}">
                        Rejected
                    </a>
                </div>
            </div>
            @endif

            {{-- Registrants table with bulk actions --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                {{-- Header with search and bulk actions --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <h2 class="text-base font-bold text-gray-900">
                            @if ($status === 'pending')
                                Pending Registrants
                            @elseif ($status === 'approved')
                                Approved Registrants
                            @elseif ($status === 'rejected')
                                Rejected Registrants
                            @else
                                All Registrants
                            @endif
                        </h2>
                        <span class="text-xs text-gray-400" id="registrantCount">({{ $registrants->total() }})</span>
                    </div>
                    <div class="flex items-center gap-2">
                        {{-- Bulk action buttons --}}
                        @if (Auth::user()->canWrite())
                        <div id="bulkActions" class="hidden items-center gap-2">
                            <span class="text-xs text-gray-500" id="selectedCount">0 selected</span>
                            <button onclick="bulkApprove()"
                                    class="px-3 py-1.5 text-xs font-medium rounded-lg bg-emerald-500 text-white hover:bg-emerald-600 transition">
                                Approve Selected
                            </button>
                            <button onclick="openBulkRejectModal()"
                                    class="px-3 py-1.5 text-xs font-medium rounded-lg bg-red-500 text-white hover:bg-red-600 transition">
                                Reject Selected
                            </button>
                        </div>
                        @endif
                        <form method="GET" action="{{ route('admin.registrants.index') }}" class="relative" onsubmit="return false;">
                            @if (request('status') && request('status') !== 'all')
                                <input type="hidden" name="status" value="{{ request('status') }}">
                            @endif
                            @if (request('utm_source'))
                                <input type="hidden" name="utm_source" value="{{ request('utm_source') }}">
                            @endif
                            @if (request('utm_medium'))
                                <input type="hidden" name="utm_medium" value="{{ request('utm_medium') }}">
                            @endif
                            @if (request('utm_campaign'))
                                <input type="hidden" name="utm_campaign" value="{{ request('utm_campaign') }}">
                            @endif
                            @if (request('direct'))
                                <input type="hidden" name="direct" value="{{ request('direct') }}">
                            @endif
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" name="search" id="tableSearch" placeholder="Search name, email, company..."
                                   value="{{ request('search') }}"
                                   class="pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white w-full sm:w-64 transition">
                            <a href="javascript:void(0)" id="clearRegSearchBtn" onclick="clearRegSearch()" style="display:none;"
                               class="absolute right-2 top-1/2 -translate-y-1/2 p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-200 transition" title="Clear search">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </a>
                        </form>
                    </div>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full table-fixed" id="registrantTable">
                        <thead id="registrantThead">
                            @php
                                $sort = request('sort');
                                $sortDir = (strtolower((string) request('direction', 'asc')) === 'desc') ? 'desc' : 'asc';
                                $sortUrl = function ($key) use ($sort, $sortDir) {
                                    return route('admin.registrants.index', array_merge(request()->except(['sort', 'direction', 'page']), [
                                        'sort' => $key,
                                        'direction' => ($sort === $key && $sortDir === 'asc') ? 'desc' : 'asc',
                                    ]));
                                };
                                $sortArrow = function ($key) use ($sort, $sortDir) {
                                    if ($sort !== $key) return '';
                                    return '<span class="text-[9px]">' . ($sortDir === 'asc' ? '▲' : '▼') . '</span>';
                                };
                            @endphp
                            <tr class="bg-gray-50/80">
                                @if (Auth::user()->canWrite())
                                <th class="px-5 py-3.5 text-left w-10">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </th>
                                @endif
                                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-44">
                                    <a href="{{ $sortUrl('name') }}" class="inline-flex items-center gap-1 hover:text-indigo-600 {{ $sort === 'name' ? 'text-indigo-600' : '' }}">Name{!! $sortArrow('name') !!}</a>
                                </th>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden xl:table-cell w-40">
                                    <a href="{{ $sortUrl('company') }}" class="inline-flex items-center gap-1 hover:text-indigo-600 {{ $sort === 'company' ? 'text-indigo-600' : '' }}">Profile{!! $sortArrow('company') !!}</a>
                                </th>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell w-20">
                                    <a href="{{ $sortUrl('source') }}" class="inline-flex items-center gap-1 hover:text-indigo-600 {{ $sort === 'source' ? 'text-indigo-600' : '' }}">Source{!! $sortArrow('source') !!}</a>
                                </th>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-20">
                                    <a href="{{ $sortUrl('status') }}" class="inline-flex items-center gap-1 hover:text-indigo-600 {{ $sort === 'status' ? 'text-indigo-600' : '' }}">Status{!! $sortArrow('status') !!}</a>
                                </th>
                                <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell w-20">
                                    <a href="{{ $sortUrl('date') }}" class="inline-flex items-center gap-1 hover:text-indigo-600 {{ $sort === 'date' ? 'text-indigo-600' : '' }}">Date{!! $sortArrow('date') !!}</a>
                                </th>
                                <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell w-14">
                                    <a href="{{ $sortUrl('emails') }}" class="inline-flex items-center gap-1 hover:text-indigo-600 {{ $sort === 'emails' ? 'text-indigo-600' : '' }}" title="Sort by emails sent">Email{!! $sortArrow('emails') !!}</a>
                                </th>
                                <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-20">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50" id="registrantTableBody">
                            @forelse ($registrants as $r)
                                <tr class="hover:bg-gray-50/50 transition search-row">
                                    @if (Auth::user()->canWrite())
                                    <td class="px-3 py-3">
                                        <input type="checkbox" class="registrant-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" value="{{ $r->id }}">
                                    </td>
                                    @endif
                                    <td class="px-3 py-4 max-w-0">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-[10px] font-bold flex-shrink-0">
                                                {{ strtoupper(substr($r->name, 0, 1)) }}
                                            </div>
                                            <div class="min-w-0 truncate">
                                                <a href="{{ route('admin.registrants.show', $r) }}" class="text-sm font-semibold text-gray-900 hover:text-indigo-600 transition search-name truncate block">
                                                    {{ $r->name }}
                                                </a>
                                                <p class="text-[11px] text-gray-500 truncate search-email">{{ $r->email }}</p>
                                                @if ($r->phone)
                                                    <p class="text-[11px] text-gray-400 truncate">{{ $r->phone }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 hidden xl:table-cell max-w-0">
                                        <div class="min-w-0 truncate">
                                            @if ($r->company || $r->job_title || $r->job_role)
                                                @if ($r->company)
                                                    <p class="text-sm font-medium text-gray-800 truncate" title="{{ $r->company }}">{{ $r->company }}</p>
                                                @endif
                                                @if ($r->job_title)
                                                    <p class="text-[11px] text-gray-500 truncate" title="{{ $r->job_title }}">{{ $r->job_title }}</p>
                                                @endif
                                                @if ($r->job_role)
                                                    <p class="text-[11px] text-gray-400 truncate" title="{{ $r->job_role }}">{{ $r->job_role }}</p>
                                                @endif
                                            @else
                                                <span class="text-sm text-gray-400">—</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 hidden sm:table-cell max-w-0">
                                        @if ($r->utm_source)
                                            <span class="inline-flex items-center gap-1 text-xs text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full truncate max-w-full" title="{{ $r->utm_source }}">
                                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                                <span class="truncate">{{ $r->utm_source }}</span>
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400">Direct</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3">
                                        @if ($r->status === 'approved')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 flex-shrink-0"></span> <span class="truncate">Approved</span>
                                            </span>
                                        @elseif ($r->status === 'rejected')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 flex-shrink-0"></span> <span class="truncate">Rejected</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse flex-shrink-0"></span> <span class="truncate">Pending</span>
                                            </span>
                                        @endif
                                        @if ($r->hasClientRemark())
                                            <div class="mt-1.5 flex flex-col items-start gap-0.5">
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-semibold border {{ $r->client_remark_action === 'approve' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($r->client_remark_action === 'reject' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-orange-50 text-orange-700 border-orange-200') }}">
                                                    @if ($r->client_remark_action === 'approve')
                                                        ✅ Marked Approve
                                                    @elseif ($r->client_remark_action === 'reject')
                                                        ❌ Marked Reject
                                                    @else
                                                        <span class="inline-flex items-center gap-1">
                                                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                            Marked Waiting List
                                                        </span>
                                                    @endif
                                                    @if ($r->clientRemarkedBy)
                                                        <span class="font-normal text-gray-500">· {{ $r->clientRemarkedBy->name }}</span>
                                                    @endif
                                                </span>
                                                @if ($r->client_remark)
                                                    <span class="text-[10px] text-gray-500">{{ $r->client_remark }}</span>
                                                @endif
                                                @if ($r->client_remarked_at)
                                                    <span class="text-[10px] text-gray-400">{{ $r->client_remarked_at->copy()->addHours(7)->format('d M Y, H:i') }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3 hidden sm:table-cell">
                                        <span class="text-sm text-gray-500 whitespace-nowrap">{{ $r->created_at->copy()->addHours(7)->format('d M Y') }}</span>
                                    </td>
                                    <td class="px-3 py-3 text-center hidden sm:table-cell">
                                        @if ($r->email_logs_count > 0)
                                            <span class="inline-flex items-center gap-0.5 text-xs font-semibold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded-full" title="{{ $r->email_logs_count }} email(s) sent">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                {{ $r->email_logs_count }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-0.5 text-xs text-gray-400" title="No emails sent">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                <span>0</span>
                                            </span>
                                        @endif
                                    </td>                                    <td class="px-3 py-3">
                                        <div class="flex items-center justify-center gap-1">
                                            <a href="{{ route('admin.registrants.show', $r) }}"
                                               title="View"
                                               class="p-1 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            @if (!Auth::user()->canWrite() && $r->isPending())
                                            @if (!$r->hasClientRemark())
                                            <div class="flex flex-col items-center gap-1">
                                                <div class="flex items-center gap-1">
                                                    <button type="button" data-decision data-id="{{ $r->id }}" data-action="approve"
                                                            class="decision-toggle px-2 py-1 rounded-lg text-xs font-semibold transition whitespace-nowrap border border-gray-200 bg-gray-50 text-gray-500 hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-200" title="Mark as Approved">
                                                        ✅
                                                    </button>
                                                    <button type="button" data-decision data-id="{{ $r->id }}" data-action="reject"
                                                            class="decision-toggle px-2 py-1 rounded-lg text-xs font-semibold transition whitespace-nowrap border border-gray-200 bg-gray-50 text-gray-500 hover:bg-red-50 hover:text-red-600 hover:border-red-200" title="Mark as Rejected">
                                                        ❌
                                                    </button>
                                                    <button type="button" data-decision data-id="{{ $r->id }}" data-action="waitlist"
                                                            class="decision-toggle px-2 py-1 rounded-lg text-xs font-semibold transition whitespace-nowrap border border-gray-200 bg-gray-50 text-gray-500 hover:bg-orange-50 hover:text-orange-600 hover:border-orange-200" title="Mark as Waiting List">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    </button>
                                                </div>
                                                <select data-reason data-id="{{ $r->id }}" class="decision-reason hidden mt-1 w-36 text-[10px] border border-gray-300 rounded-lg px-2 py-1 bg-white">
                                                    <option value="">— Reason —</option>
                                                    @foreach (config('client_reasons.reject') as $reason)
                                                        <option value="{{ $reason }}">{{ $reason }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @else
                                            <span class="text-[10px] text-gray-400">Already marked</span>
                                            @endif
                                            @endif
                                            @if (Auth::user()->canWrite())
                                            <a href="{{ route('admin.registrants.edit', $r) }}"
                                               title="Edit"
                                               class="p-1 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            {{-- Approve - always available --}}
                                            <form action="{{ route('admin.registrants.approve', $r) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit"
                                                        onclick="return confirm('Approve {{ addslashes($r->name) }}?')"
                                                        title="Approve"
                                                        class="p-1 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </button>
                                            </form>
                                            {{-- Reject - always available --}}
                                            <button onclick="openRejectModal('{{ $r->id }}', '{{ addslashes($r->name) }}')"
                                                    title="Reject"
                                                    class="p-1 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                            {{-- Resend credentials - only for approved --}}
                                            @if ($r->status === 'approved')
                                                <button onclick="resendCredentials('{{ $r->id }}', '{{ addslashes($r->name) }}')"
                                                        title="Resend"
                                                        class="p-1 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                    </svg>
                                                </button>
                                            @endif
                                            <form action="{{ route('admin.registrants.destroy', $r) }}" method="POST" class="inline" onsubmit="return confirm('Delete {{ addslashes($r->name) }} permanently?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        title="Delete"
                                                        class="p-1 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-5 py-16 text-center">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                            </svg>
                                            <p class="text-gray-400 font-medium">No registrants found</p>
                                            <p class="text-xs text-gray-400">No registrants match the current filter</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div id="registrantPagination" class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
                    @if ($registrants->hasPages())
                        {{ $registrants->links() }}
                    @endif
                </div>
            </div>
        </div>
    </main>
</div>

{{-- ==================== REJECT MODAL (Single) ==================== --}}
<div id="rejectModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in">
        <div class="bg-red-50 px-6 py-4 border-b border-red-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Rejection Confirmation</h3>
                    <p class="text-xs text-gray-500">A rejection email will be sent automatically</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <p class="text-sm text-gray-600 mb-4">
                You are about to reject <strong id="rejectName" class="text-red-600"></strong>'s registration.
                A rejection email will be sent automatically.
            </p>
            {{-- Client remark info (shown if available) --}}
            <div id="rejectClientRemark" class="hidden mb-4 p-3 bg-indigo-50 border border-indigo-200 rounded-xl">
                <p class="text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-1">Client Recommendation</p>
                <p class="text-sm text-gray-800" id="rejectClientRemarkText"></p>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="flex justify-end gap-2.5">
                    <button type="button" onclick="closeRejectModal()"
                            class="px-5 py-2.5 text-sm font-medium rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition">Cancel</button>
                    <button type="submit"
                            class="px-5 py-2.5 text-sm font-semibold rounded-xl bg-red-500 text-white hover:bg-red-600 shadow-sm shadow-red-200 transition">Yes, Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ==================== REJECT REASON MODAL (client recommendation) ==================== --}}
<div id="rejectReasonModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeRejectReasonModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in">
        <div class="bg-red-50 px-6 py-4 border-b border-red-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Reject Reason</h3>
                    <p class="text-xs text-gray-500">Select a reason for <span id="rrName" class="font-semibold text-gray-700"></span></p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Reason</label>
            <select id="rrSelect" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:ring-2 focus:ring-red-100 focus:border-red-300 outline-none bg-white">
                <option value="">— Select a reason —</option>
                @foreach (config('client_reasons.reject') as $reason)
                    <option value="{{ $reason }}">{{ $reason }}</option>
                @endforeach
            </select>
            <div class="flex justify-end gap-2.5 mt-5">
                <button type="button" onclick="closeRejectReasonModal()"
                        class="px-5 py-2.5 text-sm font-medium rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition">Cancel</button>
                <button type="button" onclick="confirmRejectReason()"
                        class="px-5 py-2.5 text-sm font-semibold rounded-xl bg-red-500 text-white hover:bg-red-600 shadow-sm shadow-red-200 transition">Reject</button>
            </div>
        </div>
    </div>
    </div>
</div>

{{-- ==================== BULK REJECT MODAL ==================== --}}
<div id="bulkRejectModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in">
        <div class="bg-red-50 px-6 py-4 border-b border-red-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Bulk Rejection</h3>
                    <p class="text-xs text-gray-500">Reject <span id="bulkRejectCount" class="font-bold">0</span> selected registrants</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <p class="text-sm text-gray-600 mb-4">
                Reject <strong id="bulkRejectCount" class="font-bold text-red-600">0</strong> selected registrants?
                A rejection email will be sent automatically to each.
            </p>
            <form id="bulkRejectForm" method="POST" action="{{ route('admin.registrants.bulk-reject') }}">
                @csrf
                <div id="bulkRejectIds"></div>
                <div class="flex justify-end gap-2.5">
                    <button type="button" onclick="closeBulkRejectModal()"
                            class="px-5 py-2.5 text-sm font-medium rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition">Cancel</button>
                    <button type="submit"
                            class="px-5 py-2.5 text-sm font-semibold rounded-xl bg-red-500 text-white hover:bg-red-600 shadow-sm shadow-red-200 transition">Yes, Reject All</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ==================== RECOMMEND MODAL ==================== --}}
@include('admin.partials.mobile-sidebar')

{{-- Client decision submit bar — select approve/reject/waitlist, submit at the end --}}
@if (Auth::user()->isClient())
<div id="decisionBar" class="hidden fixed bottom-4 left-1/2 -translate-x-1/2 z-40 bg-gray-900 text-white rounded-2xl shadow-2xl px-5 py-3 items-center gap-4">
    <div class="text-xs flex items-center gap-4">
        <span class="inline-flex items-center gap-1.5">✅ Approved: <b id="dApprove">0</b></span>
        <span class="inline-flex items-center gap-1.5">❌ Rejected: <b id="dReject">0</b></span>
        <span class="inline-flex items-center gap-1.5"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Waiting List: <b id="dWaitlist">0</b></span>
    </div>
    <button onclick="openDecisionPreview()" class="px-4 py-2 text-xs font-bold rounded-xl bg-indigo-500 hover:bg-indigo-600 transition">📋 Preview</button>
    <button onclick="submitDecisions()" class="px-4 py-2 text-xs font-bold rounded-xl bg-emerald-500 hover:bg-emerald-600 transition">Submit Decisions</button>
</div>

{{-- Preview & cancel modal for the client's selected decisions --}}
<div id="decisionPreviewModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/30">
    <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full max-h-[80vh] flex flex-col overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-800">My Selections</h3>
            <button type="button" onclick="closeDecisionPreview()" class="text-gray-400 hover:text-gray-600 text-lg leading-none">✕</button>
        </div>
        <div id="decisionPreviewList" class="overflow-y-auto px-5 py-3 divide-y divide-gray-50"></div>
        <div class="px-5 py-4 border-t border-gray-100 flex justify-end gap-2">
            <button type="button" onclick="closeDecisionPreview()" class="px-4 py-2 text-xs font-semibold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Close</button>
        </div>
    </div>
</div>

{{-- Live presence: which OTHER clients are collaborating right now (across devices) --}}
<div id="collabPresence" class="hidden fixed bottom-4 left-4 z-40 items-center gap-2.5 bg-white/95 backdrop-blur border border-emerald-200 rounded-2xl shadow-lg px-4 py-2.5 text-xs">
    <span class="inline-flex items-center gap-1.5 font-bold text-emerald-700">
        <span class="relative flex h-2 w-2">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
        </span>
        Collaborating
    </span>
    <span id="collabPresenceList" class="text-gray-700 font-medium"></span>
</div>
@endif

<script>
    const rejectRoute = '{{ route("admin.registrants.reject", ["registrant" => "REG_ID"]) }}';
    const resendRoute = '{{ route("admin.registrants.resend-credentials", ["registrant" => "REG_ID"]) }}';
    // ---- Reject Modal (Single) ----
    function openRejectModal(id, name) {
        document.getElementById('rejectName').textContent = name;
        document.getElementById('rejectForm').action = rejectRoute.replace('REG_ID', id);
        const modal = document.getElementById('rejectModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Fetch client remark if available
        var remarkContainer = document.getElementById('rejectClientRemark');
        var remarkText = document.getElementById('rejectClientRemarkText');
        remarkContainer.classList.add('hidden');

        fetch('{{ url('admin/dashboard/daily') }}/fetch?registrant_id=' + id)
            .then(function(r) { return r.json(); })
            .then(function(data) { /* not needed */ })
            .catch(function() { /* silently ignore */ });

        // Use a simpler approach: check if the registrant row has a client remark badge
        var checkbox = document.querySelector('.registrant-checkbox[value="' + id + '"]');
        if (checkbox) {
            var row = checkbox.closest('tr');
            var badge = row && row.querySelector('[class*="Client"]');
            if (badge) {
                var title = badge.getAttribute('title') || '';
                remarkText.textContent = title;
                remarkContainer.classList.remove('hidden');
            }
        }
    }
    function closeRejectModal() {
        const modal = document.getElementById('rejectModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // ---- Bulk Reject Modal ----
    function openBulkRejectModal() {
        const checked = document.querySelectorAll('.registrant-checkbox:checked');
        if (checked.length === 0) {
            alert('Please select at least one registrant.');
            return;
        }
        document.getElementById('bulkRejectCount').textContent = checked.length;
        const container = document.getElementById('bulkRejectIds');
        container.innerHTML = '';
        checked.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = cb.value;
            container.appendChild(input);
        });
        const modal = document.getElementById('bulkRejectModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    function closeBulkRejectModal() {
        const modal = document.getElementById('bulkRejectModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.getElementById('bulk_admin_notes').value = '';
    }

    // ---- Bulk Approve ----
    function bulkApprove() {
        const checked = document.querySelectorAll('.registrant-checkbox:checked');
        if (checked.length === 0) {
            alert('Please select at least one registrant.');
            return;
        }
        if (!confirm('Approve ' + checked.length + ' selected registrant(s)?')) return;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.registrants.bulk-approve") }}';
        form.style.display = 'none';

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);

        checked.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = cb.value;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    }

    // ---- Resend Credentials ----
    function resendCredentials(id, name) {
        if (!confirm('Resend credentials to ' + name + '?')) return;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = resendRoute.replace('REG_ID', id);
        form.style.display = 'none';

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);

        document.body.appendChild(form);
        form.submit();
    }

    // ---- Select All ----
    document.getElementById('selectAll')?.addEventListener('change', function() {
        document.querySelectorAll('.registrant-checkbox').forEach(cb => cb.checked = this.checked);
        updateBulkActions();
    });
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('registrant-checkbox')) {
            updateBulkActions();
        }
    });
    function updateBulkActions() {
        const checked = document.querySelectorAll('.registrant-checkbox:checked').length;
        const el = document.getElementById('bulkActions');
        const count = document.getElementById('selectedCount');
        if (checked > 0) {
            el.classList.remove('hidden');
            el.classList.add('flex');
            count.textContent = checked + ' selected';
        } else {
            el.classList.add('hidden');
            el.classList.remove('flex');
        }
    }

    // ---- Client decision select & submit (approve / reject+reason / waitlist) ----
    function decisionToggle(btn) {
        const id = btn.dataset.id;
        const active = btn.classList.contains('decision-active');
        // Clear the row's own-selection tint (approve=green, reject=red, waitlist=yellow)
        const row = btn.closest('tr');
        if (row) row.classList.remove('decision-row', 'bg-emerald-50', 'bg-red-50', 'bg-yellow-50');
        document.querySelectorAll('.decision-toggle[data-id="' + id + '"]').forEach(b => {
            b.classList.remove('decision-active','rt-pending-active','bg-emerald-50','text-emerald-600','border-emerald-300','bg-red-50','text-red-600','border-red-300','bg-orange-50','text-orange-600','border-orange-300');
        });
        const reasonSel = document.querySelector('.decision-reason[data-id="' + id + '"]');
        if (reasonSel) { reasonSel.classList.add('hidden'); reasonSel.value = ''; }
        if (!active) {
            btn.classList.add('decision-active');
            const a = btn.dataset.action;
            if (a === 'approve') { btn.classList.add('bg-emerald-50','text-emerald-600','border-emerald-300'); if (row) row.classList.add('decision-row', 'bg-emerald-50'); }
            else if (a === 'reject') { btn.classList.add('bg-red-50','text-red-600','border-red-300'); if (reasonSel) reasonSel.classList.remove('hidden'); if (row) row.classList.add('decision-row', 'bg-red-50'); }
            else { btn.classList.add('bg-orange-50','text-orange-600','border-orange-300'); if (row) row.classList.add('decision-row', 'bg-yellow-50'); }
        }
        updateDecisionBar();
        syncPending();
    }
    function collectDecisions() {
        const decisions = [];
        let invalid = false;
        document.querySelectorAll('.decision-toggle.decision-active').forEach(btn => {
            const id = btn.dataset.id;
            const action = btn.dataset.action;
            let reason = null;
            if (action === 'reject') {
                const sel = document.querySelector('.decision-reason[data-id="' + id + '"]');
                reason = sel ? sel.value : '';
                if (!reason) { invalid = true; }
            }
            decisions.push({ id: id, action: action, reason: reason });
        });
        return { decisions: decisions, invalid: invalid };
    }
    // Collect ALL of this client's decisions: current-page DOM selections PLUS any
    // selections made on OTHER pagination pages (kept in myPendingCache via realtime).
    // Rows present on this page but NOT active were canceled, so they are excluded.
    function collectAllMyDecisions() {
        const decisions = [];
        const seen = new Set();
        let invalid = false;
        // 1) Current-page DOM selections are authoritative for rows on this page.
        document.querySelectorAll('.decision-toggle.decision-active').forEach(btn => {
            const id = btn.dataset.id;
            if (seen.has(id)) return;
            seen.add(id);
            const action = btn.dataset.action;
            let reason = null;
            if (action === 'reject') {
                const sel = document.querySelector('.decision-reason[data-id="' + id + '"]');
                reason = sel ? sel.value : '';
                if (!reason) { invalid = true; }
            }
            decisions.push({ id: id, action: action, reason: reason });
        });
        // 2) Own selections made on other pages (restored from cache via realtime).
        const inDomIds = new Set();
        document.querySelectorAll('.decision-toggle').forEach(b => inDomIds.add(String(b.dataset.id)));
        (myPendingCache || []).forEach(p => {
            if (!p.id || !p.action || seen.has(String(p.id))) return;
            if (inDomIds.has(String(p.id))) return; // this page's row decides (inactive = canceled)
            seen.add(String(p.id));
            const action = p.action;
            let reason = null;
            if (action === 'reject') {
                reason = p.reason || '';
                if (!reason) { invalid = true; }
            }
            decisions.push({ id: String(p.id), action: action, reason: reason });
        });
        return { decisions: decisions, invalid: invalid };
    }
    function updateDecisionBar() {
        const bar = document.getElementById('decisionBar');
        if (!bar) return;
        const ds = collectDecisions();
        let ap = 0, rj = 0, wl = 0;
        ds.decisions.forEach(d => { if (d.action === 'approve') ap++; else if (d.action === 'reject') rj++; else wl++; });
        // Own selections on OTHER pagination pages (in cache, not in the DOM) — so the
        // bar total matches the preview and what other viewers see.
        const inDomIds = new Set();
        document.querySelectorAll('.decision-toggle').forEach(b => inDomIds.add(String(b.dataset.id)));
        (myPendingCache || []).forEach(p => {
            if (!p.id || !p.action || inDomIds.has(String(p.id))) return;
            if (p.action === 'approve') ap++;
            else if (p.action === 'reject') rj++;
            else wl++;
        });
        // Include other clients' pending (pre-submit) selections so the counts shown
        // are identical for the editor and every viewer in realtime
        (realtimePending || []).forEach(p => {
            if (!p.registrant_id || !p.action) return;
            if (p.action === 'approve') ap++;
            else if (p.action === 'reject') rj++;
            else wl++;
        });
        document.getElementById('dApprove').textContent = ap;
        document.getElementById('dReject').textContent = rj;
        document.getElementById('dWaitlist').textContent = wl;
        if (ap + rj + wl > 0) { bar.classList.remove('hidden'); bar.classList.add('flex'); }
        else { bar.classList.add('hidden'); bar.classList.remove('flex'); }
    }
    function submitDecisions() {
        const ds = collectAllMyDecisions();
        if (ds.decisions.length === 0) { alert('Please select at least one decision.'); return; }
        if (ds.invalid) { alert('Please select a reason for every rejected registrant.'); return; }
        if (!confirm('Submit ' + ds.decisions.length + ' decision(s) for admin review?')) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.registrants.submit-decisions") }}';
        form.style.display = 'none';
        const csrf = document.createElement('input'); csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}'; form.appendChild(csrf);
        const data = document.createElement('input'); data.type = 'hidden'; data.name = 'decisions'; data.value = JSON.stringify(ds.decisions); form.appendChild(data);
        document.body.appendChild(form);
        form.submit();
    }
    function escapeHtml(s) {
        return String(s == null ? '' : s).replace(/[&<>"']/g, function(c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }
    function buildPreviewList() {
        const items = [];
        const seen = new Set();
        // Which registrant rows are present on this page (active or not) — the DOM
        // state is authoritative for these, so a canceled row here is NOT re-added.
        const inDomIds = new Set();
        document.querySelectorAll('.decision-toggle').forEach(b => inDomIds.add(String(b.dataset.id)));
        // Visible selections (rows on this page)
        document.querySelectorAll('.decision-toggle.decision-active').forEach(btn => {
            const id = btn.dataset.id;
            if (seen.has(id)) return;
            seen.add(id);
            const row = btn.closest('tr');
            const nameEl = row ? row.querySelector('a.search-name') : null;
            items.push({
                id: id,
                name: nameEl ? nameEl.textContent.trim() : 'Registrant #' + id,
                action: btn.dataset.action,
                reason: btn.dataset.action === 'reject' ? (row ? (row.querySelector('.decision-reason[data-id="' + id + '"]')?.value || '') : '') : '',
                inDom: true,
                mine: true,
            });
        });
        // Off-page selections from cache (rows NOT present on this page at all)
        (myPendingCache || []).forEach(p => {
            if (!p.id || !p.action || seen.has(String(p.id))) return;
            if (inDomIds.has(String(p.id))) return; // this page's row decides (inactive = canceled)
            seen.add(String(p.id));
            const link = document.querySelector('#registrantTableBody tr a[href*="/registrants/' + p.id + '"]');
            items.push({
                id: String(p.id),
                name: p.name || (link ? link.textContent.trim() : 'Registrant #' + p.id),
                action: p.action,
                reason: p.reason || '',
                inDom: false,
                mine: true,
            });
        });
        // Other clients' pending selections (view-only, from the realtime poll)
        (realtimePending || []).forEach(p => {
            if (!p.registrant_id || !p.action) return;
            items.push({
                id: String(p.registrant_id),
                name: p.name || 'Registrant #' + p.registrant_id,
                action: p.action,
                reason: p.reason || '',
                inDom: false,
                mine: false,
                by: p.client_name || 'Colleague',
            });
        });
        return items;
    }
    function openDecisionPreview() {
        const list = document.getElementById('decisionPreviewList');
        const modal = document.getElementById('decisionPreviewModal');
        if (!list || !modal) return;
        const items = buildPreviewList();
        if (!items.length) {
            list.innerHTML = '<p class="py-6 text-center text-sm text-gray-400">No selections yet.</p>';
        } else {
            list.innerHTML = items.map(function(it) {
                const label = it.action === 'approve' ? '✅ Approved' : it.action === 'reject' ? '❌ Rejected' : '🕐 Waiting List';
                const color = it.action === 'approve' ? 'text-emerald-600' : it.action === 'reject' ? 'text-red-600' : 'text-orange-600';
                const reason = it.action === 'reject' && it.reason ? '<span class="block text-[10px] text-gray-500 mt-0.5">' + escapeHtml(it.reason) + '</span>' : '';
                const right = it.mine
                    ? '<button type="button" onclick="cancelDecision(' + it.id + ')" class="flex-shrink-0 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 px-2.5 py-1 rounded-lg">Cancel</button>'
                    : '<span class="flex-shrink-0 text-[10px] font-semibold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-lg">' + escapeHtml(it.by) + '</span>';
                return '<div class="flex items-center justify-between gap-3 py-2.5">' +
                    '<div class="min-w-0">' +
                        '<p class="text-sm font-medium text-gray-800 truncate">' + escapeHtml(it.name) + '</p>' +
                        '<p class="text-[11px] font-semibold ' + color + '">' + label + '</p>' + reason +
                    '</div>' +
                    right +
                '</div>';
            }).join('');
        }
        modal.classList.remove('hidden');
    }
    function closeDecisionPreview() {
        const modal = document.getElementById('decisionPreviewModal');
        if (modal) modal.classList.add('hidden');
    }
    function cancelDecision(id) {
        const btn = document.querySelector('.decision-toggle[data-id="' + id + '"].decision-active');
        if (btn) {
            decisionToggle(btn); // active → toggles it OFF (removes the selection)
        } else {
            // Off-page selection: drop it from the cache mirror and re-sync
            myPendingCache = (myPendingCache || []).filter(p => String(p.id) !== String(id));
            syncPending();
            updateDecisionBar();
        }
        openDecisionPreview(); // refresh the list
    }
    document.addEventListener('click', function(e) {
        const el = e.target.closest ? e.target.closest('[data-decision]') : null;
        if (el) decisionToggle(el);
    });
    document.addEventListener('change', function(e) {
        if (e.target && e.target.classList && e.target.classList.contains('decision-reason')) {
            updateDecisionBar();
            syncPending();
        }
    });

    // ---- Table Search (AJAX live search, no reload) ----
    let regSearchTimer = null;
    let regSearchSeq = 0;
    function regLiveSearch(input) {
        clearTimeout(regSearchTimer);
        regSearchTimer = setTimeout(async () => {
            const seq = ++regSearchSeq;
            const params = new URLSearchParams(window.location.search);
            params.set('search', input.value);
            params.set('page', '1');
            try {
                const res = await fetch('{{ route("admin.registrants.search") }}?' + params.toString(), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                if (seq !== regSearchSeq) return;
                const tbody = document.getElementById('registrantTableBody');
                if (tbody && data.rows) tbody.innerHTML = data.rows;
                const pag = document.getElementById('registrantPagination');
                if (pag) pag.innerHTML = data.pagination || '';
                const count = document.getElementById('registrantCount');
                if (count) count.textContent = '(' + (data.total || 0) + ')';
                // Keep row hover states & any row-level inline handlers are global, so fine
            } catch (e) { /* ignore */ }
            input.focus();
            input.setSelectionRange(input.value.length, input.value.length);
        }, 300);
    }
    function clearRegSearch() {
        const input = document.getElementById('tableSearch');
        const btn = document.getElementById('clearRegSearchBtn');
        if (input) { input.value = ''; if (btn) btn.style.display = 'none'; regLiveSearch(input); }
    }
    document.getElementById('tableSearch')?.addEventListener('input', function() {
        const btn = document.getElementById('clearRegSearchBtn');
        if (btn) btn.style.display = this.value ? 'block' : 'none';
        regLiveSearch(this);
    });
    // Show the clear button if the URL already carries a search value
    (function() {
        const input = document.getElementById('tableSearch');
        const btn = document.getElementById('clearRegSearchBtn');
        if (input && btn && input.value) btn.style.display = 'block';
    })();

    // ---- Close modals on Esc ----
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeRejectModal();
            closeBulkRejectModal();
            closeRecommendModal();
            closeRejectReasonModal();
        }
    });

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
    document.getElementById('sidebarToggle')?.addEventListener('click', toggleSidebar);
    document.getElementById('sidebarOverlay')?.addEventListener('click', toggleSidebar);

    // ═══════════════════════════════
    //  Must be : Approve / Reject (direct submit)
    // ═══════════════════════════════
    var mustBeUrl = '{{ url('admin/registrants') }}/REG_ID/client-remark';

    // Reject reason modal state
    var rrPending = { id: null, btn: null };

    function openRejectReason(id, btn) {
        rrPending = { id: id, btn: btn };
        var name = (btn && btn.getAttribute('data-name')) || '';
        document.getElementById('rrName').textContent = name;
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
        submitMustBe(rrPending.id, 'reject', reason);
    }

    function confirmApprove(id, btn) {
        var name = btn ? (btn.dataset.name || '') : '';
        if (!confirm('Are you sure you want to approve "' + name + '"?')) {
            return;
        }
        submitMustBe(id, 'approve');
    }

    function submitMustBe(id, action, reason) {
        var approveBtn = document.getElementById('mustbe-approve-' + id);
        var rejectBtn = document.getElementById('mustbe-reject-' + id);

        // Disable both during submit
        if (approveBtn) approveBtn.disabled = true;
        if (rejectBtn) rejectBtn.disabled = true;

        fetch(mustBeUrl.replace('REG_ID', id), {
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
                var isApprove = action === 'approve';
                var userName = '{{ Auth::user()->name }}';
                // Update button styling — keep emoji, set active/disabled state
                if (approveBtn) {
                    if (isApprove) {
                        approveBtn.className = 'px-2 py-1 rounded-lg text-xs font-semibold whitespace-nowrap bg-emerald-50 text-emerald-700 border border-emerald-200 cursor-default';
                    } else {
                        approveBtn.className = 'px-2 py-1 rounded-lg text-xs font-semibold whitespace-nowrap bg-gray-50 text-gray-300 border border-gray-100 cursor-default';
                    }
                    approveBtn.disabled = true;
                }
                if (rejectBtn) {
                    if (!isApprove) {
                        rejectBtn.className = 'px-2 py-1 rounded-lg text-xs font-semibold whitespace-nowrap bg-red-50 text-red-700 border border-red-200 cursor-default';
                    } else {
                        rejectBtn.className = 'px-2 py-1 rounded-lg text-xs font-semibold whitespace-nowrap bg-gray-50 text-gray-300 border border-gray-100 cursor-default';
                    }
                    rejectBtn.disabled = true;
                }
                // Update the status badge cell in the same row
                var row = (approveBtn || rejectBtn).closest('tr');
                var statusCell = row && row.querySelectorAll('td')[{{ Auth::user()->canWrite() ? 4 : 3 }}];
                if (statusCell) {
                    var oldRemark = statusCell.querySelector('.client-remark-label');
                    if (oldRemark) oldRemark.remove();
                    var remarkDiv = document.createElement('div');
                    remarkDiv.className = 'client-remark-label mt-1 text-[10px] ' + (isApprove ? 'text-emerald-600' : 'text-red-600');
                    remarkDiv.textContent = (isApprove ? '✅ Approve' : '❌ Reject') + ' by ' + userName + (reason ? ': ' + reason : '');
                    statusCell.appendChild(remarkDiv);
                }
            } else {
                alert('Error: ' + (data.error || 'Unknown'));
            }
        })
        .catch(function(err) {
            if (approveBtn) approveBtn.disabled = false;
            if (rejectBtn) rejectBtn.disabled = false;
            alert('Gagal: ' + err.message);
        });
    }
</script>

<style>
    .animate-fade-in { animation: fadeIn 0.2s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    .realtime-updated { animation: pulse-green 0.6s ease-out; }
    @keyframes pulse-green {
        0% { background-color: rgba(16, 185, 129, 0.3); }
        100% { background-color: transparent; }
    }
</style>

{{-- Real-time polling untuk stats cards (setiap 10 detik) --}}
<script>
(function(){
    var pollUrl = '{{ route("admin.dashboard.data") }}';
    setInterval(function() {
        fetch(pollUrl)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                document.querySelectorAll('[data-stat]').forEach(function(el) {
                    var key = el.getAttribute('data-stat');
                    var val = data[key];
                    if (val === undefined) return;
                    var oldVal = el.textContent.trim();
                    var numVal = Number(val);
                    var oldNum = Number(oldVal);
                    if (!isNaN(numVal) && oldNum !== numVal) {
                        el.textContent = numVal;
                        el.classList.remove('realtime-updated');
                        void el.offsetWidth;
                        el.classList.add('realtime-updated');
                    }
                });
            })
            .catch(function() {});
    }, 10000);
})();
</script>

{{-- Realtime collaboration: reflect other clients' markings (poll every 4s) --}}
<div id="realtimeToast" class="hidden fixed bottom-4 right-4 z-50 bg-indigo-600 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-lg"></div>
<script>
// ---- Pending-selection sync (share current toggles with other clients) ----
let pendingSyncTimer = null;
// Latest OTHER clients' pending (pre-submit) selections from the realtime poll —
// used so the decision bar counts match the editor's view
let realtimePending = [];
// The client's OWN full pending selections (from cache via realtime) — used so
// syncPending doesn't wipe selections on other pagination pages.
let myPendingCache = [];
// Reject reason options (from config) used to render the pending-reason dropdown
const clientRejectReasons = @json(config('client_reasons.reject'));
function syncPending() {
    const sel = [];
    document.querySelectorAll('.decision-toggle.decision-active').forEach(btn => {
        const item = { id: btn.dataset.id, action: btn.dataset.action };
        if (item.action === 'reject') {
            const reasonSel = document.querySelector('.decision-reason[data-id="' + item.id + '"]');
            item.reason = reasonSel ? reasonSel.value : '';
        }
        sel.push(item);
    });
    // Track which registrant rows are actually present on THIS page so we can keep
    // cached selections for off-page rows (pagination) while still removing rows the
    // user deselects on the current page.
    const inDom = new Set();
    document.querySelectorAll('.decision-toggle').forEach(function(btn) {
        inDom.add(String(btn.dataset.id));
    });
    (myPendingCache || []).forEach(function(p) {
        if (!p.id || !p.action) return;
        if (inDom.has(String(p.id))) return; // this page decides its own rows
        sel.push({ id: String(p.id), action: p.action, reason: p.reason || null });
    });
    clearTimeout(pendingSyncTimer);
    pendingSyncTimer = setTimeout(function() {
        fetch('{{ route("admin.registrants.pending-sync") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({ selections: sel }),
        }).catch(function() {});
    }, 400);
}
function updatePendingBadges(pending) {
    // Re-enable toggles that were previously disabled (row no longer claimed by another client)
    document.querySelectorAll('.decision-toggle.decision-disabled').forEach(b => {
        b.disabled = false;
        b.classList.remove('decision-disabled', 'opacity-40', 'cursor-not-allowed');
    });
    // Clear row tints added for OTHER clients' pending selections
    document.querySelectorAll('tr.rt-pending-row').forEach(r => {
        r.classList.remove('rt-pending-row', 'bg-emerald-50', 'bg-red-50', 'bg-yellow-50');
    });
    // Clear previous "other client pending" highlights and restore default tooltips
    document.querySelectorAll('.decision-toggle.rt-pending-active').forEach(b => {
        b.classList.remove('rt-pending-active','bg-emerald-50','text-emerald-600','border-emerald-300','bg-red-50','text-red-600','border-red-300','bg-orange-50','text-orange-600','border-orange-300');
        const a = b.dataset.action;
        b.title = a === 'approve' ? 'Mark as Approved' : a === 'reject' ? 'Mark as Rejected' : 'Mark as Waiting List';
    });
    document.querySelectorAll('.rt-pending-reason').forEach(el => el.remove());
    document.querySelectorAll('.rt-pending-reason-select').forEach(el => el.remove());
    if (!pending || !pending.length) return;
    const byReg = {};
    pending.forEach(p => {
        if (p.action && p.registrant_id) {
            (byReg[p.registrant_id] = byReg[p.registrant_id] || []).push(p);
        }
    });
    Object.keys(byReg).forEach(regId => {
        const link = document.querySelector('#registrantTableBody tr a[href*="/registrants/' + regId + '"]');
        const row = link ? link.closest('tr') : null;
        if (!row) return;
        // Rows already carrying a submitted remark are claimed — skip highlighting
        if (/Marked Approve|Marked Reject|Marked Waiting List|Approve by|Reject by/.test(row.textContent)) return;
        // Highlight the exact toggle the editor sees, so viewer's screen matches editor's
        byReg[regId].forEach(function(p) {
            const btn = row.querySelector('.decision-toggle[data-id="' + regId + '"][data-action="' + p.action + '"]');
            if (!btn) return;
            btn.classList.add('rt-pending-active');
            if (p.action === 'approve') btn.classList.add('bg-emerald-50','text-emerald-600','border-emerald-300');
            else if (p.action === 'reject') btn.classList.add('bg-red-50','text-red-600','border-red-300');
            else btn.classList.add('bg-orange-50','text-orange-600','border-orange-300');
            const extra = (p.action === 'reject' && p.reason) ? ': ' + p.reason : '';
            btn.title = '🕐 Pending by ' + p.client_name + extra + ' (not submitted yet)';
        });
        // A registrant can only be confirmed by ONE client — block other clients
        // from changing its status (no dual input): disable the toggles + clear
        // any stale own selection on this row.
        let staleActive = false;
        row.querySelectorAll('.decision-toggle').forEach(b => {
            b.disabled = true;
            b.classList.add('decision-disabled', 'opacity-40', 'cursor-not-allowed');
            if (b.classList.contains('decision-active')) {
                b.classList.remove('decision-active', 'bg-emerald-50', 'text-emerald-600', 'border-emerald-300', 'bg-red-50', 'text-red-600', 'border-red-300', 'bg-orange-50', 'text-orange-600', 'border-orange-300');
                row.classList.remove('decision-row', 'bg-emerald-50', 'bg-red-50', 'bg-yellow-50');
                staleActive = true;
            }
        });
        const rs = row.querySelector('.decision-reason[data-id="' + regId + '"]');
        if (rs) { rs.classList.add('hidden'); rs.value = ''; }
        if (staleActive) updateDecisionBar();
        // Tint the row so the viewer sees the same color as the editor (other clients' pending)
        if (!row.querySelector('.decision-toggle.decision-active')) {
            const a0 = byReg[regId][0] ? byReg[regId][0].action : null;
            if (a0 === 'approve') row.classList.add('rt-pending-row', 'bg-emerald-50');
            else if (a0 === 'reject') row.classList.add('rt-pending-row', 'bg-red-50');
            else if (a0 === 'waitlist') row.classList.add('rt-pending-row', 'bg-yellow-50');
        }
        // Show rejected reasons in realtime too — as a read-only dropdown below the reject button,
        // identical to the editor's own. Skip it if the viewer already picked their own reject here.
        const reasons = [...new Set(byReg[regId].filter(p => p.action === 'reject' && p.reason).map(p => p.reason))];
        const ownRejectActive = row.querySelector('.decision-toggle[data-id="' + regId + '"][data-action="reject"].decision-active');
        if (reasons.length && !ownRejectActive) {
            const rejBtn = row.querySelector('.decision-toggle[data-id="' + regId + '"][data-action="reject"]');
            const target = (rejBtn && rejBtn.closest('.flex.flex-col')) ? rejBtn.closest('.flex.flex-col') : row;
            const sel = document.createElement('select');
            sel.className = 'rt-pending-reason-select mt-1 w-36 text-[10px] border border-gray-300 rounded-lg px-2 py-1 bg-white';
            sel.disabled = true;
            sel.title = 'Pending reject reason — by colleague (not submitted yet)';
            sel.setAttribute('data-id', regId);
            const emptyOpt = document.createElement('option');
            emptyOpt.value = '';
            emptyOpt.textContent = '— Reason —';
            sel.appendChild(emptyOpt);
            (typeof clientRejectReasons !== 'undefined' ? clientRejectReasons : []).forEach(function(r) {
                const o = document.createElement('option');
                o.value = r;
                o.textContent = r;
                sel.appendChild(o);
            });
            sel.value = reasons[0];
            target.appendChild(sel);
        }
    });
}
// Restore the viewer's OWN pending selections into the toggle UI so they survive a
// page refresh / navigation (the selections are already stored in cache via syncPending).
function restoreMyPending(myPending) {
    if (!myPending || !myPending.length) return;
    myPending.forEach(function(p) {
        if (!p.id || !p.action) return;
        const link = document.querySelector('#registrantTableBody tr a[href*="/registrants/' + p.id + '"]');
        const row = link ? link.closest('tr') : null;
        if (!row) return;
        // Skip rows already carrying a submitted remark or claimed by another client
        if (/Marked Approve|Marked Reject|Marked Waiting List|Approve by|Reject by/.test(row.textContent)) return;
        if (row.querySelector('.decision-toggle.decision-disabled')) return;
        const btn = row.querySelector('.decision-toggle[data-id="' + p.id + '"][data-action="' + p.action + '"]');
        if (!btn) return;
        if (!btn.classList.contains('decision-active')) {
            decisionToggle(btn);
        }
        if (p.action === 'reject' && p.reason) {
            const sel = row.querySelector('.decision-reason[data-id="' + p.id + '"]');
            if (sel) sel.value = p.reason;
        }
    });
    updateDecisionBar();
}

(function() {
    const rtUrl = '{{ route("admin.registrants.realtime") }}';
    const rowsUrl = '{{ route("admin.registrants.rows") }}';
    const currentMy = @json($my);
    let since = new Date().toISOString();
    let toastTimer = null;

    function showToast(msg) {
        const t = document.getElementById('realtimeToast');
        if (!t) return;
        t.textContent = msg;
        t.classList.remove('hidden');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(function() { t.classList.add('hidden'); }, 4000);
    }
    // Cross-device presence: show which OTHER clients are collaborating right now.
    function updatePresence(presence) {
        const chip = document.getElementById('collabPresence');
        const list = document.getElementById('collabPresenceList');
        if (!chip || !list) return;
        const arr = (presence || []).filter(function(p) { return p && p.name; });
        if (!arr.length) { chip.classList.add('hidden'); chip.classList.remove('flex'); return; }
        list.textContent = arr.map(function(p) {
            return p.name + (p.pending > 0 ? ' (' + p.pending + ')' : '');
        }).join(', ');
        chip.classList.remove('hidden');
        chip.classList.add('flex');
    }
    function updateCounts(data) {
        if (data.myCounts) {
            ['approve','reject','waitlist','unmarked'].forEach(function(k) {
                const el = document.getElementById('myCount' + k.charAt(0).toUpperCase() + k.slice(1));
                if (el && data.myCounts[k] !== undefined) el.textContent = data.myCounts[k];
            });
        }
        if (data.stats) {
            ['total','pending','approved','rejected'].forEach(function(k) {
                const el = document.querySelector('[data-stat="' + k + '"]');
                if (el) el.textContent = data.stats[k];
            });
        }
    }
    function actionLabel(a) { return a === 'approve' ? 'Approved' : a === 'reject' ? 'Rejected' : 'Waiting List'; }
    function reRenderRows(ids, cb) {
        if (!ids.length) { cb(0); return; }
        fetch(rowsUrl + '?ids=' + ids.join(','))
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (!data.rows) { cb(0); return; }
                const wrap = document.createElement('tbody');
                wrap.innerHTML = data.rows;
                let replaced = 0;
                ids.forEach(function(id) {
                    const newLink = wrap.querySelector('tr a[href*="/registrants/' + id + '"]');
                    const oldLink = document.querySelector('#registrantTableBody tr a[href*="/registrants/' + id + '"]');
                    const newTr = newLink ? newLink.closest('tr') : null;
                    const oldTr = oldLink ? oldLink.closest('tr') : null;
                    if (newTr && oldTr) {
                        oldTr.outerHTML = newTr.outerHTML;
                        replaced++;
                    }
                });
                cb(replaced);
            })
            .catch(function() { cb(0); });
    }
    // Restore this client's own pending selections right away (survives refresh/navigation)
    fetch(rtUrl + '?since=' + encodeURIComponent(new Date().toISOString()))
        .then(function(r) { return r.json(); })
        .then(function(data) {
            realtimePending = data.pending || [];
            myPendingCache = data.myPending || [];
            updatePendingBadges(data.pending);
            restoreMyPending(data.myPending);
            updateDecisionBar();
            updatePresence(data.presence);
        })
        .catch(function() {});
    setInterval(function() {
        fetch(rtUrl + '?since=' + encodeURIComponent(since))
            .then(function(r) { return r.json(); })
            .then(function(data) {
                since = data.now || since;
                updateCounts(data);
                updatePendingBadges(data.pending);
                realtimePending = data.pending || [];
                myPendingCache = data.myPending || [];
                restoreMyPending(data.myPending);
                updateDecisionBar();
                updatePresence(data.presence);
                if (data.changed && data.changed.length) {
                    const ids = data.changed.map(function(c) { return c.id; });
                    reRenderRows(ids, function(replaced) {
                        // Fresh rows lose their highlights — re-apply other clients' pending
                        updatePendingBadges(data.pending);
                        if (replaced > 0) {
                            const shown = data.changed.slice(0, 2).map(function(c) {
                                return "'" + c.name + "' (" + actionLabel(c.action) + (c.by ? ' by ' + c.by : '') + ')';
                            }).join(', ');
                            showToast('Updated: ' + shown + (data.changed.length > 2 ? ' +' + (data.changed.length - 2) + ' more' : ''));
                        }
                    });
                }
            })
            .catch(function() {});
    }, 4000);
})();
</script>

</body>
</html>
