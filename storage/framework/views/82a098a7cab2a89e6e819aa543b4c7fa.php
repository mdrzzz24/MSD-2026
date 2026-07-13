<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — <?php echo e(config('app.name')); ?></title>
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
            background: linear-gradient(135deg, rgba(5,13,42,.4), rgba(10,26,74,.3), rgba(5,13,42,.5));
            z-index: 1;
        }
        .login-hero::after {
            display: none;
        }
        @keyframes gridPulse {
            0%,100% { opacity: .3; }
            50% { opacity: .6; }
        }
        .dot-live {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: #00d4ff;
            animation: pulseLive 1.5s ease-in-out infinite;
            display: inline-block;
        }
        @keyframes pulseLive {
            0%,100% { opacity: 1; box-shadow: 0 0 6px #00d4ff; }
            50% { opacity: .3; box-shadow: 0 0 0 #00d4ff; }
        }
        .login-content { position: relative; z-index: 3; }
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

            
            <div class="lg:hidden relative mb-8 overflow-hidden rounded-2xl" style="background: url('<?php echo e(asset('img/QRHeader.png')); ?>') no-repeat center center; background-size: cover; min-height: 200px;">
                
            </div>

            
            <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/60 p-8 sm:p-10 border border-gray-100">
                <h2 class="text-2xl font-bold text-gray-900 mb-1">Welcome Back</h2>
                <p class="text-gray-500 text-sm mb-8">Already have an account? Sign in below, or <a href="<?php echo e(route('home1', request()->only(['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content']))); ?>#register" style="color:#e91e63;" class="font-semibold hover:underline">register here</a>.</p>
                <div class="text-center mb-6">
                    <a href="<?php echo e(route('qr-login.form')); ?>" class="text-sm font-medium hover:underline inline-flex items-center gap-1.5" style="color:#e91e63;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                        Sign in with QR Code
                    </a>
                </div>

                
                <?php if($errors->any()): ?>
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-start gap-3 text-sm">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span><?php echo e($errors->first()); ?></span>
                    </div>
                <?php endif; ?>

                <?php echo $__env->make('admin.partials.notification', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                <form action="<?php echo e(route('login.attempt')); ?>" method="POST" class="space-y-5">
                    <?php echo csrf_field(); ?>

                    
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input type="email" id="email" name="email" value="<?php echo e(old('email')); ?>" required autofocus
                                   placeholder="email@example.com"
                                   class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition">
                        </div>
                    </div>

                    
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input type="password" id="password" name="password" required placeholder="••••••••"
                                   class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition">
                        </div>
                    </div>

                    
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember"
                               class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <label for="remember" class="ml-2.5 text-sm text-gray-600">Remember me</label>
                    </div>

                    <button type="submit"
                            class="w-full py-3 font-bold text-sm tracking-wide"
                            style="background:linear-gradient(135deg,#ff3d6e,#e91e63);color:#fff;border-radius:999px;border:none;cursor:pointer;box-shadow:0 8px 24px rgba(233,30,99,0.35);transition:transform 0.25s,box-shadow 0.25s;"
                            onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 12px 30px rgba(233,30,99,0.5)'"
                            onmouseout="this.style.transform='';this.style.boxShadow='0 8px 24px rgba(233,30,99,0.35)'">
                        Sign In
                    </button>
                </form>
            </div>

            <p class="text-center text-xs text-gray-400 mt-6">
                
            </p>
            <p class="text-center text-xs text-gray-400 mt-2">
                &copy; <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?>. All rights reserved.
            </p>
        </div>
    </div>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\2026-Testing\resources\views/auth/login.blade.php ENDPATH**/ ?>