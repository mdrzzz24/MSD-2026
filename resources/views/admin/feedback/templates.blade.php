<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Feedback Templates — {{ config('app.name') }}</title>
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
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Dashboard
                    </a>
                    <span class="text-gray-300">/</span>
                    <h1 class="text-lg font-bold text-gray-900">Feedback Templates</h1>
                </div>
                <a href="{{ route('admin.feedback.templates.create') }}" class="px-4 py-2 text-sm font-semibold rounded-xl bg-indigo-500 text-white hover:bg-indigo-600 shadow-sm transition">
                    + New Template
                </a>
            </div>
        </header>
        <div class="p-4 sm:p-6 lg:p-8">
            @include('admin.partials.notification')

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                @forelse ($templates as $t)
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition overflow-hidden">
                        <div class="px-5 py-4 border-b border-gray-100">
                            <h3 class="text-sm font-bold text-gray-900">{{ $t->name }}</h3>
                            @if ($t->description)
                                <p class="text-xs text-gray-500 mt-0.5">{{ $t->description }}</p>
                            @endif
                        </div>
                        <div class="px-5 py-3 flex items-center justify-between text-xs text-gray-500">
                            <span>{{ $t->questions_count }} question(s)</span>
                            <span>{{ $t->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="px-5 py-3 bg-gray-50/50 border-t border-gray-100 flex items-center gap-2">
                            <a href="{{ route('admin.feedback.templates.edit', $t) }}" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 transition">Edit</a>
                            <form action="{{ route('admin.feedback.templates.destroy', $t) }}" method="POST" class="inline" onsubmit="return confirm('Delete template &quot;{{ $t->name }}&quot;?')">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1.5 text-xs font-medium rounded-lg bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 transition">Delete</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="md:col-span-2 xl:col-span-3 text-center py-16">
                        <svg class="w-12 h-12 mx-auto text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <p class="text-sm text-gray-400 mt-3">No templates yet.</p>
                        <a href="{{ route('admin.feedback.templates.create') }}" class="inline-block mt-3 text-sm font-medium text-indigo-600 hover:text-indigo-800">Create your first template →</a>
                    </div>
                @endforelse
            </div>
        </div>
    </main>
</div>
</body>
</html>
