<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invitations: {{ $workshop->name ?: $workshop->title }} — {{ config('app.name') }}</title>
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
        <a href="{{ route('admin.workshops.index') }}" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>Workshops
        </a>
        <span class="text-gray-300">/</span>
        <h1 class="text-lg font-bold text-gray-900">Invitations: {{ $workshop->name ?: $workshop->title }}</h1>
    </div>
</header>
<div class="p-4 sm:p-6 lg:p-8">
    @include('admin.partials.notification')

    {{-- Generate Invitation --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
        <h2 class="text-sm font-bold text-gray-800 mb-3">Generate Invitation Link</h2>
        <form action="{{ route('admin.workshops.invitations.generate', $workshop) }}" method="POST" class="flex items-end gap-3">
            @csrf
            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Target Email <span class="text-gray-400">(optional)</span></label>
                <input type="email" name="email" placeholder="invitee@company.com" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition">
            </div>
            <div class="w-32">
                <label class="block text-xs font-semibold text-gray-600 mb-1">Max Uses</label>
                <input type="number" name="max_uses" value="1" min="1" max="100" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition">
            </div>
            <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 shadow-sm transition">Generate</button>
        </form>
    </div>

    {{-- Invitations List --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50/80">
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Link</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Target Email</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Uses</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Created</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($invitations as $inv)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('workshop.invitation', $inv->token) }}" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium truncate block max-w-[220px]">
                                        {{ route('workshop.invitation', $inv->token) }}
                                    </a>
                                    <button onclick="copyLink(this, '{{ route('workshop.invitation', $inv->token) }}')"
                                            class="flex-shrink-0 px-2 py-1 text-xs font-medium rounded-lg bg-gray-100 text-gray-600 hover:bg-indigo-100 hover:text-indigo-700 transition"
                                            title="Copy link">
                                        Copy
                                    </button>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">{{ $inv->email ?? '—' }}</td>
                            <td class="px-5 py-4 text-sm text-center text-gray-600">{{ $inv->use_count }}/{{ $inv->max_uses }}</td>
                            <td class="px-5 py-4 text-center">
                                @if ($inv->is_active && $inv->use_count < $inv->max_uses)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500 border border-gray-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> {{ $inv->is_active ? 'Used Up' : 'Inactive' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-sm text-center text-gray-500">{{ $inv->created_at->format('d M H:i') }}</td>
                            <td class="px-5 py-4 text-center">
                                <form action="{{ route('admin.workshops.invitations.toggle', $inv) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1.5 text-xs font-medium rounded-lg {{ $inv->is_active ? 'bg-amber-100 text-amber-700 hover:bg-amber-200' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' }} transition">
                                        {{ $inv->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center">
                                <p class="text-gray-400 font-medium">No invitations yet</p>
                                <p class="text-xs text-gray-400 mt-1">Generate an invitation link above to get started.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.workshops.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">&larr; Back to Workshops</a>
    </div>
</div>
</main>
</div>

<script>
function copyLink(btn, url) {
    navigator.clipboard.writeText(url).then(function() {
        var orig = btn.textContent;
        btn.textContent = 'Copied!';
        btn.classList.remove('bg-gray-100', 'text-gray-600', 'hover:bg-indigo-100', 'hover:text-indigo-700');
        btn.classList.add('bg-emerald-100', 'text-emerald-700');
        setTimeout(function() {
            btn.textContent = orig;
            btn.classList.remove('bg-emerald-100', 'text-emerald-700');
            btn.classList.add('bg-gray-100', 'text-gray-600', 'hover:bg-indigo-100', 'hover:text-indigo-700');
        }, 2000);
    }).catch(function() {
        alert('Failed to copy link');
    });
}
</script>
</body>
</html>
