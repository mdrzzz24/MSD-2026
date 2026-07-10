<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Send Reminder — {{ config('app.name') }}</title>
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
            <div class="flex items-center h-16 px-4 sm:px-6 lg:px-8 gap-4">
                <a href="{{ route('admin.templates.index') }}" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Templates
                </a>
                <span class="text-gray-300">/</span>
                <h1 class="text-lg font-bold text-gray-900">Send Gentle Reminder</h1>
            </div>
        </header>

        <div class="p-4 sm:p-6 lg:p-8">
            @if (session('success'))
                <div class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl mb-6">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm">{!! session('success') !!}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 px-5 py-4 rounded-2xl mb-6">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-sm">{!! session('error') !!}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Form --}}
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                Send Reminder Email
                            </h2>
                        </div>
                        <form method="POST" action="{{ route('admin.templates.send-reminder') }}" class="p-6 space-y-5">
                            @csrf
                            <div>
                                <p class="text-sm text-gray-600 mb-2">
                                    Using template: <strong class="text-violet-700">{{ $template->name }}</strong> — Subject: <em>"{{ $template->subject }}"</em>
                                </p>
                                <p class="text-xs text-gray-400">
                                    Select specific registrants below, or leave empty to send to <strong>ALL approved registrants</strong>.
                                </p>
                            </div>

                            <div class="max-h-80 overflow-y-auto border border-gray-200 rounded-xl divide-y divide-gray-100">
                                @foreach ($registrants as $r)
                                    <label class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 cursor-pointer transition">
                                        <input type="checkbox" name="registrant_ids[]" value="{{ $r->id }}" class="w-4 h-4 rounded border-gray-300 text-violet-600 focus:ring-violet-500">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">{{ $r->display_name }}</p>
                                            <p class="text-xs text-gray-400 truncate">{{ $r->email }}</p>
                                        </div>
                                        <span class="text-xs text-gray-400">{{ $r->created_at->format('d M Y') }}</span>
                                    </label>
                                @endforeach
                            </div>

                            <div class="flex items-center justify-between pt-2">
                                <p class="text-xs text-gray-400" id="selectedCount">0 selected</p>
                                <button type="submit"
                                        onclick="return confirm('Send reminder to selected registrants? Make sure the template is correct.')"
                                        class="px-6 py-2.5 text-sm font-semibold rounded-xl bg-violet-500 text-white hover:bg-violet-600 shadow-sm shadow-violet-200 transition">
                                    🚀 Send Reminder
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Info --}}
                <div class="lg:col-span-1 space-y-4">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-bold text-gray-800">📊 Stats</h3>
                        </div>
                        <div class="p-5 space-y-3">
                            <div>
                                <p class="text-xs text-gray-400 uppercase tracking-wider">Approved Registrants</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $registrants->count() }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 uppercase tracking-wider">Template</p>
                                <p class="text-sm font-medium text-violet-700">{{ $template->name }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-amber-50 rounded-2xl border border-amber-200 p-5">
                        <h3 class="text-sm font-bold text-amber-800 mb-2">⚠️ Perhatian</h3>
                        <p class="text-xs text-amber-700">Email akan dikirim ke <strong>semua approved registrants</strong> jika tidak ada yang dipilih. Pastikan template reminder sudah benar sebelum mengirim.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    document.querySelectorAll('input[name="registrant_ids[]"]').forEach(cb => {
        cb.addEventListener('change', () => {
            const count = document.querySelectorAll('input[name="registrant_ids[]"]:checked').length;
            const total = document.querySelectorAll('input[name="registrant_ids[]"]').length;
            document.getElementById('selectedCount').textContent =
                count > 0 ? count + ' selected' : (total + ' total — will send to ALL if none selected');
        });
    });
</script>

</body>
</html>
