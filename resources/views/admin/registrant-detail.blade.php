<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Registrant — {{ config('app.name') }}</title>
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
            <div class="flex items-center h-16 px-4 sm:px-6 lg:px-8 gap-4">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Dashboard
                </a>
                <span class="text-gray-300">/</span>
                <h1 class="text-lg font-bold text-gray-900">Detail Registrant</h1>
            </div>
        </header>

        <div class="p-4 sm:p-6 lg:p-8">
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                {{-- Main detail card --}}
                <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    {{-- Header card --}}
                    <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-lg font-bold flex-shrink-0">
                            {{ strtoupper(substr($registrant->name, 0, 1)) }}
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">{{ $registrant->name }}</h2>
                            <p class="text-xs text-gray-500">ID: #{{ $registrant->id }}</p>
                        </div>
                        <div class="ml-auto">
                            @if ($registrant->status === 'approved')
                                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Approved
                                </span>
                            @elseif ($registrant->status === 'rejected')
                                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Rejected
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Pending
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Detail fields --}}
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Email</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $registrant->email }}</dd>
                            </div>
                            @if ($registrant->plain_password)
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Password</dt>
                                <dd class="text-sm font-medium text-gray-900 flex items-center gap-2">
                                    <code class="bg-gray-200 px-2 py-0.5 rounded select-all text-xs" id="detailPwd">{{ $registrant->plain_password }}</code>
                                    <button onclick="copyDetailPwd()" class="text-gray-400 hover:text-gray-600 transition" title="Copy password">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    </button>
                                </dd>
                            </div>
                            @endif
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Phone</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $registrant->phone ?? '—' }}</dd>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Job Title</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $registrant->job_title ?? '—' }}</dd>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Job Role</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $registrant->job_role ?? '—' }}</dd>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Company</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $registrant->company ?? '—' }}</dd>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Industry</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $registrant->industry ?? '—' }}</dd>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Employees</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $registrant->employees ?? '—' }}</dd>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">GDPR Consent</dt>
                                <dd class="text-sm font-medium">
                                    @if ($registrant->gdpr)
                                        <span class="inline-flex items-center gap-1 text-emerald-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Consented
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </dd>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Registered At</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $registrant->created_at->copy()->addHours(7)->format('d M Y, H:i') }}</dd>
                            </div>
                            @if ($registrant->first_name || $registrant->last_name)
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">First Name</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $registrant->first_name ?? '—' }}</dd>
                            </div>
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Last Name</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $registrant->last_name ?? '—' }}</dd>
                            </div>
                            @endif
                            @if ($registrant->referral_code)
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Referral Code</dt>
                                <dd class="text-sm font-medium text-gray-900 font-mono">{{ $registrant->referral_code }}</dd>
                            </div>
                            @endif
                            @if ($registrant->utm_source)
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">UTM Source</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $registrant->utm_source }}</dd>
                            </div>
                            @endif

                        </div>

                        @if ($registrant->notes)
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Registrant Notes</dt>
                                <dd class="text-sm text-gray-700">{{ $registrant->notes }}</dd>
                            </div>
                        @endif

                        {{-- Admin Remarks / Notes (inline editable) --}}
                        <div class="bg-yellow-50 rounded-xl p-4 border border-yellow-200" id="adminNotesCard">
                            <div class="flex items-center justify-between mb-2">
                                <dt class="text-xs font-semibold text-yellow-600 uppercase tracking-wider">Admin Remarks</dt>
                                @if (Auth::user()->canWrite())
                                <button onclick="toggleAdminNotesEdit()"
                                        class="text-xs font-medium text-yellow-700 hover:text-yellow-900 bg-yellow-100 hover:bg-yellow-200 px-3 py-1 rounded-lg transition"
                                        id="editNotesBtn">
                                    ✏️ {{ $registrant->admin_notes ? 'Edit' : 'Add Note' }}
                                </button>
                                @endif
                            </div>
                            {{-- Display mode --}}
                            <dd class="text-sm text-gray-800 whitespace-pre-wrap" id="adminNotesDisplay">
                                {{ $registrant->admin_notes ?: 'No remarks yet.' }}
                            </dd>
                            {{-- Edit mode (hidden by default) --}}
                            @if (Auth::user()->canWrite())
                            <div id="adminNotesEdit" class="hidden">
                                <textarea id="adminNotesInput" rows="4"
                                          class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 resize-none"
                                          placeholder="Add your remarks about this registrant...">{{ $registrant->admin_notes }}</textarea>
                                <div class="flex items-center gap-2 mt-2">
                                    <button onclick="saveAdminNotes()"
                                            class="px-4 py-2 text-sm font-semibold rounded-xl bg-yellow-500 text-white hover:bg-yellow-600 shadow-sm transition">
                                        💾 Save Remarks
                                    </button>
                                    <button onclick="cancelAdminNotesEdit()"
                                            class="px-4 py-2 text-sm font-medium rounded-xl bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                                        Cancel
                                    </button>
                                    <span id="notesStatus" class="text-xs text-gray-400 ml-2 hidden"></span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    @if (Auth::user()->canWrite())
                    <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.registrants.edit', $registrant) }}"
                               class="px-4 py-2 text-sm font-medium rounded-xl bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 transition">
                                ✏️ Edit
                            </a>
                            <form action="{{ route('admin.registrants.destroy', $registrant) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Delete {{ addslashes($registrant->name) }} permanently?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-4 py-2 text-sm font-medium rounded-xl bg-red-50 text-red-700 hover:bg-red-100 border border-red-200 transition">
                                    🗑 Delete
                                </button>
                            </form>
                        </div>
                        <div class="flex items-center gap-2.5">
                            @if ($registrant->isPending())
                                <form action="{{ route('admin.registrants.approve', $registrant) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            onclick="return confirm('Approve {{ addslashes($registrant->name) }}?')"
                                            class="px-5 py-2.5 text-sm font-semibold rounded-xl bg-emerald-500 text-white hover:bg-emerald-600 shadow-sm shadow-emerald-200 transition">
                                        ✓ Approve
                                    </button>
                                </form>
                            @endif
                            @if ($registrant->status === 'approved' && $registrant->plain_password)
                                <form action="{{ route('admin.registrants.resend-credentials', $registrant) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            onclick="return confirm('Resend credentials to {{ addslashes($registrant->name) }}?')"
                                            class="px-4 py-2.5 text-sm font-semibold rounded-xl bg-blue-500 text-white hover:bg-blue-600 shadow-sm shadow-blue-200 transition">
                                        📧 Resend Credentials
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Sidebar column --}}
                <div class="xl:col-span-1 space-y-5">
                    {{-- Quick stats --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-bold text-gray-800">Quick Stats</h3>
                        </div>
                        <div class="p-5 space-y-4">
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Unique Code</p>
                                <p class="text-sm font-bold text-gray-900 font-mono">{{ $registrant->unique_code ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Registered</p>
                                <p class="text-sm font-bold text-gray-900">{{ $registrant->created_at->copy()->addHours(7)->format('d M Y, H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Processed</p>
                                <p class="text-sm font-bold text-gray-900">{{ $registrant->processed_at?->copy()->addHours(7)->format('d M Y, H:i') ?? '—' }}</p>
                                @if ($registrant->status === 'approved' && $registrant->approver)
                                    <p class="text-xs text-gray-500 mt-0.5">by {{ $registrant->approver->name }}</p>
                                @elseif ($registrant->status === 'rejected' && $registrant->rejecter)
                                    <p class="text-xs text-gray-500 mt-0.5">by {{ $registrant->rejecter->name }}</p>
                                @endif
                            </div>
                    </div>

                    {{-- Email History --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Email History
                                <span class="text-xs font-normal text-gray-400">({{ $emailLogs->count() }} sent)</span>
                            </h3>
                        </div>
                        <div class="p-5 space-y-4">
                            {{-- Expected email types (sent & missing) --}}
                            @if (count($expectedTypes) > 0)
                                <div class="space-y-2">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Email Status</p>
                                    @foreach ($expectedTypes as $et)
                                        <div class="flex items-center justify-between gap-3 p-2.5 rounded-lg {{ $et['sent'] ? 'bg-emerald-50/50' : 'bg-gray-50' }}">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                @if ($et['sent'])
                                                    <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                @else
                                                    <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                @endif
                                                <span class="text-xs font-medium {{ $et['sent'] ? 'text-emerald-800' : 'text-gray-500' }}">{{ $et['label'] }}</span>
                                            </div>
                                            @if ($et['sent'])
                                                <span class="text-xs font-medium text-emerald-600 bg-emerald-100 px-2 py-0.5 rounded-full flex-shrink-0">Sent</span>
                                            @else
                                                <span class="text-xs font-medium text-gray-400 bg-gray-200 px-2 py-0.5 rounded-full flex-shrink-0">Not sent</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Detailed email logs --}}
                            @if ($emailLogs->count() > 0)
                                <div class="space-y-2">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Log Details</p>
                                    <div class="space-y-2">
                                        @foreach ($emailLogs as $log)
                                            <div class="flex items-start gap-3 p-3 rounded-xl {{ $log->status === 'sent' ? 'bg-emerald-50' : ($log->status === 'failed' ? 'bg-red-50' : ($log->status === 'bounced' ? 'bg-orange-50' : 'bg-gray-50')) }}">
                                                <div class="flex-shrink-0 mt-0.5">
                                                    @if ($log->status === 'sent')
                                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    @elseif ($log->status === 'failed')
                                                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    @elseif ($log->status === 'bounced')
                                                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                                    @else
                                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs font-semibold text-gray-800 truncate">{{ $log->subject }}</p>
                                                    <p class="text-xs text-gray-500 mt-0.5">
                                                        <span class="capitalize">{{ str_replace('_', ' ', $log->template_type) }}</span>
                                                        &middot;
                                                        {{ $log->sent_at?->copy()->addHours(7)->format('d M Y, H:i') ?? '—' }}
                                                    </p>
                                                    @if ($log->status === 'failed' && $log->error_message)
                                                        <p class="text-xs text-red-500 mt-0.5 truncate">{{ $log->error_message }}</p>
                                                    @endif
                                                </div>
                                                <span class="text-xs font-medium px-2 py-0.5 rounded-full flex-shrink-0 {{ $log->status === 'sent' ? 'bg-emerald-100 text-emerald-700' : ($log->status === 'failed' ? 'bg-red-100 text-red-600' : ($log->status === 'bounced' ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-600')) }}">
                                                    {{ ucfirst($log->status) }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Workshops --}}
                    @if ($workshops->count() > 0)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Workshops ({{ $workshops->count() }})
                            </h3>
                        </div>
                        <div class="p-5">
                            <div class="flex flex-wrap gap-2">
                                @foreach ($workshops as $w)
                                    <a href="{{ route('admin.workshops.registrants', $w) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                        {{ $w->title }}
                                        @php $pw = $w->pivot; @endphp
                                        @if ($pw)
                                            <span class="text-xs px-1.5 py-0.5 rounded-full {{ $pw->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($pw->status === 'rejected' ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-700') }}">{{ $pw->status }}</span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Agenda Sessions --}}
                    @if ($agendaItems->count() > 0)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Sessions ({{ $agendaItems->count() }})
                            </h3>
                        </div>
                        <div class="p-5">
                            <div class="flex flex-wrap gap-2">
                                @foreach ($agendaItems as $item)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-gray-50 text-gray-700 rounded-lg border border-gray-200">
                                        {{ $item->title }}
                                        @php $as = $item->pivot->status ?? 'pending'; @endphp
                                        <span class="text-xs px-1.5 py-0.5 rounded-full {{ $as === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($as === 'rejected' ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-700') }}">{{ $as }}</span>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- QR Code --}}
                    @if ($registrant->qr_token)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                </svg>
                                QR Code
                            </h3>
                        </div>
                        <div class="p-5 text-center">
                            <img src="{{ $registrant->qr_code_url }}" alt="QR Code" class="w-32 h-32 mx-auto rounded-lg border border-gray-200 mb-3">
                            <div class="flex items-center gap-2">
                                <input type="text" value="{{ $registrant->qr_share_url }}" readonly
                                       class="text-xs text-indigo-600 bg-indigo-50 px-2 py-1.5 rounded-lg w-full border-0 cursor-text"
                                       id="qrShareUrl">
                                <button onclick="copyQrUrl()"
                                        class="px-3 py-1.5 text-xs font-medium rounded-lg bg-indigo-500 text-white hover:bg-indigo-600 transition whitespace-nowrap flex-shrink-0">
                                    Copy
                                </button>
                            </div>
                            <a href="{{ $registrant->qr_share_url }}" target="_blank"
                               class="inline-block text-xs text-indigo-600 hover:text-indigo-800 font-medium mt-2">
                                Preview QR →
                            </a>
                            @if ($registrant->checked_in_at)
                                <p class="text-xs text-emerald-600 font-semibold mt-2">
                                    ✓ Checked in at {{ $registrant->checked_in_at->copy()->addHours(7)->format('H:i, d M Y') }}
                                </p>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
    </main>
</div>

<script>
    function copyDetailPwd() {
        const el = document.getElementById('detailPwd');
        if (!el) return;
        navigator.clipboard.writeText(el.textContent).then(() => {
            const orig = el.textContent;
            el.textContent = 'Copied!';
            setTimeout(() => el.textContent = orig, 1200);
        });
    }

    function copyQrUrl() {
        const el = document.getElementById('qrShareUrl');
        if (!el) return;
        navigator.clipboard.writeText(el.value).then(() => {
            const btn = event.target;
            const orig = btn.textContent;
            btn.textContent = 'Copied!';
            btn.classList.add('bg-emerald-500');
            btn.classList.remove('bg-indigo-500', 'hover:bg-indigo-600');
            setTimeout(() => {
                btn.textContent = orig;
                btn.classList.remove('bg-emerald-500');
                btn.classList.add('bg-indigo-500', 'hover:bg-indigo-600');
            }, 1500);
        });
    }

    // ── Admin Notes inline editing ──
    const notesDisplay = document.getElementById('adminNotesDisplay');
    const notesEdit   = document.getElementById('adminNotesEdit');
    const notesInput  = document.getElementById('adminNotesInput');
    const editBtn     = document.getElementById('editNotesBtn');
    const notesStatus = document.getElementById('notesStatus');
    const notesCard   = document.getElementById('adminNotesCard');

    function toggleAdminNotesEdit() {
        notesDisplay.classList.add('hidden');
        notesEdit.classList.remove('hidden');
        editBtn.classList.add('hidden');
        notesInput.focus();
        notesInput.setSelectionRange(notesInput.value.length, notesInput.value.length);
    }

    function cancelAdminNotesEdit() {
        notesDisplay.classList.remove('hidden');
        notesEdit.classList.add('hidden');
        editBtn.classList.remove('hidden');
        notesInput.value = notesDisplay.textContent.trim() === 'No remarks yet.' ? '' : notesDisplay.textContent.trim();
    }

    async function saveAdminNotes() {
        const notes = notesInput.value.trim();
        const btn = event.target;
        const origText = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Saving...';
        notesStatus.classList.add('hidden');

        try {
            const res = await fetch('{{ route('admin.registrants.notes', $registrant) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ admin_notes: notes }),
            });

            const data = await res.json();

            if (res.ok && data.success) {
                notesDisplay.textContent = data.notes || 'No remarks yet.';
                notesDisplay.classList.remove('hidden');
                notesEdit.classList.add('hidden');
                editBtn.classList.remove('hidden');
                editBtn.textContent = data.notes ? '✏️ Edit' : '✏️ Add Note';
                notesCard.classList.add('bg-yellow-50', 'border-yellow-200');
                notesCard.classList.remove('bg-yellow-100', 'border-yellow-300');
                // Flash effect
                notesCard.classList.add('ring-2', 'ring-yellow-400');
                setTimeout(() => notesCard.classList.remove('ring-2', 'ring-yellow-400'), 1500);
            } else {
                notesStatus.textContent = data.error || 'Failed to save.';
                notesStatus.classList.remove('hidden');
            }
        } catch (e) {
            notesStatus.textContent = 'Network error. Please try again.';
            notesStatus.classList.remove('hidden');
        } finally {
            btn.disabled = false;
            btn.textContent = origText;
        }
    }
</script>

</body>
</html>
