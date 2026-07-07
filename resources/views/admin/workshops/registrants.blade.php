<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Workshop Registrants — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
</head>
<body class="bg-gray-50 font-sans antialiased">
<div class="flex min-h-screen">
@include('admin.partials.sidebar')
<main class="flex-1 lg:ml-64">
<header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200"><div class="flex items-center h-16 px-4 sm:px-6 lg:px-8 gap-4"><a href="{{ route('admin.workshops.index') }}" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>Workshop</a><span class="text-gray-300">/</span><h1 class="text-lg font-bold text-gray-900">Registrants: {{ $workshop->title }}</h1></div></header>
<div class="p-4 sm:p-6 lg:p-8">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <p class="text-sm text-gray-500">{{ $workshop->date->format('d M Y') }} • {{ $workshop->timeRange() }} • {{ $workshop->room ?? '—' }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Total registrants: <strong>{{ $registrants->count() }}</strong></p>
        </div>
        @if ($registrants->isEmpty())
            <div class="px-5 py-12 text-center text-gray-400 text-sm">No registrants yet.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead><tr class="bg-gray-50/80">
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Nama</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase hidden sm:table-cell">Company</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Status</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($registrants as $r)
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-5 py-4"><p class="text-sm font-semibold text-gray-900">{{ $r->display_name }}</p></td>
                                <td class="px-5 py-4"><span class="text-sm text-gray-600">{{ $r->email }}</span></td>
                                <td class="px-5 py-4 hidden sm:table-cell"><span class="text-sm text-gray-600">{{ $r->company ?? '—' }}</span></td>
                                <td class="px-5 py-4 hidden md:table-cell">
                                    @if ($r->status === 'approved')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Approved</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> {{ ucfirst($r->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
</main>
</div>
</body>
</html>
