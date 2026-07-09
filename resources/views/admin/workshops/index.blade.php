<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Workshops — {{ config('app.name') }}</title>
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
        <div><h1 class="text-lg font-bold text-gray-900">Manage Workshops</h1><p class="text-xs text-gray-500">Manage schedules, open/close registration</p></div>
        @unless(Auth::user()->isClient())
        <a href="{{ route('admin.workshops.create') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium bg-indigo-500 text-white rounded-xl hover:bg-indigo-600 shadow-sm shadow-indigo-200 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Create Workshop</a>
        @endunless
    </div>
</header>
<div class="p-4 sm:p-6 lg:p-8">
    @if (session('success'))
        <div class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl mb-6"><svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span class="text-sm">{!! session('success') !!}</span></div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead><tr class="bg-gray-50/80">
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Workshop</th>
                    @unless(Auth::user()->isClient())
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase hidden lg:table-cell">Linked Agenda</th>
                    @endunless
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Registrants</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($workshops as $w)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-4"><p class="text-sm font-semibold text-gray-900">{{ $w->title }}</p>
                                @if($w->description)<p class="text-xs text-gray-400 mt-0.5 truncate max-w-[250px]">{{ $w->description }}</p>@endif
                            </td>
                            @unless(Auth::user()->isClient())
                            <td class="px-5 py-4 hidden lg:table-cell">
                                @php $linked = $w->agendaItems; @endphp
                                @if ($linked->isNotEmpty())
                                    @foreach ($linked as $ai)
                                        <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-indigo-50 text-indigo-700 mb-1">{{ $ai->title }}</span>
                                    @endforeach
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                            @endunless
                            <td class="px-5 py-4 hidden md:table-cell">
                                <div class="flex items-center gap-2 text-xs">
                                    <a href="{{ route('admin.workshops.registrants', $w) }}" class="font-bold text-indigo-600 hover:text-indigo-800">
                                        {{ ($w->approved_count ?? 0) + ($w->pending_count ?? 0) + ($w->rejected_count ?? 0) }} total
                                    </a>
                                    <span class="text-emerald-600">✓{{ $w->approved_count ?? 0 }}</span>
                                    <span class="text-amber-600">⏳{{ $w->pending_count ?? 0 }}</span>
                                    <span class="text-red-500">✕{{ $w->rejected_count ?? 0 }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                @if ($w->registration_open)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Open</span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Closed</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex justify-center gap-1.5">
                                    <a href="{{ route('admin.workshops.registrants', $w) }}" class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition">View</a>
                                    @unless(Auth::user()->isClient())
                                    <a href="{{ route('admin.workshops.edit', $w) }}" class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-amber-100 text-amber-700 hover:bg-amber-200 transition">Edit</a>
                                    <form action="{{ route('admin.workshops.toggle', $w) }}" method="POST">@csrf
                                        <button class="px-2.5 py-1.5 text-xs font-medium rounded-lg {{ $w->registration_open ? 'bg-red-100 text-red-600 hover:bg-red-200' : 'bg-emerald-100 text-emerald-600 hover:bg-emerald-200' }} transition">
                                            {{ $w->registration_open ? 'Close' : 'Open' }}
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.workshops.destroy', $w) }}" method="POST" onsubmit="return confirm('Delete workshop {{ $w->title }}?')">@csrf @method('DELETE')
                                        <button class="px-2.5 py-1.5 text-xs font-medium rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition">Delete</button>
                                    </form>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-16 text-center"><p class="text-gray-400 font-medium">No workshops yet</p><p class="text-xs text-gray-400">Create your first workshop</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</main>
</div>
</body>
</html>
