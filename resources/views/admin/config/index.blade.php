<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>App Config & QR — {{ config('app.name') }}</title>
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
            <h1 class="text-lg font-bold text-gray-900">Event Checker — App Config & QR</h1>
        </div>
    </div>
</header>

<div class="p-4 sm:p-6 lg:p-8">
    @include('admin.partials.notification')

    <div class="grid lg:grid-cols-2 gap-6">

        {{-- ═══ Left: config builder ═══ --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-base font-bold text-gray-900 mb-1">App Configuration</h2>
            <p class="text-xs text-gray-500 mb-5">Set the values for your environment. The QR on the right updates automatically. Scan it from the app: Settings → Import Configuration → Scan QR.</p>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Base URL (endpoint API)</label>
                    <input id="cfg-base-url" type="text" value="{{ $host }}/2026/api"
                           class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    <p class="text-[11px] text-gray-400 mt-1">Emulator: <code class="text-indigo-500">http://10.0.2.2:8000/2026/api</code> · Physical device (adb reverse): <code class="text-indigo-500">http://127.0.0.1:8080/2026/api</code></p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">JSON (compact, for QR)</label>
                    <textarea id="cfg-json" rows="4" readonly
                              class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-xs font-mono text-gray-700 focus:outline-none"></textarea>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button onclick="copyJson()" class="px-3 py-2 text-xs font-medium rounded-lg bg-indigo-500 text-white hover:bg-indigo-600 transition">📋 Copy JSON</button>
                    <button onclick="downloadQr()" class="px-3 py-2 text-xs font-medium rounded-lg border border-gray-200 text-gray-600 bg-white hover:bg-gray-50 transition">⬇️ Download QR</button>
                </div>
            </div>
        </div>

        {{-- ═══ Right: QR code ═══ --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col items-center justify-center">
            <h2 class="text-base font-bold text-gray-900 mb-1">QR Code</h2>
            <p class="text-xs text-gray-500 mb-4">Scan to import the configuration into the app</p>
            <div class="bg-white rounded-2xl border-2 border-dashed border-gray-200 p-4">
                <img id="cfg-qr" src="" alt="QR Code" class="w-56 h-56 mx-auto" crossorigin="anonymous">
            </div>
            <p class="text-[11px] text-gray-400 mt-3 text-center max-w-xs">The QR contains the JSON config. Make sure the QR generator produces <strong>plain text</strong>, not a redirect URL.</p>
        </div>

    </div>
</div>
</main>
</div>

<script>
// ── Build compact JSON from form fields ──
function buildConfig() {
    var baseUrl = document.getElementById('cfg-base-url').value.trim();

    return JSON.stringify({
        base_url: baseUrl,
        request_format: 'json',
        event_id: '',
        app_name: 'Event Checker'
    }); // compact, no spaces
}

// ── Update JSON textarea + QR image ──
function updatePreview() {
    var json = buildConfig();
    document.getElementById('cfg-json').value = json;

    // Use qrserver API to render QR from the raw JSON text.
    var qr = document.getElementById('cfg-qr');
    qr.src = 'https://api.qrserver.com/v1/create-qr-code/?size=560x560&data=' + encodeURIComponent(json);
}

// ── Copy JSON to clipboard ──
function copyJson() {
    var json = buildConfig();
    if (navigator.clipboard) {
        navigator.clipboard.writeText(json).then(function () { alert('JSON copied!'); });
    } else {
        var t = document.getElementById('cfg-json');
        t.select(); t.setSelectionRange(0, 99999);
        document.execCommand('copy'); alert('JSON copied!');
    }
}

// ── Download QR as PNG ──
function downloadQr() {
    var qr = document.getElementById('cfg-qr');
    var link = document.createElement('a');
    link.href = qr.src;
    link.download = 'event-checker-config-qr.png';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Live update on input
document.getElementById('cfg-base-url').addEventListener('input', updatePreview);

updatePreview();
</script>
</body>
</html>
