<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrants: {{ $workshop->title }} — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
    <style>.truncate-cell{max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}</style>
</head>
<body class="bg-gray-50 font-sans antialiased">
<div class="flex min-h-screen">
@include('admin.partials.sidebar')
<main class="flex-1 lg:ml-64">
<header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.workshops.index') }}" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Workshops
            </a>
            <span class="text-gray-300">/</span>
            <h1 class="text-lg font-bold text-gray-900 truncate">{{ $workshop->title }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.workshops.registrants.export-csv', $workshop) }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg border border-gray-200 text-gray-600 bg-white hover:bg-gray-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export CSV
            </a>
        </div>
    </div>
</header>

<div class="p-4 sm:p-6 lg:p-8">
    @include('admin.partials.notification')

    {{-- Workshop Summary Card --}}
    @php $linkedAgenda = $workshop->agendaItems->first(); @endphp
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-5 gap-4">
            <div><p class="text-xs text-gray-400 uppercase tracking-wider">Time</p><p class="text-sm font-semibold text-gray-900">{{ $workshop->timeRange() !== '—' ? $workshop->timeRange() : ($linkedAgenda ? $linkedAgenda->timeLabel() : '—') }}</p></div>
            <div><p class="text-xs text-gray-400 uppercase tracking-wider">Room</p><p class="text-sm font-semibold text-gray-900">{{ $workshop->room ?? $linkedAgenda?->room ?? '—' }}</p></div>
            <div><p class="text-xs text-gray-400 uppercase tracking-wider">Capacity</p><p class="text-sm font-semibold text-gray-900">{{ $workshop->capacity > 0 ? $workshop->capacity : 'Unlimited' }}</p></div>
            <div><p class="text-xs text-gray-400 uppercase tracking-wider">Approved</p><p class="text-sm font-bold text-indigo-600">{{ $registrants->where('pivot.status', 'approved')->count() }}</p></div>
            <div><p class="text-xs text-gray-400 uppercase tracking-wider">Status</p>
                @if ($workshop->registration_open)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700">Open</span>
                @else
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Closed</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Registrants Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-2">
            <div><h2 class="text-base font-bold text-gray-900">Registrant List</h2><p class="text-xs text-gray-500">Total: <strong>{{ $registrants->count() }}</strong> registrant(s)</p></div>
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-400">App: <strong class="text-emerald-600">{{ $registrants->where('pivot.status', 'approved')->count() }}</strong></span>
                <span class="text-xs text-gray-400">Pend: <strong class="text-amber-600">{{ $registrants->where('pivot.status', 'pending')->count() }}</strong></span>
                <span class="text-xs text-gray-400">Rej: <strong class="text-red-600">{{ $registrants->where('pivot.status', 'rejected')->count() }}</strong></span>
            </div>
        </div>

        @if ($registrants->isEmpty())
            <div class="px-5 py-12 text-center text-gray-400 text-sm">No registrants yet for this workshop.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead><tr class="bg-gray-50/80">
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Phone</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Company</th>
                        <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase hidden xl:table-cell">Job Title</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">WS Status</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Reg Status</th>
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Check-in</th>
                        @if (Auth::user()->canWrite())
                        <th class="px-4 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Action</th>
                        @endif
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($registrants as $i => $r)
                            @php $wsStatus = $r->pivot->status ?? 'pending'; @endphp
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-4 py-3.5"><span class="text-sm text-gray-400">{{ $i + 1 }}</span></td>
                                <td class="px-4 py-3.5"><a href="{{ route('admin.registrants.show', $r) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 hover:underline">{{ $r->display_name }}</a></td>
                                <td class="px-4 py-3.5"><span class="text-sm text-gray-600 truncate-cell block">{{ $r->email }}</span></td>
                                <td class="px-4 py-3.5 hidden md:table-cell"><span class="text-sm text-gray-600">{{ $r->phone ?? '—' }}</span></td>
                                <td class="px-4 py-3.5 hidden lg:table-cell"><span class="text-sm text-gray-600 truncate-cell block">{{ $r->company ?? '—' }}</span></td>
                                <td class="px-4 py-3.5 hidden xl:table-cell"><span class="text-sm text-gray-600">{{ $r->job_title ?? '—' }}</span></td>
                                <td class="px-4 py-3.5 text-center">
                                    @if ($wsStatus === 'approved')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Approved</span>
                                    @elseif ($wsStatus === 'rejected')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200" title="{{ $r->pivot->admin_notes ?? '' }}"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Rejected</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Pending</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    @if ($r->status === 'approved')<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Approved</span>
                                    @elseif ($r->status === 'rejected')<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Rejected</span>
                                    @else<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Pending</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    @if ($r->checked_in_at)
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600" title="{{ $r->checked_in_at->format('d M Y H:i') }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>{{ $r->checked_in_at->format('H:i') }}
                                        </span>
                                    @else<span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>
                                @if (Auth::user()->canWrite())
                                <td class="px-4 py-3.5 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        @if ($wsStatus === 'pending')
                                            <form action="{{ route('admin.workshops.registrants.approve', [$workshop, $r->id]) }}" method="POST" class="inline">@csrf<button class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition" title="Approve">✓</button></form>
                                            <button onclick="showRejectModal({{ $r->id }},'{{ e($r->display_name) }}')" class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition" title="Reject">✕</button>
                                        @elseif ($wsStatus === 'approved')
                                            @if (Auth::user()->isSuperAdmin())
                                            <button onclick="showRejectModal({{ $r->id }},'{{ e($r->display_name) }}')" class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition" title="Reject">✕</button>
                                            @endif
                                        @elseif ($wsStatus === 'rejected')
                                            <form action="{{ route('admin.workshops.registrants.approve', [$workshop, $r->id]) }}" method="POST" class="inline">@csrf<button class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition" title="Approve">✓</button></form>
                                        @endif
                                    </div>
                                </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;background:rgba(0,0,0,0.4);backdrop-filter:blur(4px);padding:16px;">
  <div style="background:#fff;border-radius:16px;box-shadow:0 25px 50px rgba(0,0,0,0.25);width:100%;max-width:448px;overflow:hidden;">
    <form id="rejectForm" method="POST">
      @csrf
      <div style="padding:24px;">
        <h3 style="font-size:18px;font-weight:700;color:#111827;margin-bottom:4px;">Reject Registration</h3>
        <p style="font-size:14px;color:#6b7280;margin-bottom:16px;">Reject <strong id="rejectName"></strong>'s workshop registration?</p>
        <label style="display:block;font-size:14px;font-weight:600;color:#374151;margin-bottom:4px;">Reason <span style="color:#ef4444;">*</span></label>
        <textarea name="admin_notes" required rows="3" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:12px;font-size:14px;resize:vertical;" placeholder="Reason for rejection..."></textarea>
        <div style="display:flex;gap:8px;margin-top:16px;">
          <button type="button" onclick="closeRejectModal()" style="flex:1;padding:10px 0;background:#f3f4f6;color:#374151;font-weight:600;font-size:14px;border:none;border-radius:12px;cursor:pointer;">Cancel</button>
          <button type="submit" style="flex:1;padding:10px 0;background:#ef4444;color:#fff;font-weight:600;font-size:14px;border:none;border-radius:12px;cursor:pointer;">Reject</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
function showRejectModal(registrantId, name) {
    document.getElementById('rejectName').textContent = name;
    document.getElementById('rejectForm').action = '{{ route('admin.workshops.registrants.reject', [$workshop, '__ID__']) }}'.replace('__ID__', registrantId);
    document.getElementById('rejectModal').style.display = 'flex';
}
function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}
</script>

</main>
</div>
</body>
</html>
