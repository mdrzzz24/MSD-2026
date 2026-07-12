<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QR Login — <?php echo e(config('app.name')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <style>
        .animate-fade-in { animation: fadeIn 0.6s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .login-hero {
            background: url('<?php echo e(asset('img/background.png')); ?>') no-repeat center center;
            background-size: cover;
            position: relative;
        }
        .login-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(5,13,42,.5), rgba(10,26,74,.35), rgba(5,13,42,.6));
            z-index: 1;
        }
        .login-content { position: relative; z-index: 3; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen flex font-sans">

    
    <div class="hidden lg:flex lg:w-5/12 xl:w-1/2 login-hero relative overflow-hidden items-center justify-center">
        <div class="relative z-10 text-center px-12 animate-fade-in login-content">
            <div class="mb-6">
                <img src="<?php echo e(asset('img/logo-msd.png')); ?>" alt="MSD" style="height:clamp(50px,8vw,90px);width:auto;filter:drop-shadow(0 0 20px rgba(0,212,255,.3))" class="mx-auto">
            </div>
            <h1 class="text-3xl xl:text-4xl font-extrabold leading-tight tracking-tight mb-3" style="background:linear-gradient(135deg,#fff 50%,#00d4ff 100%);-webkit-background-clip:text;background-clip:text;color:transparent;">
                Winning with AI
            </h1>
            <p class="text-[rgba(255,255,255,.7)] text-base max-w-sm mx-auto leading-relaxed">
                Build, Run, and Scale for Measurable Impact
            </p>
        </div>
    </div>

    
    <div class="flex-1 flex items-center justify-center bg-gray-50 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md animate-fade-in">

            
            <div class="lg:hidden relative mb-8 overflow-hidden rounded-2xl" style="background: url('<?php echo e(asset('img/QRHeader.png')); ?>') no-repeat center center; background-size: cover; min-height: 180px;">
            </div>

            
            <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/60 p-8 sm:p-10 border border-gray-100">
                <div class="text-center mb-6">
                    <img src="<?php echo e(asset('img/logo-msd.png')); ?>" alt="MSD" style="height:40px;width:auto;" class="mx-auto mb-3">
                    <h2 class="text-xl font-bold text-gray-900">QR Code Login</h2>
                    <p class="text-sm text-gray-500 mt-1" id="pageSubtitle">Sign in by scanning your QR code</p>
                </div>

                
                <div id="alertContainer"></div>

                
                <div id="stepEmail">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                    <div class="flex gap-2">
                        <input type="email" id="emailInput" value="<?php echo e(old('email')); ?>"
                               placeholder="your@email.com" autocomplete="email" autofocus
                               class="flex-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 focus:bg-white transition">
                        <button id="verifyBtn" onclick="verifyEmail()"
                                class="px-5 py-3 text-white text-sm font-bold transition disabled:opacity-50 disabled:cursor-not-allowed"
                                style="background:linear-gradient(135deg,#ff3d6e,#e91e63);border-radius:999px;border:none;cursor:pointer;box-shadow:0 8px 24px rgba(233,30,99,0.35);transition:transform 0.25s,box-shadow 0.25s;"
                                onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 12px 30px rgba(233,30,99,0.5)'"
                                onmouseout="this.style.transform='';this.style.boxShadow='0 8px 24px rgba(233,30,99,0.35)'">
                            Next
                        </button>
                    </div>
                    <p class="text-xs text-gray-400 mt-3 text-center">
                        <a href="<?php echo e(route('login')); ?>" style="color:#e91e63;" class="hover:underline">Sign in with password</a>
                        &middot;
                        <a href="<?php echo e(route('home1')); ?>#register" style="color:#e91e63;" class="hover:underline">Register</a>
                    </p>
                </div>

                
                <div id="stepScanner" class="hidden">
                    <div class="bg-pink-50 rounded-xl border border-pink-100 p-3 mb-4 flex items-center gap-3">
                        <div id="avatarInitial" class="w-9 h-9 rounded-full flex items-center justify-center text-white font-bold text-sm" style="background:linear-gradient(135deg,#ff3d6e,#e91e63);"></div>
                        <div class="flex-1 min-w-0">
                            <p id="registrantName" class="text-sm font-semibold text-gray-900 truncate"></p>
                            <p id="registrantEmail" class="text-xs text-gray-500 truncate"></p>
                        </div>
                        <button onclick="resetPage()" class="text-gray-400 hover:text-gray-600 transition flex-shrink-0" title="Change email">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </button>
                    </div>

                    <div class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden">
                        <div id="qr-reader" class="w-full"></div>
                    </div>

                    <p class="text-xs text-gray-400 text-center mt-3">
                        Point your camera at the QR code to log in automatically
                    </p>
                </div>
            </div>

            <p class="text-center text-xs text-gray-400 mt-6">
                &copy; <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?>. All rights reserved.
            </p>
        </div>
    </div>

    
    <div id="loadingOverlay" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-xl p-8 text-center">
            <svg class="w-10 h-10 mx-auto mb-4 animate-spin" style="color:#e91e63;" fill="none" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="50" stroke-dashoffset="20" stroke-linecap="round"/>
            <p class="text-gray-900 text-sm font-medium" id="loadingText">Verifying...</p>
        </div>
    </div>

<script>
let html5QrReader = null;

function showAlert(type, text) {
    const container = document.getElementById('alertContainer');
    container.innerHTML = `<div class="flex items-start gap-3 border rounded-xl px-4 py-3 mb-4 text-sm ${type === 'error' ? 'bg-red-50 border-red-200 text-red-700' : 'bg-emerald-50 border-emerald-200 text-emerald-700'}"><span>${text}</span></div>`;
    setTimeout(() => { if (container.innerHTML.includes(text)) container.innerHTML = ''; }, 5000);
}

function showLoading(text) {
    document.getElementById('loadingText').textContent = text;
    document.getElementById('loadingOverlay').classList.remove('hidden');
}

function hideLoading() {
    document.getElementById('loadingOverlay').classList.add('hidden');
}

async function verifyEmail() {
    const email = document.getElementById('emailInput').value.trim();
    if (!email) return showAlert('error', 'Please enter your email address.');

    const btn = document.getElementById('verifyBtn');
    btn.disabled = true;
    btn.textContent = '...';

    try {
        const res = await fetch('<?php echo e(route('qr-login.verify-email')); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Accept': 'application/json' },
            body: JSON.stringify({ email })
        });
        const data = await res.json();

        if (data.success) {
            document.getElementById('stepEmail').classList.add('hidden');
            document.getElementById('registrantName').textContent = data.name;
            document.getElementById('registrantEmail').textContent = email;
            document.getElementById('avatarInitial').textContent = data.initial;
            document.getElementById('pageSubtitle').textContent = 'Scan your QR code to sign in';
            document.getElementById('stepScanner').classList.remove('hidden');
            startScanner();
        }
    } catch (err) {
        try {
            const data = await err.response?.json ? err.response.json() : JSON.parse(err.responseText);
            showAlert('error', data.message || 'Verification failed.');
        } catch {
            showAlert('error', 'Connection error. Please try again.');
        }
    }

    btn.disabled = false;
    btn.textContent = 'Next';
}

