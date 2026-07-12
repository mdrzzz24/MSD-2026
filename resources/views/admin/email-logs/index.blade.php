<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Email Logs — {{ config('app.name') }}</title>
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
                <div>
                    <h1 class="text-lg font-bold text-gray-900">Email Logs</h1>
                    <p class="text-xs text-gray-500">All email sending history</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.email-logs.export-csv', request()->query()) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg border border-gray-200 text-gray-600 bg-white hover:bg-gray-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export CSV
                    </a>
                </div>
            </div>
        </header>

        <div class="p-4 sm:p-6 lg:p-8">

            {{-- Summary Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalSent }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Sent</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $totalSuccess }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Failed</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $totalFailed }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Bounced</p>
                    <p class="text-2xl font-bold text-orange-600 mt-1">{{ $totalBounced }}</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Unique Recipients</p>
                    <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $uniqueRecipients }}</p>
                </div>
            </div>

            {{-- Filter --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
                <form method="GET" class="flex flex-wrap items-end gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Email Type</label>
                        <select name="type" class="px-3 py-2 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="">All types</option>
                            @foreach ($types as $key => $info)
                                <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $info['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                        <select name="status" class="px-3 py-2 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="">All statuses</option>
                            <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="bounced" {{ request('status') == 'bounced' ? 'selected' : '' }}>Bounced</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">From date</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                               class="px-3 py-2 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">To date</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}"
                               class="px-3 py-2 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Email / Name / Subject"
                               class="px-3 py-2 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-indigo-500 w-48">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-indigo-500 text-white text-sm font-semibold rounded-xl hover:bg-indigo-600 transition shadow-sm shadow-indigo-200">
                            Filter
                        </button>
                        <a href="{{ route('admin.email-logs.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-200 transition">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            @include('admin.partials.notification')

            {{-- Table --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50/80">
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Time</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Recipient</th>
                                <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Subject</th>
                                <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse ($logs as $log)
                                @php $color = \App\Models\EmailTemplate::typeColor($log->template_type); $label = \App\Models\EmailTemplate::typeLabel($log->template_type); @endphp
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-5 py-4 text-sm text-gray-500 whitespace-nowrap">
                                        {{ $log->sent_at ? $log->sent_at->format('d M H:i') : '-' }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-{{ $color }}-50 text-{{ $color }}-700 border border-{{ $color }}-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-{{ $color }}-500"></span>
                                            {{ $label }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $log->recipient_name ?: '-' }}</div>
                                        <div class="text-xs text-gray-400">{{ $log->recipient_email }}</div>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-600 max-w-xs truncate">{{ $log->subject }}</td>
                                    <td class="px-5 py-4 text-center">
                                        @if ($log->status === 'sent')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Sent
                                            </span>
                                        @elseif ($log->status === 'bounced')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-orange-50 text-orange-700 border border-orange-200" title="{{ $log->error_message }}">
                                                <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span> Bounced
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200" title="{{ $log->error_message }}">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Failed
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <a href="{{ route('admin.email-logs.show', $log) }}"
                                           class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-sky-100 text-sky-700 hover:bg-sky-200 transition">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-16 text-center">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            <p class="text-gray-400 font-medium">No logs yet</p>
                                            <p class="text-xs text-gray-400">Logs will appear after emails are sent.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                {{ $logs->withQueryString()->links() }}
            </div>
        </div>
    </main>
</div>

</body>
</html>
