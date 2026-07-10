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
        .animate-float { animation: float 6s ease-in-out infinite; }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
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
            background: linear-gradient(135deg, rgba(5,13,42,.85), rgba(10,26,74,.65), rgba(5,13,42,.9));
            z-index: 1;
        }
        .login-hero::after {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(0,212,255,.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,212,255,.04) 1px, transparent 1px);
            background-size: 60px 60px;
            z-index: 1;
            animation: gridPulse 8s ease-in-out infinite;
        }
        @keyframes gridPulse {
            0%,100% { opacity: .3; }
            50% { opacity: .6; }
        }
        .ai-particle {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            z-index: 2;
        }
        .ai-particle:nth-child(1) {
            top: 15%; left: 8%;
            width: 6px; height: 6px;
            background: #00d4ff;
            box-shadow: 0 0 12px #00d4ff;
            animation: floatAI 6s ease-in-out infinite;
        }
        .ai-particle:nth-child(2) {
            top: 25%; right: 12%;
            width: 4px; height: 4px;
            background: #a855f7;
            box-shadow: 0 0 10px #a855f7;
            animation: floatAI 8s ease-in-out infinite 1s;
        }
        .ai-particle:nth-child(3) {
            bottom: 30%; left: 15%;
            width: 8px; height: 8px;
            background: #00d4ff;
            box-shadow: 0 0 16px #00d4ff;
            animation: floatAI 7s ease-in-out infinite 2s;
        }
        .ai-particle:nth-child(4) {
            bottom: 20%; right: 20%;
            width: 5px; height: 5px;
            background: #a855f7;
            box-shadow: 0 0 12px #a855f7;
            animation: floatAI 9s ease-in-out infinite 0.5s;
        }
        @keyframes floatAI {
            0%,100% { transform: translateY(0) scale(1); opacity: .6; }
            50% { transform: translateY(-30px) scale(1.5); opacity: 1; }
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
        <div class="ai-particle"></div>
        <div class="ai-particle"></div>
        <div class="ai-particle"></div>
        <div class="ai-particle"></div>

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

            
            <div class="lg:hidden text-center mb-8">
                <div class="w-16 h-16 mx-auto mb-3 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl flex items-center justify-center p-3">
                    <img src="<?php echo e(asset('img/logo-msd.svg')); ?>" alt="MSD" class="w-full h-full brightness-0 invert">
                </div>
                <h1 class="text-xl font-bold text-gray-900">MSD 2026</h1>
                <p class="text-xs text-gray-500 mt-1">Winning with AI</p>
            </div>

            
            <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/60 p-8 sm:p-10 border border-gray-100">
                <h2 class="text-2xl font-bold text-gray-900 mb-1">Welcome Back</h2>
                <p class="text-gray-500 text-sm mb-8">Already have an account? Sign in below, or <a href="<?php echo e(route('home1', request()->only(['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content']))); ?>#register" class="text-indigo-600 hover:text-indigo-800 font-semibold hover:underline">register here</a>.</p>

                
                <?php if($errors->any()): ?>
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-start gap-3 text-sm">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span><?php echo e($errors->first()); ?></span>
                    </div>
                <?php endif; ?>

                <?php if(session('error')): ?>
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-start gap-3 text-sm">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span><?php echo e(session('error')); ?></span>
                    </div>
                <?php endif; ?>

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
                            class="w-full py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 transition-all duration-200 text-sm tracking-wide">
                        Sign In
                    </button>
                </form>
            </div>

            <p class="text-center text-xs text-gray-400 mt-6">
                Don&apos;t have an account? <a href="<?php echo e(route('home1', request()->only(['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content']))); ?>#register" class="text-indigo-600 hover:text-indigo-800 font-semibold hover:underline">Register here</a>
            </p>
            <p class="text-center text-xs text-gray-400 mt-2">
                &copy; <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?>. All rights reserved.
            </p>
        </div>
    </div>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\2026-Testing\resources\views/auth/login.blade.php ENDPATH**/ ?>