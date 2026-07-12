<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Scan QR — {{ $agendum->title }} — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
</head>
<body class="bg-gray-50 font-sans antialiased">
<div class="flex min-h-screen">
@include('admin.partials.sidebar')
<main class="flex-1 lg:ml-64">
<header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.agenda.index') }}" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Agenda
            </a>
            <span class="text-gray-300">/</span>
            <h1 class="text-lg font-bold text-gray-900">Scan QR — {{ $agendum->title }}</h1>
        </div>
        <a href="{{ route('admin.agenda.visitors', $agendum) }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg border border-gray-200 text-gray-600 bg-white hover:bg-gray-50 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            View Check-ins
        </a>
    </div>
</header>

<div class="p-4 sm:p-6 lg:p-8">
    @include('admin.partials.notification')

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- QR Scanner Input --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-base font-bold text-gray-900 mb-4">Scan QR Code</h2>

            {{-- Tab toggle --}}
            <div class="flex gap-2 mb-5">
                <button id="tabManual" onclick="switchTab('manual')" class="flex-1 py-2 text-xs font-semibold rounded-xl transition bg-indigo-600 text-white">Manual</button>
                <button id="tabCamera" onclick="switchTab('camera')" class="flex-1 py-2 text-xs font-semibold rounded-xl transition bg-gray-100 text-gray-600 hover:bg-gray-200">Camera</button>
            </div>

            {{-- Manual Input --}}
            <div id="panelManual" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Enter QR Token</label>
                    <div class="flex gap-2">
                        <input type="text" id="qrInput" placeholder="Paste or type QR token..." autofocus
                            class="flex-1 px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition font-mono">
                        <button onclick="processScan()" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition shadow-sm">Scan</button>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Type or paste the QR token, then press Enter or click Scan.</p>
                </div>
                <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
                    <p class="text-sm font-medium text-indigo-800 mb-2">Need a registrant's QR code?</p>
                    <a href="{{ route('admin.registrants.index') }}" target="_blank" class="text-xs text-indigo-600 hover:text-indigo-800 underline">Browse registrants →</a>
                    <span class="text-xs text-gray-400 mx-2">or</span>
                    <a href="{{ route('admin.management.qr') }}" target="_blank" class="text-xs text-indigo-600 hover:text-indigo-800 underline">View all QR codes →</a>
                </div>
            </div>

            {{-- Camera Scanner --}}
            <div id="panelCamera" class="space-y-4" style="display:none;">
                <div id="qrScanner" style="width:100%;max-width:400px;margin:0 auto;"></div>
                <p class="text-xs text-gray-400 text-center">Point your camera at the registrant's QR code badge.</p>
                <button onclick="stopCamera()" class="w-full py-2 text-xs font-semibold rounded-xl bg-red-50 text-red-700 hover:bg-red-100 transition">Stop Camera</button>
            </div>
        </div>

        {{-- Result Panel --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6" id="resultPanel">
            <h2 class="text-base font-bold text-gray-900 mb-4">Result</h2>
            <div id="resultContent" class="text-center py-12 text-gray-400">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                <p class="text-sm">Scan a QR code to see result here.</p>
            </div>
        </div>
    </div>
</div>
</main>
</div>

<script>
const scanUrl = '{{ route('admin.agenda.scan-process', $agendum) }}';
const csrfToken = '{{ csrf_token() }}';
let html5QrCode = null;
let cameraRunning = false;

function switchTab(tab) {
    document.getElementById('panelManual').style.display = tab === 'manual' ? '' : 'none';
    document.getElementById('panelCamera').style.display = tab === 'camera' ? '' : 'none';
    document.getElementById('tabManual').className = 'flex-1 py-2 text-xs font-semibold rounded-xl transition ' + (tab === 'manual' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200');
    document.getElementById('tabCamera').className = 'flex-1 py-2 text-xs font-semibold rounded-xl transition ' + (tab === 'camera' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200');

    if (tab === 'camera') {
        startCamera();
    } else {
        stopCamera();
        document.getElementById('qrInput').focus();
    }
}

function startCamera() {
    if (cameraRunning) return;
    if (typeof Html5Qrcode === 'undefined') {
        showResult('error', 'QR scanner library not loaded. Please refresh.');
        return;
    }
    html5QrCode = new Html5Qrcode('qrScanner');
    cameraRunning = true;

    html5QrCode.start(
        { facingMode: 'environment' },
        { fps: 10, qrbox: { width: 250, height: 250 } },
        onScanSuccess,
        onScanFailure
    ).catch(() => {
        cameraRunning = false;
        showResult('error', 'Camera access denied. Use Manual input instead.');
        switchTab('manual');
    });
}

function stopCamera() {
    if (html5QrCode && cameraRunning) {
        html5QrCode.stop().then(() => { html5QrCode.clear(); cameraRunning = false; }).catch(() => {});
    }
}

function onScanSuccess(decodedText) {
    stopCamera();
    document.getElementById('qrInput').value = decodedText;
    processScan();
    switchTab('manual');
    document.getElementById('tabCamera').className = 'flex-1 py-2 text-xs font-semibold rounded-xl transition bg-gray-100 text-gray-600 hover:bg-gray-200';
    document.getElementById('tabManual').className = 'flex-1 py-2 text-xs font-semibold rounded-xl transition bg-indigo-600 text-white';
    document.getElementById('panelCamera').style.display = 'none';
    document.getElementById('panelManual').style.display = '';
}

function onScanFailure(err) {}

document.getElementById('qrInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') { e.preventDefault(); processScan(); }
});

function processScan() {
    const input = document.getElementById('qrInput');
    let token = input.value.trim();
    if (!token) { showResult('error', 'Please enter a QR token.'); input.focus(); return; }

    if (token.startsWith('http')) {
        token = token.split('/').pop().split('?')[0];
    }

    showResult('loading', 'Scanning...');

    fetch(scanUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: JSON.stringify({ qr_token: token }),
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showResult(data.already_visited ? 'warning' : 'success', data.message, data.registrant);
            input.value = '';
        } else {
            showResult('error', data.message || 'Invalid QR code.');
        }
        input.focus();
    })
    .catch(() => { showResult('error', 'Network error.'); input.focus(); });
}

