<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Onsite Event — {{ config('app.name') }}</title>
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
<div><h1 class="text-lg font-bold text-gray-900">Onsite Event</h1><p class="text-xs text-gray-500">Participant list & name badge printing</p></div>
</div>
<div class="flex items-center gap-3">
<span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200" title="Waktu saat ini">
    <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span id="realtimeClock" class="font-mono tabular-nums">--:--:--</span>
</span>
<span id="mqttBadge" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border {{ $mqttEnabled ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-100 text-gray-500 border-gray-200' }}" title="Topik MQTT badge printer">
    <span id="mqttDot" class="w-2 h-2 rounded-full {{ $mqttEnabled ? 'bg-emerald-500 animate-pulse' : 'bg-gray-400' }}"></span>
    <span class="font-mono">{{ $mqttTopic }}</span>
    <span id="mqttStatusText" class="ml-0.5 {{ $mqttEnabled ? 'text-emerald-600' : 'text-gray-400' }}">{{ $mqttEnabled ? 'ON' : 'OFF' }}</span>
</span>
<span class="text-xs text-gray-400 hidden sm:inline" id="printCount"></span>
@if (Auth::user()->hasPermission('registrants'))
<button onclick="openWalkinModal()"
        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 transition shadow-sm">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
Walkin Registration
</button>
<button onclick="copyWalkinLink()" title="Copy public walk-in link"
        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 transition border border-gray-200">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 015.656 0l2 2a4 4 0 01-5.656 5.656l-1.414-1.414M10.172 13.828a4 4 0 01-5.656 0l-2-2a4 4 0 015.656-5.656l1.414 1.414"/></svg>
Share Link
</button>
<a href="{{ route('admin.onsite.export-checkedin', request()->except(['status', 'checked_in', 'page'])) }}"
   class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-semibold rounded-xl bg-white text-gray-700 hover:bg-gray-50 transition border border-gray-200 shadow-sm"
   title="Export participants who have already checked in as CSV">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
Export Checked-in CSV
</a>
@endif
<button onclick="printSelected()" id="printSelectedBtn"
        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition shadow-sm">
<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
Print Badges
</button>
</div>
</div>
</header>

<div class="p-4 sm:p-6 lg:p-8 space-y-6">
@include('admin.partials.notification')

{{-- Status tabs (default: approved) --}}
<div class="flex flex-wrap gap-2">
    @php
        $tabs = [
            'all'      => ['label' => 'All', 'color' => 'gray'],
            'approved' => ['label' => 'Approved', 'color' => 'emerald'],
            'pending'  => ['label' => 'Pending', 'color' => 'amber'],
            'rejected' => ['label' => 'Rejected', 'color' => 'red'],
        ];
    @endphp
    @foreach ($tabs as $key => $tab)
        @php
            $isActive = $status === $key;
            $color = $tab['color'];
            $activeCls = match($color) {
                'emerald' => 'bg-emerald-600 text-white ring-emerald-600',
                'amber'   => 'bg-amber-500 text-white ring-amber-500',
                'red'     => 'bg-red-500 text-white ring-red-500',
                default   => 'bg-gray-900 text-white ring-gray-900',
            };
        @endphp
        <a href="{{ route('admin.onsite', array_merge(request()->except(['status', 'page']), ['status' => $key])) }}"
           class="px-4 py-2 rounded-xl text-sm font-semibold transition ring-2 ring-offset-2 {{ $isActive ? $activeCls : 'bg-white text-gray-600 ring-gray-100 hover:bg-gray-50' }}">
            {{ $tab['label'] }}
        </a>
    @endforeach
</div>

{{-- Stats cards --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total</p>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($total) }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm p-5">
        <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Approved</p>
        <p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($approved) }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-amber-100 shadow-sm p-5">
        <p class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Pending</p>
        <p class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($pending) }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-red-100 shadow-sm p-5">
        <p class="text-xs font-semibold text-red-500 uppercase tracking-wider">Rejected</p>
        <p class="text-2xl font-bold text-red-500 mt-1">{{ number_format($rejected) }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-indigo-100 shadow-sm p-5">
        <p class="text-xs font-semibold text-indigo-500 uppercase tracking-wider">Checked-in</p>
        <p class="text-2xl font-bold text-indigo-600 mt-1">{{ number_format($checkedInCount) }}</p>
    </div>
</div>

{{-- Filters --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
<form method="GET" action="{{ route('admin.onsite') }}" id="onsiteFilterForm" class="flex flex-wrap items-end gap-3">
    <input type="hidden" name="status" value="{{ $status }}">
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1">Search</label>
        <div class="relative">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik untuk mencari..."
                   oninput="liveSearch()" autocomplete="off"
                   class="px-3 py-2 pr-8 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 w-64">
            @if (request('search'))
            <a href="{{ route('admin.onsite', array_merge(request()->except(['search','page']), ['status' => $status])) }}"
               class="absolute right-2 top-1/2 -translate-y-1/2 p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-200 transition" title="Bersihkan pencarian">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </a>
            @endif
        </div>
    </div>
    <div>
        @include('admin.partials.profile-filter')
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1">Company</label>
        <select name="company" class="px-3 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
            <option value="">All companies</option>
            @foreach ($companies as $c)
                <option value="{{ $c }}" @selected(request('company') === $c)>{{ $c }}</option>
            @endforeach
        </select>
    </div>
    <div>
        @include('admin.partials.source-filter')
    </div>
    <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1">Checked-in</label>
        <select name="checked_in" class="px-3 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
            <option value="">All</option>
            <option value="yes" @selected(request('checked_in') === 'yes')>Checked-in</option>
            <option value="no" @selected(request('checked_in') === 'no')>Not checked-in</option>
        </select>
    </div>
    <div class="flex gap-2">
        <button type="submit" class="px-4 py-2 text-sm font-semibold rounded-xl bg-indigo-500 text-white hover:bg-indigo-600 transition">Filter</button>
        <a href="{{ route('admin.onsite', ['status' => $status]) }}" class="px-4 py-2 text-sm font-medium rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition">Reset</a>
    </div>
</form>
</div>

{{-- Participants table --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-4 border-b border-gray-100">
    <div class="flex items-center gap-3">
        <h2 class="text-base font-bold text-gray-900">
            {{ $status === 'approved' ? 'Approved Participants' : ($status === 'pending' ? 'Pending Participants' : ($status === 'rejected' ? 'Rejected Participants' : 'All Participants')) }}
        </h2>
        <span id="onsiteCount" class="text-xs text-gray-400">({{ $registrants->total() }})</span>
    </div>
    <div class="flex items-center gap-3">
        <label class="inline-flex items-center gap-2 text-xs text-gray-500 cursor-pointer select-none">
            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            Select all on page
        </label>
        <button onclick="printSelected()" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print Selected
        </button>
    </div>
</div>

<div id="onsiteTableContainer">
@include('admin.onsite._table', ['registrants' => $registrants, 'status' => $status, 'sort' => $sort, 'direction' => $direction])
</div>
</div>

</div>
</main>
</div>
@include('admin.partials.mobile-sidebar')

{{-- Walkin Registration Modal --}}
<div id="walkinModal" class="fixed inset-0 z-[80] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" onclick="closeWalkinModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <button type="button" onclick="closeWalkinModal()" class="absolute top-4 right-4 p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition z-10">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        {{-- Form state --}}
        <div id="walkinFormState">
            <div class="p-6 sm:p-8">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-emerald-100 mb-3">
                        <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Walk-in Registration</h2>
                    <p class="text-sm text-gray-500 mt-1">Register on-site attendees. They will be auto-approved and checked in.</p>
                </div>

                <div id="walkinErrors" class="hidden flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <ul class="list-disc list-inside text-sm" id="walkinErrorsList"></ul>
                </div>

                <form id="walkinForm" onsubmit="walkinSubmit('approved'); return false;" class="space-y-4">
                    @csrf
                    <input type="hidden" id="walkinMode" name="mode" value="approved">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">First Name <span class="text-red-500">*</span></label>
                            <input type="text" id="walkinFirstName" name="firstName" required autofocus
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition" placeholder="First Name">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Last Name <span class="text-red-500">*</span></label>
                            <input type="text" id="walkinLastName" name="lastName" required
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition" placeholder="Last Name">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Job Function <span class="text-red-500">*</span></label>
                            <select id="walkinJobFunction" name="job_title" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition cursor-pointer">
                                <option value="">Select Job Function</option>
                                <option>Intern</option>
                                <option>Staff</option>
                                <option>Supervisor</option>
                                <option>Manager</option>
                                <option>Senior Manager</option>
                                <option>General Manager</option>
                                <option>Head of Department</option>
                                <option>Chief</option>
                                <option>Director</option>
                                <option>President</option>
                                <option>Vice President</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Job Title <span class="text-red-500">*</span></label>
                            <select id="walkinJobRoleSelector" name="job_role_selector" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition cursor-pointer">
                                <option value="">Select Job Title</option>
                                <option>Student</option>
                                <option>Sales</option>
                                <option>Pre-Sales / Solution Architect</option>
                                <option>Engineering</option>
                                <option>Marketing</option>
                                <option>Management</option>
                                <option>Finance / Accounting</option>
                                <option>Information Technology</option>
                                <option>Operations</option>
                                <option>Human Resources</option>
                                <option>Legal / Compliance</option>
                                <option>Procurement</option>
                                <option>Research &amp; Development</option>
                                <option>Customer Service / Support</option>
                                <option>Consulting</option>
                                <option>Business Development</option>
                                <option>Administration</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2" id="walkinJobRoleSpecificField" style="display:none;">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Job Role</label>
                            <select id="walkinJobRoleSpecific" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition cursor-pointer">
                                <option value="">Select Job Role</option>
                                <option>IT Infrastructure</option>
                                <option>Infrastructure Engineer</option>
                                <option>System Administrator</option>
                                <option>Network Administrator</option>
                                <option>Network Engineer</option>
                                <option>IT Operations</option>
                                <option>IT Support</option>
                                <option>Helpdesk</option>
                                <option>Desktop Support</option>
                                <option>NOC Engineer</option>
                                <option>Data Center Engineer</option>
                                <option>IT Security</option>
                                <option>Cyber Security Analyst</option>
                                <option>Security Engineer</option>
                                <option>Security Operations Center</option>
                                <option>DevSecOps Engineer</option>
                                <option>IT Governance, Risk &amp; Compliance (GRC)</option>
                                <option>Software Engineer</option>
                                <option>Software Developer</option>
                                <option>Full Stack Developer</option>
                                <option>Front-End Developer</option>
                                <option>Back-End Developer</option>
                                <option>Mobile Developer</option>
                                <option>Web Developer</option>
                                <option>Application Developer</option>
                                <option>Technical Lead</option>
                                <option>Engineering Manager</option>
                                <option>Enterprise Architect</option>
                                <option>Solution Architect</option>
                                <option>Technical Architect</option>
                                <option>Cloud Architect</option>
                                <option>Application Architect</option>
                                <option>Data Analyst</option>
                                <option>Business Intelligence (BI) Analyst</option>
                                <option>Data Engineer</option>
                                <option>Data Scientist</option>
                                <option>AI Engineer</option>
                                <option>Machine Learning Engineer</option>
                                <option>Analytics</option>
                                <option>ERP</option>
                                <option>ERP Basis</option>
                                <option>ERP Functional</option>
                                <option>ERP Consultant</option>
                                <option>Business Application</option>
                                <option>Cloud Engineer</option>
                                <option>Cloud Administrator</option>
                                <option>DevOps Engineer</option>
                                <option>Site Reliability Engineer (SRE)</option>
                                <option>Platform Engineer</option>
                                <option>Engineer</option>
                                <option>Database Administrator (DBA)</option>
                                <option>Database Engineer</option>
                                <option>Database Architect</option>
                                <option>IT Project</option>
                                <option>IT Governance</option>
                                <option>IT Compliance</option>
                                <option>IT Audit</option>
                                <option>IT Risk</option>
                                <option>Digital Transformation</option>
                                <option>Digital Innovation</option>
                                <option>Digital Technology</option>
                                <option>IT Transformation</option>
                                <option>IT Business Analyst</option>
                                <option>System Analyst</option>
                                <option>IT Business Solution</option>
                                <option>IT Product Owner</option>
                                <option>IT Product</option>
                                <option>QA Engineer</option>
                                <option>Software</option>
                                <option>IT Engineer</option>
                                <option>IT Automation Engineer</option>
                                <option>IT Quality Assurance</option>
                            </select>
                        </div>
                        <input type="hidden" name="job_role" id="walkinJobRoleFinal">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Company Name <span class="text-red-500">*</span></label>
                            <input type="text" id="walkinCompany" name="company" required
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition" placeholder="Company name">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Business Email <span class="text-red-500">*</span></label>
                            <input type="email" id="walkinEmail" name="email" required
                                class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition" placeholder="email@example.com">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Mobile Phone <span class="text-red-500">*</span></label>
                            <div class="flex">
                                <span class="inline-flex items-center px-3 py-2.5 bg-gray-50 border border-gray-200 border-r-0 rounded-l-xl text-sm text-gray-500">+62</span>
                                <input type="text" id="walkinPhone" name="phone" required oninput="this.value=this.value.replace(/[^0-9]/g,'').replace(/^0/,'')"
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-r-xl rounded-l-none text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition" placeholder="815-xxx-xxxx">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Industry <span class="text-red-500">*</span></label>
                            <select id="walkinIndustry" name="industry" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition cursor-pointer">
                                <option value="">Select Industry</option>
                                <option>AGRICULTURE, FORESTRY</option>
                                <option>CHEMICALS</option>
                                <option>CONSTRUCTION, PROPERTY &amp; REAL ESTATE</option>
                                <option>DISTRIBUTION</option>
                                <option>EDUCATION</option>
                                <option>FINANCIAL SERVICES</option>
                                <option>FISHING &amp; MARINE</option>
                                <option>FOREIGN SERVICES</option>
                                <option>GOVERNMENT SERVICES</option>
                                <option>HEALTHCARE</option>
                                <option>HIGH TECHNOLOGY</option>
                                <option>HOSPITALITY / TOURISM</option>
                                <option>MANUFACTURING</option>
                                <option>MEDIA</option>
                                <option>MINING &amp; METALS</option>
                                <option>OIL &amp; GAS</option>
                                <option>PROFESSIONAL &amp; BUSINESS SERVICES</option>
                                <option>RETAIL, WHOLESALE</option>
                                <option>TELECOMMUNICATIONS</option>
                                <option>TRANSPORTATION</option>
                                <option>UTILITIES / PUBLIC SERVICES</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">How did you hear about this event? <span class="text-red-500">*</span></label>
                            <select id="walkinReferral" name="referral_source" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition cursor-pointer">
                                <option value="">Select one</option>
                                <option>LinkedIn</option>
                                <option>Instagram</option>
                                <option>Kompas Newspaper</option>
                                <option>Metrodata Website</option>
                                <option>Email</option>
                                <option>Metrodata Group Sales Representative / Colleague</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Number of Employee <span class="text-red-500">*</span></label>
                            <select id="walkinEmployees" name="employees" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition cursor-pointer">
                                <option value="">Select Number of Employee</option>
                                <option>1 – 50</option>
                                <option>51 – 200</option>
                                <option>201 – 500</option>
                                <option>501 – 1000</option>
                                <option>1000+</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="inline-flex items-start gap-2 text-xs text-gray-600 cursor-pointer">
                                <input type="checkbox" id="walkinGdpr" name="gdpr" required checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mt-0.5">
                                <span>By submitting this form, I understand Metrodata will process my personal information in accordance with their <strong><a href="https://www.metrodata.co.id/privacy-policy" target="_blank" class="text-indigo-600 underline">Privacy Notice</a></strong>. Additionally, I consent to my information being shared with <strong><a href="https://jovenindo.com/privacy-policy" target="_blank" class="text-indigo-600 underline">Event Partners</a></strong> in accordance. I understand I may withdraw my consent or update my information at any time.</span>
                            </label>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        <button type="button" onclick="walkinSubmit('pending')" id="walkinPendingBtn"
                                class="w-full py-3 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-semibold rounded-xl shadow-lg shadow-amber-500/25 transition-all text-sm">
                            <svg class="w-5 h-5 inline-block mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z"/></svg>
                            Register Only
                            <span class="block text-[11px] font-normal opacity-80 mt-0.5">goes to Pending</span>
                        </button>
                        <button type="button" onclick="walkinSubmit('approved')" id="walkinSubmitBtn"
                                class="w-full py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/25 transition-all text-sm">
                            <svg class="w-5 h-5 inline-block mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            Register & Print
                            <span class="block text-[11px] font-normal opacity-80 mt-0.5">approved & prints badge</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Success state --}}
        <div id="walkinSuccessState" class="hidden">
            <div class="p-6 sm:p-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-100 mb-4">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-1">Registered Successfully!</h2>
                <p class="text-sm text-gray-500 mb-2"><span id="walkinSuccessName" class="font-semibold text-gray-900"></span> <span id="walkinSuccessStatus">has been registered.</span></p>
                <p id="walkinPrintStatus" class="hidden text-xs font-semibold mb-6"></p>

                <div class="bg-gray-50 rounded-xl p-4 text-left text-sm space-y-2 mb-6">
                    <div class="flex justify-between"><span class="text-gray-400">Name</span><span class="font-semibold text-gray-900" id="walkinSuccessNameRow"></span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Email</span><span class="text-gray-700" id="walkinSuccessEmail"></span></div>
                    <div class="flex justify-between"><span class="text-gray-400">Unique Code</span><span class="font-mono text-sm font-semibold text-indigo-600" id="walkinSuccessUnique"></span></div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6 inline-block">
                    <img src="" alt="QR Code" class="w-44 h-44 mx-auto" id="walkinQrImg">
                </div>

                <div class="bg-gray-50 rounded-xl p-3 mb-6">
                    <p class="text-xs text-gray-400 mb-1">QR Check-in URL</p>
                    <p class="text-sm font-mono text-indigo-600 break-all" id="walkinCheckinUrl"></p>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="resetWalkinForm()" class="flex-1 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition text-sm">
                        Register Another
                    </button>
                    <button type="button" onclick="closeWalkinModal()" class="flex-1 py-3 border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition text-sm">
                        Done
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const printStatus = '{{ $status }}';

// Realtime clock in the header
(function () {
    const el = document.getElementById('realtimeClock');
    if (!el) return;
    const pad = n => String(n).padStart(2, '0');
    function tick() {
        const now = new Date();
        const date = now.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short' });
        const time = pad(now.getHours()) + ':' + pad(now.getMinutes()) + ':' + pad(now.getSeconds());
        el.textContent = date + ' · ' + time;
    }
    tick();
    setInterval(tick, 1000);
})();

// Live MQTT printer status — poll the badge endpoint every 5s so the
// ON/OFF badge updates near-real-time without reloading the page.
(function () {
    const badge = document.getElementById('mqttBadge');
    if (!badge) return;
    const dot = document.getElementById('mqttDot');
    const txt = document.getElementById('mqttStatusText');
    async function refresh() {
        try {
            const res = await fetch('{{ route("admin.onsite.mqtt-status") }}', {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin'
            });
            const data = await res.json();
            if (!data || typeof data.enabled === 'undefined') return;
            const on = !!data.enabled;
            badge.classList.toggle('bg-emerald-50', on);
            badge.classList.toggle('text-emerald-700', on);
            badge.classList.toggle('border-emerald-200', on);
            badge.classList.toggle('bg-gray-100', !on);
            badge.classList.toggle('text-gray-500', !on);
            badge.classList.toggle('border-gray-200', !on);
            dot.className = 'w-2 h-2 rounded-full ' + (on ? 'bg-emerald-500 animate-pulse' : 'bg-gray-400');
            txt.textContent = on ? 'ON' : 'OFF';
            txt.className = 'ml-0.5 ' + (on ? 'text-emerald-600' : 'text-gray-400');
        } catch (e) { /* ignore transient errors */ }
    }
    refresh();
    setInterval(refresh, 5000);
})();

function selectedIds() {
    return Array.from(document.querySelectorAll('.onsite-checkbox:checked')).map(cb => cb.value);
}

function buildPrintParams() {
    const ids = selectedIds();
    const params = new URLSearchParams();
    if (ids.length) {
        params.set('ids', ids.join(','));
    } else {
        params.set('status', printStatus); // fallback: all in current filter
    }
    return params;
}

function showToast(message, type) {
    const existing = document.getElementById('onsiteToast');
    if (existing) existing.remove();
    const toast = document.createElement('div');
    toast.id = 'onsiteToast';
    const colors = { success: 'bg-emerald-600', error: 'bg-red-600', info: 'bg-indigo-600' };
    toast.className = 'fixed bottom-6 right-6 z-[100] ' + (colors[type] || colors.info) + ' text-white px-5 py-3 rounded-xl shadow-lg text-sm font-semibold flex items-center gap-2';
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 4500);
}

async function triggerMqtt(params) {
    const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
    try {
        const res = await fetch('{{ route("admin.onsite.badges.trigger") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: params.toString() + '&_token=' + encodeURIComponent(token)
        });
        return await res.json();
    } catch (e) {
        return { success: false, message: 'Gagal terhubung ke server.' };
    }
}

function renderSendResult(data) {
    if (!data || !data.success) {
        showToast('\u274c ' + (data?.message || 'Gagal mengirim ke MQTT'), 'error');
        return;
    }
    if (!data.enabled) {
        showToast('\u26a0\ufe0f MQTT belum diaktifkan \u2014 data tidak terkirim', 'info');
        return;
    }
    if (data.published > 0) {
        const approvedCount = (Array.isArray(data.approved_ids) && data.approved_ids.length) ? data.approved_ids.length : 0;
        const extra = approvedCount ? ' & ' + approvedCount + ' approved' : '';
        showToast('✅ ' + data.published + ' dari ' + data.total + ' badge dikirim & ditandai check-in' + extra, 'success');
        // Update the checked-in cells immediately (no refresh)
        if (Array.isArray(data.ids)) data.ids.forEach(id => updateCheckinCell(id, data.checked_in_at));
        // Pending participants were auto-approved → refresh the Pending list so they move out.
        if (printStatus === 'pending' && approvedCount) {
            setTimeout(() => window.location.reload(), 1200);
        }
    } else {
        showToast('⚠️ Terhubung ke MQTT tapi tidak ada yang terkirim', 'info');
    }
}

// Live search — fetch results via AJAX (no page reload, cursor stays active).
// A sequence guard ignores stale responses so the latest query always wins.
let searchTimer = null;
let searchSeq = 0;
async function liveSearch() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(async () => {
        const seq = ++searchSeq;
        const input = document.querySelector('input[name="search"]');
        const form = document.getElementById('onsiteFilterForm');
        if (!form) return;
        const params = new URLSearchParams(new FormData(form));
        params.set('page', '1');
        try {
            const res = await fetch('{{ route("admin.onsite.search") }}?' + params.toString(), { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            if (seq !== searchSeq) return; // stale response, ignore
            if (data && data.html) {
                const container = document.getElementById('onsiteTableContainer');
                if (container) container.innerHTML = data.html;
                const countEl = document.getElementById('onsiteCount');
                if (countEl) countEl.textContent = '(' + (data.total || 0) + ')';
                bindTableEvents();
            }
        } catch (e) {}
        if (input) {
            input.focus();
            input.setSelectionRange(input.value.length, input.value.length);
        }
    }, 300);
}

function updateCheckinCell(id, time) {
    // Passive check symbol (status indicator) — filled green when checked-in
    const el = document.querySelector('[data-checkin-indicator="' + id + '"]');
    if (el) {
        el.className = 'inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600';
        el.title = 'Sudah check-in';
        el.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4"/><rect x="4" y="4" width="16" height="16" rx="3" fill="none"/></svg>';
    }
    const cell = document.getElementById('checkin-' + id);
    if (cell) {
        cell.innerHTML = '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">' +
            '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' +
            (time || '✓') + '</span>';
    }
}

async function printSelected() {
    const params = buildPrintParams();
    const ids = selectedIds();
    const count = ids.length ? ids.length : {{ $bulkCount }};

    // Bulk print confirmation (sends to the physical printer)
    if (count > 1) {
        const ok = confirm('Cetak ' + count + ' badge sekaligus ke printer?\n\nPastikan printer siap dan stok label cukup.');
        if (!ok) return;
    }

    const btn = document.getElementById('printSelectedBtn');
    const original = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span> Mengirim ' + count + '...';
    const data = await triggerMqtt(params);
    btn.disabled = false;
    btn.innerHTML = original;
    renderSendResult(data);
}

async function printOne(id, btn) {
    const params = new URLSearchParams({ ids: id });
    btn.disabled = true;
    btn.classList.add('opacity-60');
    const data = await triggerMqtt(params);
    btn.disabled = false;
    btn.classList.remove('opacity-60');
    renderSendResult(data);
}

function updatePrintCount() {
    const n = selectedIds().length;
    const el = document.getElementById('printCount');
    if (el) el.textContent = n ? n + ' selected' : '';
    const btn = document.getElementById('printSelectedBtn');
    if (btn) {
        if (n) {
            btn.classList.remove('bg-indigo-600');
            btn.classList.add('bg-emerald-600');
            btn.querySelector('span')?.remove();
            btn.insertAdjacentHTML('afterbegin', '<span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-300 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-200"></span></span>');
            btn.title = 'Print ' + n + ' selected badge(s)';
        } else {
            btn.classList.remove('bg-emerald-600');
            btn.classList.add('bg-indigo-600');
            btn.title = 'Print all (' + printStatus + ')';
        }
    }
}

function bindSelectAll(selectAllEl) {
    if (!selectAllEl) return;
    selectAllEl.addEventListener('change', () => {
        document.querySelectorAll('.onsite-checkbox').forEach(cb => cb.checked = selectAllEl.checked);
        updatePrintCount();
    });
}

// Re-bind table events after the AJAX search replaces the table HTML
function bindTableEvents() {
    document.querySelectorAll('.onsite-checkbox').forEach(cb => cb.addEventListener('change', updatePrintCount));
    bindSelectAll(document.getElementById('selectAll'));
    bindSelectAll(document.getElementById('selectAllTable'));
    updatePrintCount();
}

bindTableEvents();

document.getElementById('sidebarToggle')?.addEventListener('click', () => {
    document.getElementById('mobileSidebar')?.classList.toggle('-translate-x-full');
    document.getElementById('sidebarOverlay')?.classList.toggle('hidden');
});

// ── Walk-in Registration popup ──
let walkinRegistered = false;

function openWalkinModal() {
    resetWalkinForm();
    const modal = document.getElementById('walkinModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    updateWalkinJobRole();
    document.getElementById('walkinFirstName')?.focus();
}

function closeWalkinModal() {
    const modal = document.getElementById('walkinModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    // Reload once so the newly registered walk-in appears in the list + stats.
    if (walkinRegistered) window.location.reload();
}

function resetWalkinForm() {
    document.getElementById('walkinForm')?.reset();
    const mode = document.getElementById('walkinMode');
    if (mode) mode.value = 'approved';
    document.getElementById('walkinErrors')?.classList.add('hidden');
    document.getElementById('walkinErrorsList') && (document.getElementById('walkinErrorsList').innerHTML = '');
    const printEl = document.getElementById('walkinPrintStatus');
    if (printEl) printEl.classList.add('hidden');
    updateWalkinJobRole();
    document.getElementById('walkinFormState')?.classList.remove('hidden');
    document.getElementById('walkinSuccessState')?.classList.add('hidden');
}

// Job Function / Job Title / Job Role toggle (same as the general registration form).
function updateWalkinJobRole() {
    const title = document.getElementById('walkinJobRoleSelector');
    const roleField = document.getElementById('walkinJobRoleSpecificField');
    const roleSelect = document.getElementById('walkinJobRoleSpecific');
    const roleFinal = document.getElementById('walkinJobRoleFinal');
    if (!title || !roleFinal) return;
    const t = title.value || '';
    const isIT = t === 'Information Technology';
    if (roleField) roleField.style.display = isIT ? '' : 'none';
    if (roleSelect) { roleSelect.required = isIT; if (!isIT) roleSelect.value = ''; }
    const sp = roleSelect ? roleSelect.value : '';
    roleFinal.value = sp ? (t + ' - ' + sp) : t;
}
document.getElementById('walkinJobRoleSelector')?.addEventListener('change', updateWalkinJobRole);
document.getElementById('walkinJobRoleSpecific')?.addEventListener('change', updateWalkinJobRole);

function walkinSubmit(mode) {
    document.getElementById('walkinMode').value = mode;
    submitWalkin();
}

async function submitWalkin() {
    const form = document.getElementById('walkinForm');
    const errorsBox = document.getElementById('walkinErrors');
    const errorsList = document.getElementById('walkinErrorsList');
    errorsBox.classList.add('hidden');
    errorsList.innerHTML = '';
    const btn = document.getElementById('walkinSubmitBtn');
    const pendingBtn = document.getElementById('walkinPendingBtn');
    const original = btn.innerHTML;
    btn.disabled = true;
    if (pendingBtn) pendingBtn.disabled = true;
    btn.innerHTML = '<span class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin inline-block align-middle mr-1.5 -mt-0.5"></span>Registering...';
    const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
    try {
        const body = new URLSearchParams(new FormData(form));
        const res = await fetch('{{ route("admin.walkin.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': token
            },
            body: body.toString()
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok) {
            if (data.errors) {
                errorsList.innerHTML = Object.values(data.errors).flat().map(e => '<li>' + e + '</li>').join('');
                errorsBox.classList.remove('hidden');
            } else {
                showToast('❌ ' + (data.message || 'Registration failed.'), 'error');
            }
            btn.disabled = false;
            if (pendingBtn) pendingBtn.disabled = false;
            btn.innerHTML = original;
            return;
        }
        if (!data.success) {
            showToast('❌ ' + (data.message || 'Registration failed.'), 'error');
            btn.disabled = false;
            if (pendingBtn) pendingBtn.disabled = false;
            btn.innerHTML = original;
            return;
        }
        walkinRegistered = true;
        showWalkinSuccess(data.registrant || {}, data.mode || 'approved', !!data.printed);
    } catch (e) {
        showToast('❌ Gagal terhubung ke server.', 'error');
        btn.disabled = false;
        if (pendingBtn) pendingBtn.disabled = false;
        btn.innerHTML = original;
    }
}

function showWalkinSuccess(r, mode, printed) {
    document.getElementById('walkinSuccessName').textContent = r.name || '';
    document.getElementById('walkinSuccessNameRow').textContent = r.name || '';
    document.getElementById('walkinSuccessEmail').textContent = r.email || '';
    document.getElementById('walkinSuccessUnique').textContent = r.unique_code || '-';
    const statusEl = document.getElementById('walkinSuccessStatus');
    const printEl = document.getElementById('walkinPrintStatus');
    if (mode === 'pending') {
        statusEl.textContent = 'has been registered as pending.';
        printEl.classList.add('hidden');
    } else {
        statusEl.textContent = 'has been registered, approved, and checked in.';
        printEl.textContent = printed ? '✅ Badge sent to printer' : '⚠️ Badge NOT sent (printer inactive)';
        printEl.classList.remove('hidden');
        printEl.className = 'text-xs font-semibold mb-6 ' + (printed ? 'text-emerald-600' : 'text-amber-600');
    }
    const qr = document.getElementById('walkinQrImg');
    if (r.qr_code_url) qr.src = r.qr_code_url;
    const urlEl = document.getElementById('walkinCheckinUrl');
    urlEl.textContent = r.qr_checkin_url || '';
    document.getElementById('walkinFormState').classList.add('hidden');
    document.getElementById('walkinSuccessState').classList.remove('hidden');
}

// Copy the public walk-in link so it can be shared (e.g. via WhatsApp).
function fallbackCopyWalkin(text, done) {
    const ta = document.createElement('textarea');
    ta.value = text;
    ta.style.position = 'fixed';
    ta.style.opacity = '0';
    document.body.appendChild(ta);
    ta.select();
    try { document.execCommand('copy'); done(); } catch (e) { showToast('❌ Gagal menyalin link', 'error'); }
    ta.remove();
}

function copyWalkinLink() {
    const url = '{{ route("walkin.public.create") }}';
    const done = () => showToast('✅ Public walk-in link copied', 'success');
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(done).catch(() => fallbackCopyWalkin(url, done));
    } else {
        fallbackCopyWalkin(url, done);
    }
}
</script>
</body>
</html>
