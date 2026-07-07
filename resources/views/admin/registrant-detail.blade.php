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
            <div class="max-w-2xl">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
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
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Organization</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $registrant->organization ?? '—' }}</dd>
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
                                <dd class="text-sm font-medium text-gray-900">{{ $registrant->created_at->format('d M Y, H:i') }}</dd>
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
                            @if ($registrant->attended_before)
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Returning</dt>
                                <dd class="text-sm font-medium text-indigo-600">Previously attended</dd>
                            </div>
                            @endif
                        </div>

                        @if ($registrant->notes)
                            <div class="bg-gray-50 rounded-xl p-4">
                                <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Registrant Notes</dt>
                                <dd class="text-sm text-gray-700">{{ $registrant->notes }}</dd>
                            </div>
                        @endif

                        @if ($registrant->admin_notes)
                            <div class="bg-{{ $registrant->status === 'approved' ? 'emerald' : 'red' }}-50 rounded-xl p-4 border border-{{ $registrant->status === 'approved' ? 'emerald' : 'red' }}-200">
                                <dt class="text-xs font-semibold text-{{ $registrant->status === 'approved' ? 'emerald' : 'red' }}-500 uppercase tracking-wider mb-1">Admin Notes</dt>
                                <dd class="text-sm text-gray-800">{{ $registrant->admin_notes }}</dd>
                            </div>
                        @endif

                        @if ($registrant->processed_at)
                            <p class="text-xs text-gray-400 text-right">
                                Processed: {{ $registrant->processed_at->format('d M Y, H:i') }}
                                @if ($registrant->status === 'approved' && $registrant->approver)
                                    <br>by <strong>{{ $registrant->approver->name }}</strong>
                                @elseif ($registrant->status === 'rejected' && $registrant->rejecter)
                                    <br>by <strong>{{ $registrant->rejecter->name }}</strong>
                                @endif
                            </p>
                        @endif
                    </div>

                    {{-- Workshops --}}
                    @if ($registrant->status === 'approved' && $workshops->count() > 0)
                        <div class="border-t border-gray-100">
                            <div class="px-6 py-4">
                                <h3 class="text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Registered Workshops ({{ $workshops->count() }})
                                </h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($workshops as $w)
                                        <a href="{{ route('admin.workshops.registrants', $w) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                            </svg>
                                            {{ $w->title }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- QR Code --}}
                    @if ($registrant->qr_token)
                        <div class="border-t border-gray-100">
                            <div class="px-6 py-4">
                                <h3 class="text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                    </svg>
                                    QR Code
                                </h3>
                                <div class="flex items-center gap-4">
                                    <img src="{{ $registrant->qr_code_url }}" alt="QR Code" class="w-28 h-28 rounded-lg border border-gray-200">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-2">Scan for check-in</p>
                                        <a href="{{ $registrant->qr_checkin_url }}" target="_blank"
                                           class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                            {{ $registrant->qr_checkin_url }}
                                        </a>
                                        @if ($registrant->checked_in_at)
                                            <p class="text-xs text-emerald-600 font-semibold mt-2">
                                                ✓ Checked in at {{ $registrant->checked_in_at->format('H:i, d M Y') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Actions --}}
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
                </div>
            </div>

            {{-- Quick stats card --}}
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Unique Code</p>
                    <p class="text-sm font-bold text-gray-900 font-mono">{{ $registrant->unique_code ?? '—' }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Registered</p>
                    <p class="text-sm font-bold text-gray-900">{{ $registrant->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Processed</p>
                    <p class="text-sm font-bold text-gray-900">{{ $registrant->processed_at?->format('d M Y, H:i') ?? '—' }}</p>
                    @if ($registrant->status === 'approved' && $registrant->approver)
                        <p class="text-xs text-gray-500 mt-1">by {{ $registrant->approver->name }}</p>
                    @elseif ($registrant->status === 'rejected' && $registrant->rejecter)
                        <p class="text-xs text-gray-500 mt-1">by {{ $registrant->rejecter->name }}</p>
                    @endif
                </div>
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
</script>

</body>
</html>
