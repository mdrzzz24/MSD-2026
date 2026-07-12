<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Set Up Password — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
</head>
<body class="min-h-screen flex font-sans">

    {{-- LEFT: Brand Panel --}}
    <div class="hidden lg:flex lg:w-5/12 xl:w-1/2" style="background: url('{{ asset('img/background.png') }}') no-repeat center center;background-size:cover;position:relative;">
        <div style="position:absolute;inset:0;background:linear-gradient(135deg,rgba(5,13,42,.5),rgba(10,26,74,.35),rgba(5,13,42,.6));z-index:1;"></div>
        <div class="relative z-10 flex flex-col items-center justify-center text-center px-12 w-full">
            <img src="{{ asset('img/logo-msd.png') }}" alt="MSD" style="height:clamp(50px,8vw,90px);width:auto;filter:drop-shadow(0 0 20px rgba(0,212,255,.3))" class="mx-auto mb-6">
            <h1 style="font-size:clamp(24px,3.5vw,40px);font-weight:800;background:linear-gradient(135deg,#fff 50%,#00d4ff 100%);-webkit-background-clip:text;background-clip:text;color:transparent;line-height:1.2;">
                Set Up Your Password
            </h1>
            <p style="color:rgba(255,255,255,.7);font-size:15px;max-width:360px;margin-top:12px;line-height:1.6;">
                Create a secure password to access the MSD 2026 event portal.
            </p>
        </div>
    </div>

    {{-- RIGHT: Form --}}
    <div class="flex-1 flex items-center justify-center bg-gray-50 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">

            {{-- Mobile header --}}
            <div class="lg:hidden text-center mb-8">
                <div class="w-16 h-16 mx-auto mb-3 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-2xl flex items-center justify-center p-3">
                    <img src="{{ asset('img/logo-msd.svg') }}" alt="MSD" class="w-full h-full brightness-0 invert">
                </div>
                <h1 class="text-xl font-bold text-gray-900">Set Up Password</h1>
                <p class="text-xs text-gray-500 mt-1">Create your account password</p>
            </div>

            {{-- Card --}}
            <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/60 p-8 sm:p-10 border border-gray-100">
                @include('admin.partials.notification')

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-start gap-3 text-sm">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                @if (isset($user) && $user)
                    <div class="text-center mb-6">
                        <div class="w-14 h-14 rounded-full bg-pink-50 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <p class="text-sm text-gray-500">Welcome, <strong class="text-gray-900">{{ $user->name }}</strong></p>
                        <p class="text-xs text-gray-400">{{ $user->email }}</p>
                    </div>

                    <form action="{{ route('client.setup-password', ['token' => $token]) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Create Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                                <input type="password" name="password" required minlength="6" placeholder="Min. 6 characters"
                                       class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 focus:bg-white transition">
                            </div>
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Confirm Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </div>
                                <input type="password" name="password_confirmation" required minlength="6" placeholder="Re-enter password"
                                       class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 focus:bg-white transition">
                            </div>
                        </div>
                        <button type="submit"
                                class="w-full py-3 font-bold text-sm tracking-wide"
                                style="background:linear-gradient(135deg,#ff3d6e,#e91e63);color:#fff;border-radius:999px;border:none;cursor:pointer;box-shadow:0 8px 24px rgba(233,30,99,0.35);transition:transform 0.25s,box-shadow 0.25s;"
                                onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 12px 30px rgba(233,30,99,0.5)'"
                                onmouseout="this.style.transform='';this.style.boxShadow='0 8px 24px rgba(233,30,99,0.35)'">
                            Save Password & Login
                        </button>
                    </form>
                @else
                    <div class="text-center py-8">
                        <div class="w-14 h-14 rounded-xl bg-red-50 flex items-center justify-center mx-auto mb-4">
                            <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 mb-2">Invalid or Expired Link</h2>
                        <p class="text-sm text-gray-500">This invitation link is invalid or has expired. Please contact your administrator.</p>
                    </div>
                @endif
            </div>

            <p class="text-center text-xs text-gray-400 mt-6">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
        </div>
    </div>

</body>
</html>