function showResult(type, message, registrant) {
    const panel = document.getElementById('resultContent');
    let html = '';

    if (type === 'loading') {
        html = '<div class="py-12 text-center"><svg class="w-10 h-10 mx-auto mb-3 text-indigo-500 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><p class="text-sm text-gray-500">Scanning...</p></div>';
    } else if (type === 'success') {
        html = '<div class="text-center py-6">' +
            '<div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-100 mb-4"><svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></div>' +
            '<p class="text-sm font-semibold text-emerald-700 mb-1">Check-in Recorded!</p>' +
            '<p class="text-sm text-gray-600 mb-4">' + message + '</p>';
        if (registrant) {
            html += '<div class="bg-gray-50 rounded-xl p-4 text-left text-sm"><div class="space-y-2">' +
                '<div><span class="text-gray-400">Name:</span> <span class="font-semibold text-gray-900">' + registrant.name + '</span></div>' +
                (registrant.email ? '<div><span class="text-gray-400">Email:</span> <span class="text-gray-700">' + registrant.email + '</span></div>' : '') +
                (registrant.company ? '<div><span class="text-gray-400">Company:</span> <span class="text-gray-700">' + registrant.company + '</span></div>' : '') +
                (registrant.job_title ? '<div><span class="text-gray-400">Job Title:</span> <span class="text-gray-700">' + registrant.job_title + '</span></div>' : '') +
            '</div></div>';
        }
        html += '</div>';
    } else if (type === 'warning') {
        html = '<div class="text-center py-6">' +
            '<div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-amber-100 mb-4"><svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg></div>' +
            '<p class="text-sm font-semibold text-amber-700 mb-1">Already Checked In</p>' +
            '<p class="text-sm text-gray-600 mb-4">' + message + '</p>';
        if (registrant) {
            html += '<div class="bg-gray-50 rounded-xl p-4 text-left text-sm"><span class="text-gray-400">Checked in at:</span> <span class="font-semibold text-gray-700">' + (registrant.visited_at || '-') + '</span></div>';
        }
        html += '</div>';
    } else {
        html = '<div class="text-center py-6">' +
            '<div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-100 mb-4"><svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></div>' +
            '<p class="text-sm font-semibold text-red-700 mb-1">Error</p>' +
            '<p class="text-sm text-gray-600">' + message + '</p></div>';
    }
    panel.innerHTML = html;
}
</script>
</body>
</html>