function resetPage() {
    if (html5QrReader) { html5QrReader.stop().catch(() => {}); html5QrReader = null; }
    document.getElementById('stepScanner').classList.add('hidden');
    document.getElementById('stepEmail').classList.remove('hidden');
    document.getElementById('pageSubtitle').textContent = 'Sign in by scanning your QR code';
    document.getElementById('alertContainer').innerHTML = '';
    document.getElementById('emailInput').value = '';
    document.getElementById('emailInput').focus();
}

function startScanner() {
    const reader = new Html5Qrcode('qr-reader');
    html5QrReader = reader;

    reader.start(
        { facingMode: 'environment' },
        { fps: 10, qrbox: { width: 250, height: 250 }, aspectRatio: 1.0 },
        (decodedText) => {
            reader.stop().catch(() => {});
            showLoading('Verifying QR code...');
            authenticate(decodedText);
        },
        () => {}
    ).catch(() => {
        document.getElementById('qr-reader').innerHTML = `
            <div class="flex items-center justify-center h-64 p-6">
                <div class="text-center">
                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                    <p class="text-gray-500 text-sm font-medium">Camera access denied</p>
                    <p class="text-gray-400 text-xs mt-1">Please allow camera access and refresh the page.</p>
                </div>
            </div>
        `;
    });
}

async function authenticate(code) {
    try {
        const res = await fetch('<?php echo e(route('qr-login.authenticate')); ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Accept': 'application/json' },
            body: JSON.stringify({ scanned_code: code })
        });
        const data = await res.json();

        if (data.success) {
            showLoading('Login successful! Redirecting...');
            setTimeout(() => { window.location.href = data.redirect; }, 800);
        } else {
            hideLoading();
            showAlert('error', data.message || 'Invalid QR code.');
            startScanner();
        }
    } catch (err) {
        hideLoading();
        showAlert('error', 'Verification failed. Please try again.');
        startScanner();
    }
}

document.getElementById('emailInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') verifyEmail();
});
</script>

</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/auth/qr-login.blade.php ENDPATH**/ ?>