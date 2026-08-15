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
        showToast('✅ ' + data.published + ' dari ' + data.total + ' badge dikirim & ditandai check-in', 'success');
        // Update the checked-in cells immediately (no refresh)
        if (Array.isArray(data.ids)) data.ids.forEach(id => updateCheckinCell(id, data.checked_in_at));
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
</script>
</body>
</html>
