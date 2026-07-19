<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Template — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
</head>
<body class="bg-gray-50 font-sans antialiased">
<div class="flex min-h-screen">
@include('admin.partials.sidebar')
<main class="flex-1 lg:ml-64">
<header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200"><div class="flex items-center h-16 px-4 sm:px-6 lg:px-8 gap-4"><a href="{{ route('admin.templates.index') }}" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>Template</a><span class="text-gray-300">/</span><h1 class="text-lg font-bold text-gray-900">Create Manual</h1></div></header>
<div class="p-4 sm:p-6 lg:p-8">
    @if ($errors->any())
        <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 max-w-7xl mx-auto"><svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><ul class="list-disc list-inside text-sm">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    <div class="max-w-7xl mx-auto">
    <form action="{{ route('admin.templates.store') }}" method="POST">
        @csrf
        <div class="flex gap-4 mb-4">
            <div class="flex-1"><label class="block text-sm font-semibold text-gray-700 mb-1.5">Template Name</label><input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition"></div>
            <div class="w-48"><label class="block text-sm font-semibold text-gray-700 mb-1.5">Type</label><select name="type" required class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition"><option value="">-- Select --</option>@foreach ($types as $key => $info)<option value="{{ $key }}" {{ old('type', $presetType ?? '')===$key?'selected':'' }}>{{ $info['label'] }}</option>@endforeach</select></div>
            <div class="flex-1"><label class="block text-sm font-semibold text-gray-700 mb-1.5">Subject Email</label><input type="text" name="subject" value="{{ old('subject', $presetSubject ?? '') }}" required class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition"></div>
        </div>
        <div class="flex gap-4">
            {{-- Editor --}}
            <div class="w-1/2">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-sm font-semibold text-gray-700">HTML Content</label>
                        <div class="text-xs text-gray-400">Placeholder: <code class="bg-gray-100 px-1 py-0.5 rounded text-indigo-600">@{{ name }}</code> <code class="bg-gray-100 px-1 py-0.5 rounded text-indigo-600">@{{ email }}</code> <code class="bg-gray-100 px-1 py-0.5 rounded text-indigo-600">@{{ password }}</code> <code class="bg-gray-100 px-1 py-0.5 rounded text-indigo-600">@{{ status }}</code> <code class="bg-gray-100 px-1 py-0.5 rounded text-indigo-600">@{{ admin_notes }}</code> <code class="bg-gray-100 px-1 py-0.5 rounded text-indigo-600">@{{ workshop_name }}</code> <code class="bg-gray-100 px-1 py-0.5 rounded text-indigo-600">@{{ workshop_title }}</code> <code class="bg-gray-100 px-1 py-0.5 rounded text-indigo-600">@{{ workshop_room }}</code> <code class="bg-gray-100 px-1 py-0.5 rounded text-indigo-600">@{{ workshop_date }}</code> <code class="bg-gray-100 px-1 py-0.5 rounded text-indigo-600">@{{ workshop_time }}</code> <code class="bg-gray-100 px-1 py-0.5 rounded text-indigo-600">@{{ workshop_capacity }}</code> <code class="bg-gray-100 px-1 py-0.5 rounded text-indigo-600">@{{ venue_name }}</code> <code class="bg-gray-100 px-1 py-0.5 rounded text-indigo-600">@{{ qr_code }}</code></div>
                    </div>
                    <textarea name="html_content" id="htmlEditor" rows="24" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition resize-y leading-relaxed">{{ old('html_content', $presetHtml ?? '') }}</textarea>
                </div>
            </div>
            {{-- Preview --}}
            <div class="w-1/2">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                        <span class="text-sm font-semibold text-gray-700">Live Preview</span>
                        <span class="text-xs text-gray-400">Sample data: John Doe</span>
                    </div>
                    <div class="bg-gray-50 p-4" style="min-height:500px;">
                        <iframe id="previewFrame" style="width:100%;height:500px;border:none;border-radius:8px;background:#fff;"></iframe>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4 flex justify-end">
            <button type="submit" class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/25 transition-all text-sm">💾 Save Template</button>
        </div>
    </form>
    </div>
</div>

<script>
(function() {
    var editor = document.getElementById('htmlEditor');
    var frame = document.getElementById('previewFrame');
    var doc = frame.contentDocument || frame.contentWindow.document;

    function updatePreview() {
        var html = editor.value;
        // Replace placeholders with sample data for preview
        html = html
            .replace(/\{\{\s*name\s*\}\}/g, 'John Doe')
            .replace(/\{\{\s*email\s*\}\}/g, 'john@example.com')
            .replace(/\{\{\s*password\s*\}\}/g, '••••••••')
            .replace(/\{\{\s*status\s*\}\}/g, 'approved')
            .replace(/\{\{\s*unique_code\s*\}\}/g, '100724080000')
            .replace(/\{\{\s*admin_notes\s*\}\}/g, 'Sample note')
            .replace(/\{\{\s*workshop_name\s*\}\}/g, 'Sample Workshop Session')
            .replace(/\{\{\s*workshop_title\s*\}\}/g, 'Sample Workshop Topic')
            .replace(/\{\{\s*workshop_room\s*\}\}/g, 'Meeting Room A')
            .replace(/\{\{\s*workshop_date\s*\}\}/g, 'Thursday, 20 August 2026')
            .replace(/\{\{\s*workshop_time\s*\}\}/g, '09:00 – 12:00')
            .replace(/\{\{\s*workshop_capacity\s*\}\}/g, '35')
            .replace(/\{\{\s*venue_name\s*\}\}/g, 'Shangri-La Hotel Jakarta')
            .replace(/\{\{\s*track_name\s*\}\}/g, 'Sample Session')
            .replace(/\{\{\s*event_date\s*\}\}/g, '12 August 2026')
            .replace(/\{\{\s*login_url\s*\}\}/g, window.location.origin + '/login')
            .replace(/\{\{\s*qr_code\s*\}\}/g, '<img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=sample" alt="QR" style="width:200px;height:200px;display:block;margin:16px auto;">')
            .replace(/\{\{\s*qr_checkin_url\s*\}\}/g, window.location.origin + '/login');

        doc.open();
        doc.write(html);
        doc.close();
    }

    editor.addEventListener('input', updatePreview);
    // Initial render
    setTimeout(updatePreview, 100);
})();
</script>
</main></div>
</body>
</html>
