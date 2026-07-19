<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Upload Template — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
</head>
<body class="bg-gray-50 font-sans antialiased">
<div class="flex min-h-screen">
@include('admin.partials.sidebar')
<main class="flex-1 lg:ml-64">
<header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200"><div class="flex items-center h-16 px-4 sm:px-6 lg:px-8 gap-4"><a href="{{ route('admin.templates.index') }}" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>Template</a><span class="text-gray-300">/</span><h1 class="text-lg font-bold text-gray-900">Upload HTML</h1></div></header>
<div class="p-4 sm:p-6 lg:p-8"><div class="max-w-2xl"><div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
    @include('admin.partials.notification')
    @if ($errors->any())
        <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6"><svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><ul class="list-disc list-inside text-sm">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    <form action="{{ route('admin.templates.upload.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Template Name</label><input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition"></div>
        <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Type</label><select name="type" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition"><option value="">-- Select --</option>@foreach ($types as $key => $info)<option value="{{ $key }}" {{ old('type')===$key?'selected':'' }}>{{ $info['label'] }}</option>@endforeach</select></div>
        <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Description</label><input type="text" name="description" value="{{ old('description') }}" placeholder="Optional short description…" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition"></div>
        <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Subject Email</label><input type="text" name="subject" value="{{ old('subject') }}" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition"></div>
        <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">File</label><p class="text-xs text-gray-400 mb-2">Select <strong>.html</strong> / <strong>.htm</strong>, <strong>.zip</strong> (Word export), or <strong>.eml</strong> (email export from Outlook/Thunderbird).</p><p class="text-xs text-gray-400 mb-2">Supported placeholders: <code class="bg-gray-100 px-1 py-0.5 rounded text-indigo-600">@{{ name }}</code> <code class="bg-gray-100 px-1 py-0.5 rounded text-indigo-600">@{{ email }}</code> <code class="bg-gray-100 px-1 py-0.5 rounded text-indigo-600">@{{ status }}</code> <code class="bg-gray-100 px-1 py-0.5 rounded text-indigo-600">@{{ password }}</code> <code class="bg-gray-100 px-1 py-0.5 rounded text-indigo-600">@{{ admin_notes }}</code> <code class="bg-gray-100 px-1 py-0.5 rounded text-indigo-600">@{{ workshop_name }}</code> <code class="bg-gray-100 px-1 py-0.5 rounded text-indigo-600">@{{ workshop_title }}</code> <code class="bg-gray-100 px-1 py-0.5 rounded text-indigo-600">@{{ workshop_room }}</code> <code class="bg-gray-100 px-1 py-0.5 rounded text-indigo-600">@{{ workshop_date }}</code> <code class="bg-gray-100 px-1 py-0.5 rounded text-indigo-600">@{{ workshop_time }}</code> <code class="bg-gray-100 px-1 py-0.5 rounded text-indigo-600">@{{ workshop_capacity }}</code> <code class="bg-gray-100 px-1 py-0.5 rounded text-indigo-600">@{{ venue_name }}</code></p><input type="file" name="html_file" accept=".html,.htm,.zip,.eml" required class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition"></div>
        <button type="submit" class="w-full py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/25 transition-all text-sm">Upload Template</button>
    </form>
    </div></div></div></main></div>
</body>
</html>
