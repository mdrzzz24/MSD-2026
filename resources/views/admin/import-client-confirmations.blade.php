<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Import Client Confirmations — {{ config('app.name') }}</title>
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
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Dashboard
                </a>
                <span class="text-gray-300">/</span>
                <h1 class="text-lg font-bold text-gray-900">Import Client Confirmations</h1>
            </div>
        </header>
        <div class="p-4 sm:p-6 lg:p-8 space-y-6">
            @include('admin.partials.notification')

            @if ($pending && is_array($pending) && !empty($pending['to_apply']))
                {{-- ═══════════ PREVIEW STATE ═══════════ --}}
                @php
                    $toApply = $pending['to_apply'];
                    $counts = ['approve' => 0, 'reject' => 0, 'waitlist' => 0];
                    foreach ($toApply as $row) { $counts[$row['action']]++; }
                    $sample = array_slice($toApply, 0, 25);
                @endphp

                <div class="bg-white rounded-2xl border border-indigo-100 p-5 sm:p-6 shadow-sm">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Preview — {{ $pending['file_name'] ?? 'file' }}
                            </h2>
                            <p class="text-sm text-gray-500 mt-1">
                                Akan dicatat sebagai rekomendasi client atas nama
                                <strong class="text-gray-700">{{ $pending['client_name'] ?? 'Client' }}</strong>.
                                Status asli registrant <strong class="text-gray-700">tidak diubah</strong>.
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('admin.import-client-confirmations.cancel') }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition">
                                    ✕ Batal
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.import-client-confirmations.apply') }}"
                                  onsubmit="return confirm('Yakin import {{ count($toApply) }} konfirmasi client ini?')">
                                @csrf
                                <input type="hidden" name="client_id" value="{{ $pending['client_id'] ?? '' }}">
                                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition shadow-sm shadow-indigo-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Konfirmasi Import ({{ count($toApply) }})
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Summary cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-white rounded-2xl p-5 border border-emerald-100 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Approve</span>
                            <div class="w-9 h-9 bg-emerald-100 rounded-xl flex items-center justify-center">✅</div>
                        </div>
                        <p class="text-3xl font-bold text-emerald-600">{{ $counts['approve'] }}</p>
                        <p class="text-xs text-emerald-500 mt-1">APPROVED</p>
                    </div>
                    <div class="bg-white rounded-2xl p-5 border border-red-100 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-red-500 uppercase tracking-wider">Reject</span>
                            <div class="w-9 h-9 bg-red-100 rounded-xl flex items-center justify-center">❌</div>
                        </div>
                        <p class="text-3xl font-bold text-red-600">{{ $counts['reject'] }}</p>
                        <p class="text-xs text-red-500 mt-1">DECLINE (termasuk reason)</p>
                    </div>
                    <div class="bg-white rounded-2xl p-5 border border-orange-100 shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-orange-600 uppercase tracking-wider">Waiting List</span>
                            <div class="w-9 h-9 bg-orange-100 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-orange-600">{{ $counts['waitlist'] }}</p>
                        <p class="text-xs text-orange-500 mt-1">WAITING LIST</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- Sample table --}}
                    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="font-bold text-gray-800 text-sm">Contoh Data ({{ count($toApply) }} baris)</h3>
                            <span class="text-xs text-gray-400">Menampilkan 25 baris pertama</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50">
                                    <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        <th class="px-4 py-3">Name</th>
                                        <th class="px-4 py-3">Email</th>
                                        <th class="px-4 py-3">Excel Status</th>
                                        <th class="px-4 py-3">Akan Dicatat</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($sample as $row)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 font-medium text-gray-800">{{ $row['name'] }}</td>
                                            <td class="px-4 py-3 text-gray-500">{{ $row['email'] }}</td>
                                            <td class="px-4 py-3">
                                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">{{ $row['excel_status'] }}</span>
                                            </td>
                                            <td class="px-4 py-3">
                                                @php
                                                    $badge = match ($row['action']) {
                                                        'approve' => ['✅ Approve', 'bg-emerald-100 text-emerald-700'],
                                                        'reject'  => ['❌ Reject', 'bg-red-100 text-red-700'],
                                                        'waitlist'=> ['Waiting List', 'bg-orange-100 text-orange-700'],
                                                        default   => [$row['action'], 'bg-gray-100 text-gray-600'],
                                                    };
                                                @endphp
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold {{ $badge[1] }}">
                                                    @if ($row['action'] === 'waitlist')
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    @endif
                                                    {{ $badge[0] }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Skipped breakdown --}}
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden h-fit">
                        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="font-bold text-gray-800 text-sm">Dilewati (skipped)</h3>
                            <span class="text-xs text-gray-400">{{ $pending['total_rows'] - count($toApply) }} baris</span>
                        </div>
                        <div class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
                            @forelse ($pending['skipped_reasons'] ?? [] as $reason => $n)
                                <div class="px-5 py-3 flex items-center justify-between gap-3">
                                    <span class="text-sm text-gray-600 break-all">{{ $reason }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-700 flex-shrink-0">{{ $n }}</span>
                                </div>
                            @empty
                                <div class="px-5 py-8 text-center text-sm text-gray-400">Tidak ada baris yang dilewati 🎉</div>
                            @endforelse
                        </div>
                    </div>
                </div>

            @else
                {{-- ═══════════ UPLOAD STATE ═══════════ --}}
                <div class="max-w-3xl">
                    <div class="bg-white rounded-2xl border border-indigo-100 p-5 sm:p-6 shadow-sm">
                        <div class="flex items-start gap-4">
                            <div class="w-11 h-11 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Upload laporan konfirmasi client (.xlsx)</h2>
                                <p class="text-sm text-gray-500 mt-1 leading-relaxed">
                                    Unggah file Excel dari client (mis. <em>Report from Rozan.xlsx</em>).
                                    Kolom yang dibaca: <strong>BUSINESS EMAIL</strong>, <strong>FULL NAME</strong>, dan <strong>APPROVAL STATUS</strong>.
                                    Status <strong>APPROVED</strong> → ✅ Approve, <strong>DECLINE</strong> → ❌ Reject,
                                    <strong>WAITING LIST</strong> → Waiting List. Status asli registrant tidak diubah.
                                </p>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.import-client-confirmations.preview') }}"
                          enctype="multipart/form-data"
                          class="mt-6 bg-white rounded-2xl border border-gray-200 shadow-sm p-5 sm:p-6 space-y-5">
                        @csrf

                        @if ($errors->any())
                            <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <p>• {{ $error }}</p>
                                @endforeach
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">File Excel (.xlsx)</label>
                            <div class="relative">
                                <input type="file" name="file" accept=".xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                                       class="block w-full text-sm text-gray-700 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition cursor-pointer border border-gray-200 rounded-xl p-2"
                                       required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Catat atas nama client</label>
                            <select name="client_id" required
                                    class="w-full sm:w-96 rounded-xl border-gray-200 bg-gray-50 text-sm font-medium text-gray-800 focus:border-indigo-500 focus:ring-indigo-500 py-2.5 px-3">
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" {{ $client->id === ($selectedClientId ?? null) ? 'selected' : '' }}>
                                        {{ $client->name }} ({{ $client->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center gap-3 pt-1">
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition shadow-sm shadow-indigo-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Lihat Preview
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </main>
</div>
@include('admin.partials.mobile-sidebar')
</body>
</html>
