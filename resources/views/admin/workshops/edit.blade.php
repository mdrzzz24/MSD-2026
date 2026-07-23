<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Workshop — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans antialiased">
<div class="flex min-h-screen">
@include('admin.partials.sidebar')
<main class="flex-1 lg:ml-64">
<header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200"><div class="flex items-center h-16 px-4 sm:px-6 lg:px-8 gap-4"><a href="{{ route('admin.workshops.index') }}" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>Workshop</a><span class="text-gray-300">/</span><h1 class="text-lg font-bold text-gray-900">Edit: {{ $workshop->title }}</h1></div></header>
<div class="p-4 sm:p-6 lg:p-8"><div class="max-w-2xl"><div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
    @if ($errors->any())
        <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6"><svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><ul class="list-disc list-inside text-sm">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif
    <form action="{{ route('admin.workshops.update', $workshop) }}" method="POST" class="space-y-5">
        @csrf @method('PUT')
        <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Workshop Name</label><input type="text" name="name" value="{{ old('name', $workshop->name) }}" placeholder="e.g. Workshop Session 1" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition"></div>
        <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Workshop Title</label><input type="text" name="title" value="{{ old('title', $workshop->title) }}" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition"></div>
        <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Description <span class="text-xs text-gray-400 font-normal">(HTML supported)</span></label>
                        <textarea name="description" id="summernote" rows="8" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition resize-y">{{ old('description', $workshop->description) }}</textarea></div>
        @if ($workshop->agendaItems()->exists())
            <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 text-sm text-indigo-700">
                <strong>🔗 Linked to Agenda:</strong>
                @foreach ($workshop->agendaItems as $ai)
                    <span class="inline-block mt-1 px-2 py-0.5 bg-white rounded text-xs font-medium">{{ $ai->title }} ({{ $ai->timeLabel() }})</span>
                @endforeach
            </div>
        @else
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-700">
                <strong>💡 Not linked to any agenda yet.</strong> Go to <strong>Agenda</strong> → Create/Edit to link this workshop.
            </div>
        @endif
        <div class="flex gap-3">
            <button type="submit" class="flex-1 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/25 transition-all text-sm">Update Workshop</button>
            <a href="{{ route('admin.workshops.tracks', $workshop) }}" class="flex-none px-5 py-3 bg-teal-50 border border-teal-200 text-teal-700 font-semibold rounded-xl hover:bg-teal-100 transition-all text-sm text-center">
                Manage Tracks
            </a>
        </div>
    </form>

    @php $workshopTracks = $workshop->tracks()->with('speakers')->get(); @endphp
    @if ($workshopTracks->isNotEmpty())
        <div class="mt-6 pt-6 border-t border-gray-100">
            <h3 class="text-sm font-bold text-gray-800 mb-3">Tracks in this Workshop</h3>
            <div class="space-y-2">
                @foreach ($workshopTracks as $t)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 border border-gray-100">
                        <div class="flex items-center gap-3">
                            <span class="px-2 py-0.5 rounded text-xs font-bold bg-teal-100 text-teal-700">{{ $t->name }}</span>
                            @if ($t->speakers->isNotEmpty())
                                <span class="text-xs text-gray-500">
                                    @foreach ($t->speakers as $sp)
                                        {{ $sp->name }}@if (!$loop->last), @endif
                                    @endforeach
                                </span>
                            @endif
                        </div>
                        @if (!$t->is_active)
                            <span class="text-xs text-gray-400 italic">Inactive</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div></div></div>
</main>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.js"></script>
<script>
jQuery(document).ready(function() {
    jQuery('#summernote').summernote({
        height: 280,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link']],
            ['view', ['codeview']]
        ],
        callbacks: {
            onChange: function(contents) {
                var text = jQuery('<div>'+contents+'</div>').text();
                if (text.length > 65000) {
                    jQuery(this).summernote('undo');
                    alert('Description is too long.');
                }
            }
        }
    });
});
</script>
</body>
</html>
