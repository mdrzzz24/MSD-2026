<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
                    colors: {
                        navy: { DEFAULT: '#0a1a4a', 2: '#0e2461' },
                        deep: '#050d2a',
                        pink: { DEFAULT: '#ff3d6e', 2: '#e91e63' },
                    }
                }
            }
        }
    </script>
    <style>
        body { background: #050d2a; }
        .card-msd { background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.12); backdrop-filter: blur(8px); }
        .card-msd:hover { background: rgba(255,255,255,.1); }
        .table-msd thead th { color: rgba(255,255,255,.5); font-weight: 600; text-transform: uppercase; font-size: 11px; letter-spacing: .05em; padding: 14px 20px; border-bottom: 1px solid rgba(255,255,255,.08); }
        .table-msd tbody td { padding: 14px 20px; border-bottom: 1px solid rgba(255,255,255,.05); color: rgba(255,255,255,.7); font-size: 13px; }
        .table-msd tbody tr:hover { background: rgba(255,255,255,.03); }
        .table-msd tbody tr:last-child td { border-bottom: none; }
        .badge-approved { background: rgba(16,185,129,.15); color: #10b981; border: 1px solid rgba(16,185,129,.25); }
        .badge-rejected { background: rgba(239,68,68,.15); color: #ef4444; border: 1px solid rgba(239,68,68,.25); }
        .badge-pending { background: rgba(245,158,11,.15); color: #f59e0b; border: 1px solid rgba(245,158,11,.25); }
        .badge-workshop { background: rgba(245,158,11,.15); color: #fbbf24; border: 1px solid rgba(245,158,11,.25); }
        .badge-track { background: rgba(129,140,248,.15); color: #818cf8; border: 1px solid rgba(129,140,248,.25); }
        .badge-session { background: rgba(148,163,184,.15); color: #94a3b8; border: 1px solid rgba(148,163,184,.25); }
        .btn-pink { background: linear-gradient(135deg, #ff3d6e, #e91e63); color: #fff; border-radius: 999px; box-shadow: 0 8px 24px rgba(233,30,99,.35); transition: all .25s; }
        .btn-pink:hover { transform: translateY(-2px); box-shadow: 0 12px 30px rgba(233,30,99,.5); }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: rgba(255,255,255,.05); }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); border-radius: 3px; }
        .glow-pink { box-shadow: 0 0 40px rgba(255,61,110,.08); }
    </style>
</head>
<body class="font-sans antialiased text-white">

<div class="flex min-h-screen">

    {{-- ═══════════ SIDEBAR ═══════════ --}}
    <aside class="hidden lg:flex lg:flex-col w-64 fixed inset-y-0 z-40" style="background:rgba(10,26,74,.85); border-right:1px solid rgba(255,255,255,.08); backdrop-filter:blur(12px);">
        <div class="flex items-center gap-3 h-16 px-6" style="border-bottom:1px solid rgba(255,255,255,.08);">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#ff3d6e,#e91e63);">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <span class="text-lg font-bold text-white tracking-tight">MSD26</span>
        </div>
        <nav class="flex-1 px-3 py-6 space-y-1">
            <a href="{{ route('registrant.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium" style="background:rgba(255,61,110,.15); color:#ff3d6e;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/></svg>
                Dashboard
            </a>
            <div class="pt-6">
                <form action="{{ route('registrant.logout') }}" method="POST">
                    @csrf
                    <button class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium w-full transition" style="color:rgba(255,255,255,.5);" onmouseover="this.style.background='rgba(239,68,68,.1)';this.style.color='#ef4444'" onmouseout="this.style.background='';this.style.color='rgba(255,255,255,.5)'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2h5a2 2 0 012 2v1"/></svg>
                        Logout
                    </button>
                </form>
            </div>
        </nav>
        <div class="p-4" style="border-top:1px solid rgba(255,255,255,.08);">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0" style="background:linear-gradient(135deg,#ff3d6e,#e91e63);">{{ strtoupper(substr($registrant->display_name, 0, 1)) }}</div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white truncate">{{ $registrant->display_name }}</p>
                    <p class="text-xs truncate" style="color:rgba(255,255,255,.4);">{{ $registrant->email }}</p>
                </div>
            </div>
        </div>
    </aside>

    {{-- ═══════════ MAIN CONTENT ═══════════ --}}
    <main class="flex-1 lg:ml-64">

        <header class="sticky top-0 z-30" style="background:rgba(5,13,42,.85); backdrop-filter:blur(12px); border-bottom:1px solid rgba(255,255,255,.08);">
            <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                <div>
                    <h1 class="text-lg font-bold text-white">Dashboard</h1>
                    <p class="text-xs" style="color:rgba(255,255,255,.4);">Workshop & Sesi Anda</p>
                </div>
                <span class="text-xs" style="color:rgba(255,255,255,.3);">{{ $registrant->email }}</span>
            </div>
        </header>

        <div class="p-4 sm:p-6 lg:p-8 space-y-6" style="max-width:1280px;">

            @include('admin.partials.notification')

            {{-- ═══ MY SESSIONS ═══ --}}
            <div class="card-msd rounded-2xl overflow-hidden glow-pink">
                <div class="px-5 py-4 flex items-center justify-between" style="border-bottom:1px solid rgba(255,255,255,.08);">
                    <div>
                        <h2 class="text-base font-bold text-white">My Sessions</h2>
                        <p class="text-xs" style="color:rgba(255,255,255,.4);">Tracks & workshops you registered for via agenda</p>
                    </div>
                    <a href="{{ route('home1') }}" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold btn-pink">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/></svg>
                        Back to Home
                    </a>
                </div>
                @if ($myAgendaItems->isEmpty())
                    <div class="px-5 py-16 text-center" style="color:rgba(255,255,255,.3);">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <p class="text-sm">No sessions registered yet.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full table-msd">
                            <thead>
                                <tr>
                                    <th class="text-left">Session</th>
                                    <th class="text-left">Type</th>
                                    <th class="text-left">Time</th>
                                    <th class="text-left hidden sm:table-cell">Room</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($myAgendaItems as $item)
                                    @php $agStatus = $item->pivot->status ?? 'pending'; @endphp
                                    <tr>
                                        <td><p class="font-semibold text-white">{{ $item->title }}</p></td>
                                        <td>
                                            @php
                                                $type = $item->agenda_type
                                                    ?: ($item->category === 'workshop' ? 'workshop' : null)
                                                    ?: ($item->track_id ? 'track' : null)
                                                    ?: ($item->workshop_id ? 'workshop' : null)
                                                    ?: 'session';
                                            @endphp
                                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $type==='workshop'?'badge-workshop':($type==='track'?'badge-track':'badge-session') }}">{{ ucfirst($type) }}</span>
                                        </td>
                                        <td><span>{{ $item->timeLabel() }}</span></td>
                                        <td class="hidden sm:table-cell"><span>{{ $item->room ?? '—' }}</span></td>
                                        <td class="text-center">
                                            @if ($agStatus === 'approved')
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold badge-approved"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Approved</span>
                                            @elseif ($agStatus === 'rejected')
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold badge-rejected"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Rejected</span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold badge-pending"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Pending</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="flex items-center justify-center gap-1.5">
                                                @if ($agStatus === 'approved' && $item->feedback_enabled)
                                                    <a href="{{ route('feedback.form', $item) }}"
                                                       class="px-3 py-1.5 text-xs font-medium rounded-lg transition"
                                                       style="background:rgba(16,185,129,.15); color:#10b981; border:1px solid rgba(16,185,129,.25);"
                                                       onmouseover="this.style.background='rgba(16,185,129,.25)'"
                                                       onmouseout="this.style.background='rgba(16,185,129,.15)'">
                                                        Feedback
                                                    </a>
                                                @endif
                                                @if ($agStatus !== 'approved')
                                                <form action="{{ route('registrant.agenda.unregister', $item) }}" method="POST" onsubmit="return confirm('Cancel registration for {{ $item->title }}?')">
                                                    @csrf
                                                    <button class="px-3 py-1.5 text-xs font-medium rounded-lg transition" style="background:rgba(239,68,68,.15); color:#ef4444; border:1px solid rgba(239,68,68,.25);" onmouseover="this.style.background='rgba(239,68,68,.25)'" onmouseout="this.style.background='rgba(239,68,68,.15)'">Cancel</button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- ═══ AVAILABLE WORKSHOPS ═══ --}}
            <div class="card-msd rounded-2xl overflow-hidden glow-pink">
                <div class="px-5 py-4" style="border-bottom:1px solid rgba(255,255,255,.08);">
                    <h2 class="text-base font-bold text-white">Available Workshops</h2>
                    <p class="text-xs" style="color:rgba(255,255,255,.4);">Workshops you can join</p>
                </div>
                @if ($availableWorkshops->isEmpty())
                    <div class="px-5 py-16 text-center" style="color:rgba(255,255,255,.3);">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <p class="text-sm">No workshops available at this time.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full table-msd">
                            <thead>
                                <tr>
                                    <th class="text-left">Workshop</th>
                                    <th class="text-left hidden sm:table-cell">Room</th>
                                    <th class="text-left">Date</th>
                                    <th class="text-left">Time</th>
                                    <th class="text-left hidden lg:table-cell">Capacity</th>
                                    <th class="text-center">Register</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($availableWorkshops as $w)
                                    <tr>
                                        <td><p class="font-semibold text-white">{{ $w->title }}</p></td>
                                        <td class="hidden sm:table-cell"><span>{{ $w->room ?? '—' }}</span></td>
                                        <td><span>{{ $w->date ? $w->date->format('d M Y') : '—' }}</span></td>
                                        <td><span>{{ $w->timeRange() }}</span></td>
                                        <td class="hidden lg:table-cell">
                                            @if ($w->capacity > 0)
                                                <span class="{{ $w->isFull() ? 'text-red-400' : '' }}">{{ $w->registrationsCount() }}/{{ $w->capacity }}</span>
                                            @else
                                                <span style="color:rgba(255,255,255,.3);">∞</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($w->isFull())
                                                <span class="text-xs font-medium" style="color:#ef4444;">Penuh</span>
                                            @else
                                                <form action="{{ route('registrant.workshop.register', $w) }}" method="POST">
                                                    @csrf
                                                    <button class="px-4 py-1.5 text-xs font-semibold btn-pink">Daftar</button>
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
