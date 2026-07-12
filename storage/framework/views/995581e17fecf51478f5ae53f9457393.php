<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Scan QR Code — <?php echo e(config('app.name')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
</head>
<body class="bg-gray-900 font-sans antialiased min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        
        <div class="text-center mb-6">
            <div class="w-16 h-16 rounded-2xl bg-indigo-500/20 flex items-center justify-center mx-auto mb-3">
                <svg class="w-8 h-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-white">Scan Your QR Code</h1>
            <p class="text-sm text-gray-400 mt-1">Point your camera at the QR code to log in</p>
        </div>

        
        <div class="bg-white/10 backdrop-blur rounded-2xl border border-white/10 p-4 mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-indigo-500/30 flex items-center justify-center text-white font-bold text-sm">
                    <?php echo e(strtoupper(substr($registrant->display_name ?? $registrant->name, 0, 1))); ?>

                </div>
                <div>
                    <p class="text-sm font-semibold text-white"><?php echo e($registrant->display_name ?: $registrant->name); ?></p>
                    <p class="text-xs text-gray-400"><?php echo e($email); ?></p>
                </div>
            </div>
        </div>

        
        <div id="messageContainer"></div>

        
        <div class="bg-gray-800 rounded-2xl border border-gray-700 overflow-hidden">
            <div id="qr-reader" class="w-full"></div>
            <div id="qr-reader-results" class="hidden"></div>

            
            <div class="p-4 border-t border-gray-700">
                <p class="text-xs text-gray-500 text-center mb-3">Camera not working? Enter the code manually:</p>
                <form id="manualForm" class="flex gap-2">
                    <?php echo csrf_field(); ?>
                    <input type="text" id="manualCode" placeholder="Enter unique code"
                           class="flex-1 px-3 py-2 bg-gray-700 border border-gray-600 rounded-xl text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition">
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition">
                        Verify
                    </button>
                </form>
            </div>
        </div>

        
        <div class="text-center mt-6">
            <a href="<?php echo e(route('qr-login.form')); ?>"
               class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to email entry
            </a>
        </div>
    </div>

    <script>
    // Show message
    function showMessage(type, text) {
        const container = document.getElementById('messageContainer');
        const bg = type === 'error' ? 'bg-red-500/10 border-red-500/30 text-red-400' : 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400';
        container.innerHTML = `
            <div class="flex items-start gap-3 border rounded-xl px-4 py-3 mb-4 text-sm ${bg}">
                <span>${text}</span>
            </div>
        `;
    }

    // Submit scanned code
    function submitCode(code) {
        fetch('<?php echo e(route('qr-login.authenticate')); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || '<?php echo e(csrf_token()); ?>',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ scanned_code: code })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showMessage('success', '✅ QR code verified! Redirecting...');
                setTimeout(() => { window.location.href = data.redirect; }, 800);
            } else {
                showMessage('error', '❌ ' + data.message);
            }
        })
        .catch(err => {
            showMessage('error', '❌ Connection error. Please try again.');
        });
    }

    // Initialize QR scanner
    function startScanner() {
        const reader = new Html5Qrcode('qr-reader');

        reader.start(
            { facingMode: 'environment' },
            {
                fps: 10,
                qrbox: { width: 250, height: 250 },
                aspectRatio: 1.0,
            },
            (decodedText) => {
                // Stop scanning once we get a result
                reader.stop().catch(() => {});
                document.getElementById('qr-reader').innerHTML = `
                    <div class="flex items-center justify-center h-64">
                        <div class="text-center">
                            <svg class="w-10 h-10 text-emerald-400 mx-auto mb-2 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-emerald-400 text-sm font-medium">Code detected! Verifying...</p>
                        </div>
                    </div>
                `;
                submitCode(decodedText);
            },
            () => { /* ignore scan failures */ }
        ).catch(err => {
            document.getElementById('qr-reader').innerHTML = `
                <div class="flex items-center justify-center h-64 p-6">
                    <div class="text-center">
                        <svg class="w-10 h-10 text-gray-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                        <p class="text-gray-400 text-sm font-medium">Camera access denied</p>
                        <p class="text-gray-500 text-xs mt-1">Please allow camera access or enter the code manually below.</p>
                    </div>
                </div>
            `;
        });
    }

    // Manual form submission
    document.getElementById('manualForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const code = document.getElementById('manualCode').value.trim();
        if (code) {
            submitCode(code);
        }
    });

    // Start scanner on page load
    document.addEventListener('DOMContentLoaded', startScanner);
    </script>

</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/auth/qr-scan.blade.php ENDPATH**/ ?>