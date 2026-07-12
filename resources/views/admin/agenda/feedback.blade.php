<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Feedback — {{ $agendum->title }} — {{ config('app.name') }}</title>
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
                <a href="{{ route('admin.feedback.index') }}" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Feedback
                </a>
                <span class="text-gray-300">/</span>
                <h1 class="text-lg font-bold text-gray-900">{{ $agendum->title }}</h1>
                <div class="ml-auto flex items-center gap-2">
                    <a href="{{ route('admin.agenda.feedback.export-csv', $agendum) }}"
                       class="px-4 py-2 text-sm font-semibold rounded-xl bg-emerald-500 text-white hover:bg-emerald-600 shadow-sm transition">
                        Export CSV
                    </a>
                    <a href="{{ route('admin.agenda.feedback.questions', $agendum) }}"
                       class="px-4 py-2 text-sm font-semibold rounded-xl bg-indigo-500 text-white hover:bg-indigo-600 shadow-sm transition">
                        Manage Questions
                    </a>
                </div>
            </div>
        </header>
        <div class="p-4 sm:p-6 lg:p-8">
            @include('admin.partials.notification')

            {{-- Summary Stats --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Responses</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $feedbacks->count() }}</p>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Avg Rating</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        {{ $feedbacks->whereNotNull('rating')->avg('rating') ? number_format($feedbacks->whereNotNull('rating')->avg('rating'), 1) : '—' }}
                    </p>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Feedback Form</p>
                    <p class="mt-1">
                        @if ($agendum->feedback_enabled)
                            <span class="inline-flex items-center gap-1 text-sm font-semibold text-emerald-700">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Enabled
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-sm font-semibold text-gray-500">
                                <span class="w-2 h-2 rounded-full bg-gray-300"></span> Disabled
                            </span>
                        @endif
                    </p>
                </div>
            </div>

            {{-- Questions Overview --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-gray-800">Questions ({{ $questions->count() }})</h2>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse ($questions as $q)
                        <div class="px-6 py-3 flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-800">{{ $q->question_text }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    <span class="capitalize">{{ $q->question_type }}</span>
                                    @if ($q->required) · Required @endif
                                </p>
                            </div>
                            <div class="text-right text-xs text-gray-500">
                                @php $answerCount = $feedbacks->count(); @endphp
                                <span>{{ $answerCount }} answers</span>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-10 text-center text-sm text-gray-400">
                            No questions set.
                            <a href="{{ route('admin.agenda.feedback.questions', $agendum) }}" class="text-indigo-600 hover:underline ml-1">Set up questions →</a>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Feedback List --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                    <h2 class="text-sm font-bold text-gray-800">Feedback Responses</h2>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse ($feedbacks as $fb)
                        <div class="p-5 hover:bg-gray-50/50 transition">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $fb->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $fb->email }}</p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="text-xs text-gray-400">{{ $fb->created_at->format('d M Y, H:i') }}</p>
                                </div>
                            </div>

                            {{-- Answers --}}
                            @if ($questions->count() > 0 && $fb->answers->count() > 0)
                                <div class="mt-3 space-y-2 bg-gray-50 rounded-xl p-3">
                                    @foreach ($questions as $q)
                                        @php $answer = $fb->answers->firstWhere('agenda_item_question_id', $q->id); @endphp
                                        @if ($answer && $answer->answer_value)
                                            <div class="text-sm">
                                                <span class="text-xs font-semibold text-gray-500">{{ $q->question_text }}:</span>
                                                @if ($q->question_type === 'rating')
                                                    <span class="inline-flex items-center gap-0.5 ml-1">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            <svg class="w-3.5 h-3.5 {{ $i <= (int)$answer->answer_value ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                            </svg>
                                                        @endfor
                                                    </span>
                                                @elseif ($q->question_type === 'yes_no')
                                                    <span class="inline-flex items-center gap-1 ml-1 font-medium {{ $answer->answer_value === 'yes' ? 'text-emerald-600' : 'text-red-500' }}">
                                                        {{ ucfirst($answer->answer_value) }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-700 ml-1">{{ $answer->answer_value }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="p-10 text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <p class="text-sm text-gray-400 mt-3">No feedback responses yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Public Feedback Link --}}
            @if ($agendum->feedback_enabled)
                <div class="mt-6 bg-indigo-50 rounded-2xl p-5 border border-indigo-200">
                    <p class="text-sm font-semibold text-indigo-800 mb-1">Public Feedback Link</p>
                    <p class="text-xs text-indigo-600 mb-2">Share this link with attendees to collect feedback:</p>
                    <div class="flex items-center gap-2">
                        <input type="text" value="{{ route('feedback.form', $agendum) }}" readonly
                               class="text-xs text-indigo-700 bg-white px-3 py-2 rounded-lg border border-indigo-200 w-full"
                               id="feedbackUrl">
                        <button onclick="copyFeedbackUrl()"
                                class="px-3 py-2 text-xs font-medium rounded-lg bg-indigo-500 text-white hover:bg-indigo-600 transition flex-shrink-0">
                            Copy
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </main>
</div>
<script>
    function copyFeedbackUrl() {
        const el = document.getElementById('feedbackUrl');
        if (!el) return;
        navigator.clipboard.writeText(el.value).then(() => {
            const btn = event.target;
            btn.textContent = 'Copied!';
            btn.classList.add('bg-emerald-500');
            btn.classList.remove('bg-indigo-500', 'hover:bg-indigo-600');
            setTimeout(() => {
                btn.textContent = 'Copy';
                btn.classList.remove('bg-emerald-500');
                btn.classList.add('bg-indigo-500', 'hover:bg-indigo-600');
            }, 1500);
        });
    }
</script>
</body>
</html>
