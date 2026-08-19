<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Feedback — {{ config('app.name') }}</title>
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
    <style>
        .table-fixed td, .table-fixed th { min-width: 0; }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">

<div class="flex min-h-screen">
    @include('admin.partials.sidebar')

    <main class="flex-1 lg:ml-64">
        <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200">
            <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-4">
                    <button id="sidebarToggle" class="lg:hidden p-2 -ml-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">Feedback</h1>
                        <p class="text-xs text-gray-500">Registrants who submitted session feedback</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="openQrScan()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                        Scan QR
                    </button>
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

            {{-- Summary --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex flex-wrap items-center gap-x-8 gap-y-3">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $view === 'session' ? 'Sessions / Tracks with Feedback' : 'Registrants with Feedback' }}</span>
                    <span class="text-2xl font-bold text-gray-900">{{ $view === 'session' ? $totalSessions : $totalWithFeedback }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Shown</span>
                    <span class="text-2xl font-bold text-emerald-600">{{ ($view === 'session' ? $sessions : $registrants)->total() }}</span>
                </div>
                <p class="text-xs text-gray-400 ml-auto">
                    @if ($view === 'session')
                        Grouped by session / track — shows which registrants filled feedback for each one.
                    @else
                        Grouped by registrant — shows which sessions each registrant filled feedback for.
                    @endif
                </p>
            </div>

            {{-- View toggle + Search --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="inline-flex rounded-xl bg-gray-100 p-1 flex-shrink-0">
                    <a href="{{ route('admin.feedback-registrants.index', array_merge(request()->except(['view', 'page']), ['view' => 'session'])) }}"
                       class="px-4 py-1.5 text-xs font-semibold rounded-lg transition {{ $view === 'session' ? 'bg-white text-indigo-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        By Session / Track
                    </a>
                    <a href="{{ route('admin.feedback-registrants.index', array_merge(request()->except(['view', 'page']), ['view' => 'registrant'])) }}"
                       class="px-4 py-1.5 text-xs font-semibold rounded-lg transition {{ $view === 'registrant' ? 'bg-white text-indigo-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        By Registrant
                    </a>
                </div>
                <div class="relative flex-1 sm:max-w-md">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="search" id="tableSearch"
                           placeholder="{{ $view === 'session' ? 'Search session, company, room...' : 'Search name, company, email, phone...' }}"
                           value="{{ request('search') }}"
                           class="pl-9 pr-10 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white w-full transition">
                    <a href="javascript:void(0)" id="clearSearchBtn" onclick="clearSearch()" style="display:none;"
                       class="absolute right-2 top-1/2 -translate-y-1/2 p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-200 transition" title="Clear search">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Table --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full table-fixed" id="feedbackTable">
                        <thead>
                            <tr class="bg-gray-50/80">
                                @if ($view === 'session')
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-64">Session / Track</th>
                                    <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">Responses</th>
                                    <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Registrants</th>
                                    <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell w-40">Last Feedback</th>
                                    <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-28">Action</th>
                                @else
                                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-56">Registrant</th>
                                    <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell w-40">Company</th>
                                    <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-48">Sessions</th>
                                    <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell w-40">Last Feedback</th>
                                    <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50" id="feedbackTableBody">
                            @if ($view === 'session')
                                @include('admin.feedback-registrants._session_rows', ['sessions' => $sessions])
                            @else
                                @include('admin.feedback-registrants._rows', ['registrants' => $registrants])
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3" id="feedbackPagination">
                    <p class="text-xs text-gray-500">Showing <span id="feedbackCount">({{ ($view === 'session' ? $sessions : $registrants)->total() }})</span> {{ $view === 'session' ? 'sessions' : 'registrants' }}</p>
                    <div>{{ ($view === 'session' ? $sessions : $registrants)->links() }}</div>
                </div>
            </div>
        </div>
    </main>
</div>

{{-- QR Scan Modal --}}
<div id="qrScanModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeQrScan()"></div>
    <div class="relative flex items-center justify-center min-h-full p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Scan Registrant QR</h3>
                    <p class="text-xs text-gray-500">Point the camera at the registrant's badge QR code</p>
                </div>
                <button onclick="closeQrScan()" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-5">
                <div id="qrReader" class="w-full aspect-square bg-gray-900 rounded-xl overflow-hidden"></div>
                <p id="qrScanStatus" class="text-center text-xs text-gray-500 mt-3">Waiting for camera…</p>
                <input type="text" id="qrManualCode" placeholder="Or type the registrant's unique code…"
                       class="mt-3 w-full px-3 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition">
                <button onclick="manualLookup()"
                        class="mt-2 w-full py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition">
                    Look Up Code
                </button>
            </div>
        </div>
    </div>
</div>

{{-- QR Result Modal --}}
<div id="qrResultModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeQrResult()"></div>
    <div class="relative flex items-center justify-center min-h-full p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Feedback Details</h3>
                    <p class="text-xs text-gray-500" id="qrResultSubtitle">Registrant & sessions</p>
                </div>
                <button onclick="closeQrResult()" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto" id="qrResultBody"></div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    // ---- Live search (AJAX, no reload) ----
    let searchTimer = null;
    let searchSeq = 0;
    const currentView = @json($view);
    function liveSearch(input) {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(async () => {
            const seq = ++searchSeq;
            const params = new URLSearchParams(window.location.search);
            if (!params.has('view')) params.set('view', currentView);
            params.set('search', input.value);
            params.set('page', '1');
            try {
                const res = await fetch('{{ route("admin.feedback-registrants.search") }}?' + params.toString(), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await res.json();
                if (seq !== searchSeq) return;
                const tbody = document.getElementById('feedbackTableBody');
                if (tbody && data.rows) tbody.innerHTML = data.rows;
                const pag = document.getElementById('feedbackPagination');
                if (pag) pag.innerHTML = data.pagination ? data.pagination + '<p class="text-xs text-gray-500 mt-2">Showing (' + (data.total || 0) + ') ' + (currentView === 'session' ? 'sessions' : 'registrants') + '</p>' : '';
                const count = document.getElementById('feedbackCount');
                if (count) count.textContent = '(' + (data.total || 0) + ')';
            } catch (e) { /* ignore */ }
            input.focus();
            input.setSelectionRange(input.value.length, input.value.length);
        }, 300);
    }
    function clearSearch() {
        const input = document.getElementById('tableSearch');
        const btn = document.getElementById('clearSearchBtn');
        if (input) { input.value = ''; if (btn) btn.style.display = 'none'; liveSearch(input); }
    }
    document.getElementById('tableSearch')?.addEventListener('input', function() {
        const btn = document.getElementById('clearSearchBtn');
        if (btn) btn.style.display = this.value ? 'block' : 'none';
        liveSearch(this);
    });
    (function() {
        const input = document.getElementById('tableSearch');
        const btn = document.getElementById('clearSearchBtn');
        if (input && btn && input.value) btn.style.display = 'block';
    })();

    // ---- Session expand/collapse ----
    // Works for both the initial render and AJAX-re-rendered rows (delegated via
    // the tbody element, which survives innerHTML swaps).
    function toggleSessionDetail(event, rowId) {
        // Ignore clicks that landed on an interactive element (links).
        if (event.target.closest('a')) return;
        const detailRow = document.getElementById(rowId);
        const mainRow = event.currentTarget;
        if (!detailRow || !mainRow) return;
        const isOpen = mainRow.getAttribute('data-expanded') === '1';
        detailRow.classList.toggle('hidden', isOpen);
        mainRow.setAttribute('data-expanded', isOpen ? '0' : '1');
        const chevron = mainRow.querySelector('.session-chevron');
        if (chevron) {
            chevron.style.transform = isOpen ? '' : 'rotate(90deg)';
            chevron.style.color = isOpen ? '' : '#4f46e5';
        }
    }
    // Re-bind after AJAX re-renders (delegation on the tbody handles this — the
    // row onclick calls the global toggleSessionDetail directly).

    // ---- QR scanning ----
    let qrReader = null;
    let qrScanning = false;

    function openQrScan() {
        document.getElementById('qrScanModal').classList.remove('hidden');
        document.getElementById('qrScanModal').classList.add('flex');
        document.getElementById('qrScanStatus').textContent = 'Waiting for camera…';
        document.getElementById('qrManualCode').value = '';
        startScanner();
    }
    function closeQrScan() {
        stopScanner();
        document.getElementById('qrScanModal').classList.add('hidden');
        document.getElementById('qrScanModal').classList.remove('flex');
    }
    function startScanner() {
        if (qrScanning) return;
        if (typeof Html5Qrcode === 'undefined') {
            document.getElementById('qrScanStatus').textContent = 'QR scanner unavailable. Please try again.';
            return;
        }
        qrReader = new Html5Qrcode('qrReader');
        qrScanning = true;
        qrReader.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: { width: 220, height: 220 }, aspectRatio: 1.0 },
            (decodedText) => {
                stopScanner();
                lookupCode(decodedText);
            },
            () => {}
        ).catch(() => {
            qrScanning = false;
            document.getElementById('qrScanStatus').textContent = 'Camera access denied or unavailable.';
        });
    }
    function stopScanner() {
        if (qrReader) {
            try {
                qrReader.stop().then(() => {
                    try { qrReader.clear(); } catch (e) {}
                }).catch(() => {});
            } catch (e) { /* scanner not running yet — ignore */ }
        }
        qrReader = null;
        qrScanning = false;
    }
    function manualLookup() {
        const code = document.getElementById('qrManualCode').value.trim();
        if (!code) return;
        stopScanner();
        lookupCode(code);
    }
    async function lookupCode(code) {
        document.getElementById('qrScanStatus').textContent = 'Looking up…';
        try {
            const res = await fetch('{{ route("admin.feedback-registrants.qr-lookup") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ code })
            });
            const data = await res.json();
            if (data.success) {
                document.getElementById('qrScanStatus').textContent = 'Found!';
                closeQrScan();
                renderQrResult(data);
            } else {
                document.getElementById('qrScanStatus').textContent = data.message || 'Registrant not found.';
                setTimeout(() => { document.getElementById('qrScanStatus').textContent = 'Ready to scan…'; startScanner(); }, 1500);
            }
        } catch (e) {
            document.getElementById('qrScanStatus').textContent = 'Connection error. Please try again.';
            setTimeout(() => startScanner(), 1500);
        }
    }

    function renderQrResult(data) {
        const r = data.registrant;
        const initial = (r.name || '?').trim().charAt(0).toUpperCase();
        let html = '';

        // Registrant card
        html += '<div class="flex items-start gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100">';
        html += '<div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-lg font-bold flex-shrink-0">' + initial + '</div>';
        html += '<div class="min-w-0 flex-1">';
        html += '<p class="text-base font-bold text-gray-900 truncate">' + escapeHtml(r.name) + '</p>';
        html += '<p class="text-xs text-gray-500 truncate">' + escapeHtml(r.email || '') + '</p>';
        html += '<div class="flex flex-wrap gap-1.5 mt-2">';
        if (r.phone) html += '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-600">📞 ' + escapeHtml(r.phone) + '</span>';
        if (r.company) html += '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-600">🏢 ' + escapeHtml(r.company) + '</span>';
        if (r.job_title) html += '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-600">💼 ' + escapeHtml(r.job_title) + '</span>';
        html += '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium capitalize ' + (r.status === 'approved' ? 'bg-emerald-50 text-emerald-700' : (r.status === 'pending' ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700')) + '">' + escapeHtml(r.status) + '</span>';
        html += '</div></div></div>';

        html += '<div class="mt-5 flex items-center justify-between">';
        html += '<h4 class="text-sm font-bold text-gray-800">Sessions with Feedback (' + data.total + ')</h4>';
        html += '<a href="{{ route("admin.feedback-registrants.show", 0) }}'.replace('/0', '/' + r.id) + '" class="inline-flex items-center gap-1 text-xs font-medium text-indigo-600 hover:text-indigo-800">View full page <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>';
        html += '</div>';

        if (data.sessions.length === 0) {
            html += '<p class="text-sm text-gray-500 mt-3">No feedback submitted.</p>';
        } else {
            html += '<div class="mt-3 space-y-3">';
            data.sessions.forEach(s => {
                const typeColor = s.type === 'Workshop' ? 'bg-fuchsia-50 text-fuchsia-700 border-fuchsia-200' : (s.type === 'Track' ? 'bg-sky-50 text-sky-700 border-sky-200' : 'bg-gray-100 text-gray-600 border-gray-200');
                const displayTitle = s.company ? s.company + ' - ' + s.title : s.title;
                html += '<div class="border border-gray-100 rounded-xl p-4">';
                html += '<div class="flex items-start justify-between gap-3 flex-wrap">';
                html += '<div class="min-w-0 flex-1">';
                html += '<div class="flex items-center gap-2 flex-wrap">';
                html += '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium border ' + typeColor + '">' + s.type + '</span>';
                html += '<span class="text-xs text-gray-400">' + escapeHtml(s.time) + '</span>';
                html += '<span class="text-xs text-gray-400">' + escapeHtml(s.room) + '</span>';
                html += '</div>';
                html += '<p class="text-sm font-semibold text-gray-900 mt-1">' + escapeHtml(displayTitle) + '</p>';
                html += '<p class="text-[11px] text-gray-400 mt-0.5">Submitted ' + escapeHtml(s.submitted_at) + '</p>';
                html += '</div>';
                html += '<span class="text-xs font-semibold text-indigo-600 flex-shrink-0">' + s.answers.length + ' answer' + (s.answers.length === 1 ? '' : 's') + '</span>';
                html += '</div>';
                // Answers
                if (s.answers.length > 0) {
                    html += '<div class="mt-3 space-y-1.5 bg-gray-50 rounded-xl p-3">';
                    s.answers.forEach(a => {
                        html += '<div class="text-sm"><span class="text-xs font-semibold text-gray-500">' + escapeHtml(a.question) + ':</span>';
                        if (a.type === 'rating') {
                            const val = parseInt(a.value, 10) || 0;
                            let stars = '';
                            for (let i = 1; i <= 5; i++) {
                                stars += '<svg class="w-3.5 h-3.5 inline-block ' + (i <= val ? 'text-yellow-400' : 'text-gray-200') + '" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
                            }
                            html += '<span class="ml-1">' + stars + '</span>';
                        } else if (a.type === 'yes_no') {
                            const ok = String(a.value).toLowerCase() === 'yes';
                            html += '<span class="ml-1 font-medium ' + (ok ? 'text-emerald-600' : 'text-red-500') + '">' + escapeHtml(a.value) + '</span>';
                        } else if (a.type === 'multi_choice') {
                            let list = a.value;
                            try { const arr = JSON.parse(a.value); if (Array.isArray(arr)) list = arr.join(', '); } catch (e) {}
                            html += '<span class="text-gray-700 ml-1">' + escapeHtml(list) + '</span>';
                        } else {
                            html += '<span class="text-gray-700 ml-1">' + escapeHtml(a.value) + '</span>';
                        }
                        html += '</div>';
                    });
                    html += '</div>';
                }
                html += '</div>';
            });
            html += '</div>';
        }

        document.getElementById('qrResultBody').innerHTML = html;
        document.getElementById('qrResultModal').classList.remove('hidden');
        document.getElementById('qrResultModal').classList.add('flex');
    }
    function closeQrResult() {
        document.getElementById('qrResultModal').classList.add('hidden');
        document.getElementById('qrResultModal').classList.remove('flex');
    }

    function escapeHtml(str) {
        return String(str ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    // Esc closes modals
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeQrResult();
            closeQrScan();
        }
    });

    // Sidebar toggle (mobile)
    document.getElementById('sidebarToggle')?.addEventListener('click', function() {
        const overlay = document.getElementById('sidebarOverlay');
        const sidebar = document.getElementById('mobileSidebar');
        if (sidebar) sidebar.classList.toggle('-translate-x-full');
        if (overlay) overlay.classList.toggle('hidden');
    });
</script>
</body>
</html>
