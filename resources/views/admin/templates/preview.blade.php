<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Preview: {{ $template->name }} — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
</head>
<body class="bg-gray-100 font-sans antialiased">

<div class="max-w-3xl mx-auto py-8 px-4">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h1 class="text-lg font-bold text-gray-900">📧 Preview Template</h1>
                <p class="text-xs text-gray-500">{{ $template->name }} — {{ \App\Models\EmailTemplate::typeLabel($template->type) }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-400">Subject: <strong>{{ $template->subject }}</strong></span>
                <a href="{{ route('admin.templates.index') }}" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition">← Back</a>
            </div>
        </div>
        <div class="p-6 bg-gray-50">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 bg-gray-100 border-b border-gray-200 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-400"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-yellow-400"></span>
                    <span class="w-2.5 h-2.5 rounded-full bg-green-400"></span>
                    <span class="text-xs text-gray-500 ml-2">Email Preview</span>
                </div>
                <div class="p-0 email-preview">
                    {!! $html !!}
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
