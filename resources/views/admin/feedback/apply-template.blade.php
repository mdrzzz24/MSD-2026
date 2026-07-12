<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Questions — {{ $agendum->title }} — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 font-sans antialiased">
<div class="flex min-h-screen">
    @include('admin.partials.sidebar')
    <main class="flex-1 lg:ml-64">
        <header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200">
            <div class="flex items-center h-16 px-4 sm:px-6 lg:px-8 gap-4">
                <a href="{{ route('admin.agenda.feedback.show', $agendum) }}" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    {{ $agendum->title }}
                </a>
                <span class="text-gray-300">/</span>
                <h1 class="text-lg font-bold text-gray-900">Manage Questions</h1>
            </div>
        </header>
        <div class="p-4 sm:p-6 lg:p-8 max-w-3xl">
            @include('admin.partials.notification')

            {{-- Current Questions (editable) --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-gray-800">Current Questions ({{ $currentQuestions->count() }})</h2>
                    @if ($currentQuestions->count() > 0)
                        <form action="{{ route('admin.agenda.feedback.questions.clear', $agendum) }}" method="POST" onsubmit="return confirm('Remove all questions?')">
                            @csrf
                            <button class="px-3 py-1.5 text-xs font-medium rounded-lg bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 transition">Remove All</button>
                        </form>
                    @endif
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse ($currentQuestions as $q)
                        <div class="px-6 py-4 hover:bg-gray-50/50 transition" x-data="{ editing: false }">
                            {{-- Display mode --}}
                            <div x-show="!editing" class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800">{{ $q->question_text }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        <span class="capitalize">{{ str_replace('_', ' ', $q->question_type) }}</span>
                                        @if ($q->required) · <span class="text-emerald-600 font-medium">Required</span> @endif
                                        @if ($q->source_template_id) · <span class="text-indigo-500">from template</span> @endif
                                        @if ($q->parent_question_id) · <span class="text-indigo-500">nested</span> @endif
                                        @if ($q->trigger_value) · trigger: <span class="font-mono text-indigo-500">{{ $q->trigger_value }}</span> @endif
                                    </p>
                                </div>
                                <div class="flex items-center gap-2 ml-3 flex-shrink-0">
                                    <span class="text-xs text-gray-400">#{{ $q->order + 1 }}</span>
                                    <button @click="editing = true" class="px-3 py-1 text-xs font-medium rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 transition">Edit</button>
                                </div>
                            </div>

                            {{-- Edit mode --}}
                            <form x-show="editing" action="{{ route('admin.agenda.feedback.questions.update', [$agendum, $q]) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Question</label>
                                        <input type="text" name="question_text" value="{{ $q->question_text }}" required
                                               class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Type</label>
                                            <select name="question_type"
                                                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                                                <option value="text" {{ $q->question_type === 'text' ? 'selected' : '' }}>Text</option>
                                                <option value="rating" {{ $q->question_type === 'rating' ? 'selected' : '' }}>Rating (1-5)</option>
                                                <option value="choice" {{ $q->question_type === 'choice' ? 'selected' : '' }}>Multiple Choice</option>
                                                <option value="yes_no" {{ $q->question_type === 'yes_no' ? 'selected' : '' }}>Yes / No</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Trigger Value</label>
                                            <input type="text" name="trigger_value" value="{{ $q->trigger_value }}"
                                                   class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition"
                                                   placeholder="e.g. Yes">
                                        </div>
                                    </div>
                                    @if ($q->question_type === 'choice')
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Options (one per line)</label>
                                        <textarea name="options" rows="3"
                                                  class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition resize-none">{{ is_array($q->options) ? implode("\n", $q->options) : '' }}</textarea>
                                    </div>
                                    @endif
                                    <div class="flex items-center gap-2">
                                        <input type="hidden" name="required" value="0">
                                        <label class="flex items-center gap-2 text-xs text-gray-600">
                                            <input type="checkbox" name="required" value="1" {{ $q->required ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            Required
                                        </label>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="submit" class="px-4 py-1.5 text-xs font-semibold rounded-lg bg-indigo-500 text-white hover:bg-indigo-600 transition">Save</button>
                                        <button type="button" @click="editing = false" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @empty
                        <div class="px-6 py-10 text-center text-sm text-gray-400">No questions set yet. Apply a template below.</div>
                    @endforelse
                </div>
            </div>

            {{-- Apply Template --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="text-sm font-bold text-gray-800 mb-4">Apply Template</h2>
                <form action="{{ route('admin.agenda.feedback.apply-template', $agendum) }}" method="POST" onsubmit="return confirm('This will replace all existing questions. Continue?')">
                    @csrf
                    <div class="mb-4">
                        <select name="template_id" required
                                class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition">
                            <option value="">— Select Template —</option>
                            @foreach ($templates as $t)
                                <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->questions_count }} questions)</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                            class="px-5 py-2.5 text-sm font-semibold rounded-xl bg-indigo-500 text-white hover:bg-indigo-600 shadow-sm transition">
                        Apply Template
                    </button>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>
