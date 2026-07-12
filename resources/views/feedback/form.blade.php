<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Feedback — {{ $agendum->title }} — MSD 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
</head>
<body class="bg-gradient-to-br from-indigo-900 via-[#0a1a4a] to-[#050d2a] font-sans antialiased min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-lg">
        {{-- Header --}}
        <div class="text-center mb-8">
            <img src="{{ asset('img/logo-msd.png') }}" alt="MSD 2026" style="height:48px;width:auto;filter:brightness(0) invert(1)" class="mx-auto mb-4">
            <h1 class="text-2xl font-bold text-white">Session Feedback</h1>
            <p class="text-indigo-200 text-sm mt-1">{{ $agendum->title }}</p>
            @if ($agendum->start_time)
                <p class="text-indigo-300 text-xs mt-0.5">{{ $agendum->timeLabel() }}</p>
            @endif
        </div>

        {{-- Alert --}}
        @if (session('success'))
            <div class="mb-4 p-4 rounded-xl bg-emerald-500/20 border border-emerald-400/30 text-emerald-200 text-sm text-center font-medium">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 p-4 rounded-xl bg-red-500/20 border border-red-400/30 text-red-200 text-sm text-center font-medium">
                {{ session('error') }}
            </div>
        @endif

        {{-- Form --}}
        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 sm:p-8 border border-white/10 shadow-2xl" x-data="feedbackForm()">
            <div class="mb-5 p-4 rounded-xl bg-white/5 border border-white/10">
                <p class="text-sm text-indigo-200">
                    <span class="font-semibold text-white">{{ $registrant->display_name }}</span>
                    <span class="text-indigo-300">({{ $registrant->email }})</span>
                </p>
            </div>

            <form action="{{ route('feedback.store', $agendum) }}" method="POST">
                @csrf

                {{-- Dynamic Questions --}}
                @if ($questions->count() > 0)
                    @php
                        // Build parent mapping for Alpine.js
                        $parentMap = [];
                        foreach ($questions as $q) {
                            if ($q->parent_question_id) {
                                $parentMap[$q->id] = $q->parent_question_id;
                            }
                        }
                    @endphp
                    @foreach ($questions as $q)
                        @php $isReq = $q->required ? 'true' : 'false'; @endphp
                        <div class="mb-5" x-show="isVisible({{ $q->id }})" x-cloak>
                            <label class="block text-sm font-semibold text-indigo-200 mb-2">
                                {{ $q->question_text }}
                                @if ($q->required) <span class="text-red-400">*</span> @endif
                            </label>

                            @if ($q->question_type === 'text')
                                <textarea name="answers[{{ $q->id }}]" rows="3"
                                          :required="isVisible({{ $q->id }}) && {{ $isReq }}"
                                          class="w-full px-4 py-2.5 text-sm text-white bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 focus:border-indigo-400 placeholder-indigo-300/40 transition resize-none"
                                          placeholder="Your answer...">{{ old('answers.' . $q->id) }}</textarea>

                            @elseif ($q->question_type === 'rating')
                                <div class="flex items-center gap-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <button type="button" @click="setRating({{ $q->id }}, {{ $i }})"
                                                class="p-1 transition hover:scale-110 focus:outline-none">
                                            <svg class="w-8 h-8" :class="getRating({{ $q->id }}) >= {{ $i }} ? 'text-yellow-400' : 'text-white/20'" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </button>
                                    @endfor
                                    <input type="hidden" :name="'answers[{{ $q->id }}]'" x-model="answers[{{ $q->id }}]" value="{{ old('answers.' . $q->id, 0) }}">
                                </div>

                            @elseif ($q->question_type === 'choice')
                                <div class="space-y-2">
                                    @foreach ($q->options ?? [] as $opt)
                                        <label class="flex items-center gap-3 p-3 rounded-xl bg-white/5 border border-white/10 cursor-pointer hover:bg-white/10 transition">
                                            <input type="radio" :name="'answers[{{ $q->id }}]'" value="{{ $opt }}"
                                                   x-model="answers[{{ $q->id }}]"
                                                   :required="isVisible({{ $q->id }}) && {{ $isReq }}"
                                                   {{ old('answers.' . $q->id) === $opt ? 'checked' : '' }}
                                                   class="text-indigo-500 focus:ring-indigo-400 border-white/20 bg-white/10">
                                            <span class="text-sm text-white">{{ $opt }}</span>
                                        </label>
                                    @endforeach
                                </div>

                            @elseif ($q->question_type === 'yes_no')
                                <div class="flex items-center gap-4">
                                    <label class="flex items-center gap-2 p-3 rounded-xl bg-white/5 border border-white/10 cursor-pointer hover:bg-white/10 transition">
                                        <input type="radio" :name="'answers[{{ $q->id }}]'" value="yes"
                                               x-model="answers[{{ $q->id }}]"
                                               :required="isVisible({{ $q->id }}) && {{ $isReq }}"
                                               {{ old('answers.' . $q->id) === 'yes' ? 'checked' : '' }}
                                               class="text-indigo-500 focus:ring-indigo-400 border-white/20 bg-white/10">
                                        <span class="text-sm text-white">Yes</span>
                                    </label>
                                    <label class="flex items-center gap-2 p-3 rounded-xl bg-white/5 border border-white/10 cursor-pointer hover:bg-white/10 transition">
                                        <input type="radio" :name="'answers[{{ $q->id }}]'" value="no"
                                               x-model="answers[{{ $q->id }}]"
                                               :required="isVisible({{ $q->id }}) && {{ $isReq }}"
                                               {{ old('answers.' . $q->id) === 'no' ? 'checked' : '' }}
                                               class="text-indigo-500 focus:ring-indigo-400 border-white/20 bg-white/10">
                                        <span class="text-sm text-white">No</span>
                                    </label>
                                </div>
                            @endif

                            @error('answers.' . $q->id)
                                <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach
                @else
                    {{-- Fallback if no questions set --}}
                    <div class="mb-5">
                        <p class="text-sm font-semibold text-indigo-200 mb-2">Rating</p>
                        <div class="flex items-center gap-2" x-data="{ rating: {{ old('rating', 0) }} }">
                            @for ($i = 1; $i <= 5; $i++)
                                <button type="button" @click="rating = {{ $i }}"
                                        class="p-1 transition hover:scale-110 focus:outline-none">
                                    <svg class="w-8 h-8" :class="rating >= {{ $i }} ? 'text-yellow-400' : 'text-white/20'" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                            @endfor
                            <input type="hidden" name="rating" x-model="rating" value="{{ old('rating', 0) }}">
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="message" class="block text-sm font-semibold text-indigo-200 mb-1.5">Message</label>
                        <textarea name="message" id="message" rows="4"
                                  class="w-full px-4 py-2.5 text-sm text-white bg-white/5 border border-white/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/50 focus:border-indigo-400 placeholder-indigo-300/40 transition resize-none"
                                  placeholder="Share your thoughts about this session...">{{ old('message') }}</textarea>
                    </div>
                @endif

                <button type="submit"
                        class="w-full py-3 px-6 text-sm font-bold text-white bg-gradient-to-r from-indigo-500 to-purple-500 rounded-xl hover:from-indigo-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-indigo-400/50 transition shadow-lg shadow-indigo-500/25">
                    Submit Feedback
                </button>
            </form>
        </div>
    </div>

    <style>[x-cloak] { display: none !important; }</style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        function feedbackForm() {
            const parentMap = @json($parentMap ?? []);
            const triggerMap = @json($questions->mapWithKeys(fn($q) => [$q->id => $q->trigger_value])->toArray() ?? []);
            const ratingKeys = @json($questions->where('question_type', 'rating')->pluck('id')->toArray() ?? []);

            // Build reverse map: parent_id → [child_ids]
            const childrenOf = {};
            Object.entries(parentMap).forEach(([childId, parentId]) => {
                if (!childrenOf[parentId]) childrenOf[parentId] = [];
                childrenOf[parentId].push(parseInt(childId));
            });

            return {
                answers: {},
                init() {
                    // Initialize all answers from old() values via hidden inputs
                    const self = this;
                    document.querySelectorAll('input[type=hidden][name^="answers["]').forEach(el => {
                        const match = el.name.match(/answers\[(\d+)\]/);
                        if (match) {
                            const id = parseInt(match[1]);
                            if (el.value) self.answers[id] = el.value;
                        }
                    });
                    // Initialize radio buttons
                    document.querySelectorAll('input[type=radio][name^="answers["]:checked').forEach(el => {
                        const match = el.name.match(/answers\[(\d+)\]/);
                        if (match) self.answers[parseInt(match[1])] = el.value;
                    });
                },
                isVisible(qId) {
                    const parentId = parentMap[qId];
                    if (!parentId) return true; // no parent = always visible
                    const parentAnswer = this.answers[parentId];
                    const triggerValue = triggerMap[qId];
                    return parentAnswer === triggerValue;
                },
                setRating(qId, value) {
                    this.answers[qId] = value;
                },
                getRating(qId) {
                    return this.answers[qId] || 0;
                },
            }
        }
    </script>
</body>
</html>
