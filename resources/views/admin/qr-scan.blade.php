<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QR Scan — {{ config('app.name') }}</title>
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
</head>
<body class="bg-gray-50 font-sans antialiased min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        @include('admin.partials.notification')

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-lg font-bold">
                    {{ strtoupper(substr($registrant->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">{{ $registrant->name }}</h2>
                    <p class="text-xs text-gray-500">{{ $registrant->email }}</p>
                </div>
                <div class="ml-auto">
                    @if ($registrant->checked_in_at)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Checked In
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Not Checked In
                        </span>
                    @endif
                </div>
            </div>

            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400">Company</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $registrant->company ?? '—' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400">Job Title</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $registrant->job_title ?? '—' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400">Status</p>
                        <p class="text-sm font-semibold capitalize text-gray-900">{{ $registrant->status }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400">Unique Code</p>
                        <p class="text-sm font-semibold text-gray-900 font-mono">{{ $registrant->unique_code ?? '—' }}</p>
                    </div>
                </div>

                @if ($registrant->checked_in_at)
                    <div class="bg-emerald-50 rounded-xl p-4 text-center">
                        <p class="text-sm font-semibold text-emerald-700">✓ Checked in at {{ $registrant->checked_in_at->format('H:i, d M Y') }}</p>
                    </div>
                @elseif ($registrant->isApproved())
                    <form action="{{ route('registrant.qr-checkin', $registrant->qr_token) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition text-sm">
                            ✓ Confirm Check-In
                        </button>
                    </form>
                @else
                    <div class="bg-red-50 rounded-xl p-4 text-center">
                        <p class="text-sm font-semibold text-red-600">Registrant has not been approved yet.</p>
                    </div>
                @endif
            </div>
        </div>

        <p class="text-center text-xs text-gray-400 mt-4">MSD 2026 — Registration System</p>
    </div>
</body>
</html>
