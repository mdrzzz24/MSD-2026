<!DOCTYPE html>
<html lang="en">
@php
    // For Track / Workshop sessions, show the company (vendor) name before the title: "{company} - {title}".
    $fbCompany = null;
    if (in_array($agendum->agenda_type, ['track', 'workshop'], true) || !empty($agendum->track_id) || !empty($agendum->workshop_id)) {
        $fbName = null;
        if ($agendum->workshop) {
            $fbName = trim((string) $agendum->workshop->name);
        }
        if ((empty($fbName) || $fbName === '-') && $agendum->track) {
            $fbName = trim((string) ($agendum->track->name ?: $agendum->track->title));
        }
        if (!empty($fbName) && $fbName !== '-') {
            $fbCompany = $fbName;
        }
    }
    $fbTitle = $fbCompany ? $fbCompany . ' - ' . $agendum->title : $agendum->title;
@endphp
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Feedback — {{ $fbTitle }} — MSD 2026</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
    @if ($needsLogin)
        <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    @endif
</head>
<body class="bg-[#060b26] font-sans antialiased min-h-screen">
    {{-- Decorative background --}}
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-950 via-[#0a1140] to-[#04081f]"></div>
        <div class="absolute -top-40 -left-32 w-[480px] h-[480px] rounded-full bg-indigo-600/20 blur-3xl"></div>
        <div class="absolute top-1/3 -right-40 w-[420px] h-[420px] rounded-full bg-purple-600/20 blur-3xl"></div>
        <div class="absolute bottom-0 left-1/3 w-[360px] h-[360px] rounded-full bg-rose-500/10 blur-3xl"></div>
    </div>

    <div class="w-full max-w-2xl mx-auto px-4 sm:px-6 py-8 sm:py-12" x-data="{ showLogin: @json($needsLogin), authMode: 'password' }">
        {{-- Header --}}
        <div class="text-center mb-8">
            <img src="{{ asset('img/logo-msd.png') }}" alt="MSD 2026" style="height:44px;width:auto;filter:brightness(0) invert(1)" class="mx-auto mb-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-white">{{ $fbTitle }}</h1>
            <div class="flex items-center justify-center gap-2 mt-3 flex-wrap">
                @if ($agendum->start_time)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-white/5 border border-white/10 text-xs text-indigo-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $agendum->timeLabel() }}
                    </span>
                @endif
                @if ($agendum->room)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-white/5 border border-white/10 text-xs text-indigo-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        {{ $agendum->room }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Alert --}}
        @if (session('success'))
            <div class="mb-5 p-4 rounded-xl bg-emerald-500/15 border border-emerald-400/30 text-emerald-200 text-sm text-center font-medium">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-5 p-4 rounded-xl bg-red-500/15 border border-red-400/30 text-red-200 text-sm text-center font-medium">
                {{ session('error') }}
            </div>
        @endif

        @if ($needsLogin)
            {{-- Login required prompt card --}}
            <div class="bg-white/[0.06] backdrop-blur-xl rounded-3xl border border-white/10 shadow-2xl overflow-hidden">
                <div class="px-6 sm:px-8 py-12 text-center">
                    <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-white">Sign in to continue</h2>
                    <p class="text-sm text-indigo-200 mt-2 max-w-sm mx-auto">Please sign in with your registered email to view and submit your feedback for this session.</p>
                    <button type="button" @click="showLogin = true"
                            class="mt-6 inline-flex items-center gap-2 px-6 py-3 text-sm font-bold text-white rounded-full transition transform hover:-translate-y-0.5"
                            style="background:linear-gradient(135deg,#ff3d6e,#e91e63);box-shadow:0 8px 24px rgba(233,30,99,0.35);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        Sign In
                    </button>
                </div>
            </div>
        @else
        {{-- Form --}}
        <div class="bg-white/[0.06] backdrop-blur-xl rounded-3xl border border-white/10 shadow-2xl overflow-hidden" x-data="feedbackForm()">
            {{-- Registrant bar --}}
            <div class="flex items-center gap-3 px-6 sm:px-8 py-4 border-b border-white/10 bg-white/[0.03]">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                    {{ mb_strtoupper(mb_substr($registrant->display_name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-white truncate">{{ $registrant->display_name }}</p>
                    <p class="text-xs text-indigo-300 truncate">{{ $registrant->email }}</p>
                </div>
                <div class="ml-auto hidden sm:block">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-[11px] font-medium text-indigo-300">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Verified attendee
                    </span>
                </div>
            </div>

            @if ($existingFeedback)
                {{-- Already submitted: read-only summary --}}
                <div class="px-6 sm:px-8 py-6 sm:py-8">
                    <div class="mb-6 p-4 rounded-xl bg-emerald-500/15 border border-emerald-400/30 flex items-start gap-3">
                        <svg class="w-5 h-5 text-emerald-300 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <p class="text-sm font-semibold text-emerald-200">Thank you! You have already submitted your feedback for this session.</p>
                            <p class="text-xs text-emerald-200/70 mt-0.5">Submitted on {{ $existingFeedback->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>

                    @if ($existingFeedback->answers->count() > 0)
                        <p class="text-xs font-semibold text-indigo-300 uppercase tracking-widest mb-4">Your answers</p>
                        <div class="space-y-4">
                            @foreach ($questions as $q)
                                @php
                                    $answer = $existingFeedback->answers->firstWhere('agenda_item_question_id', $q->id);
                                    if (!$answer || $answer->answer_value === null || $answer->answer_value === '') continue;
                                    $display = $answer->answer_value;
                                    if ($q->question_type === 'multi_choice') {
                                        $decoded = json_decode($answer->answer_value, true);
                                        $display = is_array($decoded) ? implode(', ', $decoded) : $answer->answer_value;
                                    }
                                @endphp
                                <div class="p-5 rounded-2xl bg-white/[0.04] border border-white/10">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-white">{{ $q->question_text }}</p>
                                            <div class="mt-2">
                                                @if ($q->question_type === 'rating')
                                                    <div class="flex items-center gap-0.5">
                                                        @for ($i = 1; $i <= ($q->rating_max ?: 5); $i++)
                                                            <svg class="w-4 h-4 {{ $i <= (int) $answer->answer_value ? 'text-amber-400' : 'text-white/15' }}" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                            </svg>
                                                        @endfor
                                                        <span class="ml-2 text-xs text-amber-300/90 font-semibold">{{ $answer->answer_value }}/{{ $q->rating_max ?: 5 }}</span>
                                                    </div>
                                                @elseif ($q->question_type === 'yes_no')
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full {{ $answer->answer_value === 'yes' ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-400/30' : 'bg-rose-500/15 text-rose-300 border border-rose-400/30' }} text-xs font-semibold">
                                                        {{ ucfirst($answer->answer_value) }}
                                                    </span>
                                                @else
                                                    <p class="text-sm text-indigo-200 leading-relaxed">{{ $display }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-indigo-200">No answers were recorded for this submission.</p>
                    @endif
                </div>
            @else
            <form action="{{ route('feedback.store', $agendum) }}" method="POST" class="px-6 sm:px-8 py-6 sm:py-8">
                @csrf

                {{-- Progress --}}
                <div class="mb-6">
                    <div class="flex items-center justify-between text-xs text-indigo-300 mb-1.5">
                        <span class="font-medium">Progress</span>
                        <span x-text="answeredCount() + ' / ' + questionCount + ' answered'"></span>
                    </div>
                    <div class="h-1.5 rounded-full bg-white/10 overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r from-indigo-400 to-purple-400 transition-all duration-300"
                             :style="'width:' + (questionCount ? Math.min(100, Math.round((answeredCount() / questionCount) * 100)) : 0) + '%'"></div>
                    </div>
                </div>

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
                        <div class="mb-5 p-5 rounded-2xl border {{ $q->parent_question_id ? 'ml-3 sm:ml-5 border-l-2 border-l-indigo-400/50 bg-indigo-500/[0.06] border-white/10' : 'bg-white/[0.04] border-white/10' }}" x-show="isVisible({{ $q->id }})" x-cloak>
                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-white leading-snug">
                                    {{ $q->question_text }}
                                    @if ($q->required) <span class="text-rose-400">*</span> @endif
                                    @if ($q->parent_question_id)
                                        <span class="ml-1 inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-indigo-500/15 border border-indigo-400/30 text-[10px] font-medium text-indigo-300">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Based on your answer
                                        </span>
                                    @endif
                                </label>
                            </div>

                            @if ($q->question_type === 'text')
                                <textarea name="answers[{{ $q->id }}]" rows="3" x-model="answers[{{ $q->id }}]"
                                          :required="isVisible({{ $q->id }}) && {{ $isReq }}"
                                          class="w-full px-4 py-3 text-sm text-white bg-white/[0.04] border border-white/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400 placeholder-indigo-300/40 transition resize-none"
                                          placeholder="Type your answer here...">{{ old('answers.' . $q->id) }}</textarea>

                            @elseif ($q->question_type === 'rating')
                                @php $ratingMax = $q->rating_max ?: 5; $starSize = $ratingMax > 5 ? 'w-5 h-5' : 'w-7 h-7'; @endphp
                                <div class="rounded-2xl bg-white/[0.03] border border-white/10 p-4">
                                    <div class="flex flex-wrap items-center justify-center gap-1.5">
                                        @for ($i = 1; $i <= $ratingMax; $i++)
                                            <button type="button" @click="setRating({{ $q->id }}, {{ $i }})"
                                                    class="p-1 rounded-lg transition hover:scale-110 focus:outline-none focus:ring-2 focus:ring-indigo-400/40"
                                                    :title="'{{ $i }} / {{ $ratingMax }}'">
                                                <svg class="{{ $starSize }}" :class="getRating({{ $q->id }}) >= {{ $i }} ? 'text-amber-400 drop-shadow' : 'text-white/15 hover:text-white/30'" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            </button>
                                        @endfor
                                    </div>
                                    <div class="flex items-center justify-between mt-2.5 text-[11px] text-indigo-300/60">
                                        <span>1</span>
                                        <span class="font-semibold text-amber-300/90" x-text="(getRating({{ $q->id }}) || 0) + ' / {{ $ratingMax }}'"></span>
                                        <span>{{ $ratingMax }}</span>
                                    </div>
                                    <input type="hidden" :name="'answers[{{ $q->id }}]'" x-model="answers[{{ $q->id }}]" value="{{ old('answers.' . $q->id, 0) }}">
                                </div>

                            @elseif ($q->question_type === 'choice')
                                <div class="space-y-2.5">
                                    @foreach ($q->options ?? [] as $opt)
                                        <label class="flex items-center gap-3 px-4 py-3 rounded-xl border cursor-pointer transition"
                                               :class="isPicked({{ $q->id }}, $el.querySelector('input').value) ? 'border-indigo-400/60 bg-indigo-500/15' : 'border-white/10 bg-white/[0.04] hover:bg-white/[0.08]'">
                                            <input type="radio" :name="'answers[{{ $q->id }}]'" value="{{ $opt }}"
                                                   x-model="answers[{{ $q->id }}]"
                                                   :required="isVisible({{ $q->id }}) && {{ $isReq }}"
                                                   {{ old('answers.' . $q->id) === $opt ? 'checked' : '' }}
                                                   class="accent-indigo-500 w-4 h-4 flex-shrink-0">
                                            <span class="text-sm text-white">{{ $opt }}</span>
                                        </label>
                                    @endforeach
                                    @if ($q->allow_other)
                                        <label class="flex items-center gap-3 px-4 py-3 rounded-xl border cursor-pointer transition"
                                               :class="isPicked({{ $q->id }}, $el.querySelector('input').value) ? 'border-indigo-400/60 bg-indigo-500/15' : 'border-white/10 bg-white/[0.04] hover:bg-white/[0.08]'">
                                            <input type="radio" :name="'answers[{{ $q->id }}]'" value="__other__"
                                                   x-model="answers[{{ $q->id }}]"
                                                   :required="isVisible({{ $q->id }}) && {{ $isReq }}"
                                                   {{ old('answers.' . $q->id) === '__other__' ? 'checked' : '' }}
                                                   class="accent-indigo-500 w-4 h-4 flex-shrink-0">
                                            <span class="text-sm text-white">Other</span>
                                        </label>
                                        <div x-show="isOtherVisible({{ $q->id }})" x-cloak class="pl-2 pt-1">
                                            <input type="text" name="other_answers[{{ $q->id }}]"
                                                   x-model="otherTexts[{{ $q->id }}]"
                                                   value="{{ old('other_answers.' . $q->id) }}"
                                                   placeholder="Please specify..."
                                                   class="w-full px-4 py-2.5 text-sm text-white bg-white/[0.05] border border-indigo-400/40 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400 placeholder-indigo-300/40 transition">
                                        </div>
                                    @endif
                                </div>

                            @elseif ($q->question_type === 'multi_choice')
                                <div class="space-y-2.5">
                                    <p class="text-[11px] font-medium text-indigo-300/70 uppercase tracking-wide mb-1">Select all that apply</p>
                                    @foreach ($q->options ?? [] as $opt)
                                        <label class="flex items-center gap-3 px-4 py-3 rounded-xl border cursor-pointer transition"
                                               :class="isPicked({{ $q->id }}, $el.querySelector('input').value) ? 'border-indigo-400/60 bg-indigo-500/15' : 'border-white/10 bg-white/[0.04] hover:bg-white/[0.08]'">
                                            <input type="checkbox" name="answers[{{ $q->id }}][]" value="{{ $opt }}"
                                                   @change="toggleMulti({{ $q->id }}, $el.value)"
                                                   :checked="isSelected({{ $q->id }}, $el.value)"
                                                   {{ in_array($opt, (array) old('answers.' . $q->id, [])) ? 'checked' : '' }}
                                                   class="accent-indigo-500 w-4 h-4 flex-shrink-0 rounded">
                                            <span class="text-sm text-white">{{ $opt }}</span>
                                        </label>
                                    @endforeach
                                    @if ($q->allow_other)
                                        <label class="flex items-center gap-3 px-4 py-3 rounded-xl border cursor-pointer transition"
                                               :class="isPicked({{ $q->id }}, $el.querySelector('input').value) ? 'border-indigo-400/60 bg-indigo-500/15' : 'border-white/10 bg-white/[0.04] hover:bg-white/[0.08]'">
                                            <input type="checkbox" name="answers[{{ $q->id }}][]" value="__other__"
                                                   @change="toggleMulti({{ $q->id }}, $el.value)"
                                                   :checked="isSelected({{ $q->id }}, $el.value)"
                                                   {{ in_array('__other__', (array) old('answers.' . $q->id, [])) ? 'checked' : '' }}
                                                   class="accent-indigo-500 w-4 h-4 flex-shrink-0 rounded">
                                            <span class="text-sm text-white">Other</span>
                                        </label>
                                        <div x-show="isOtherVisible({{ $q->id }})" x-cloak class="pl-2 pt-1">
                                            <input type="text" name="other_answers[{{ $q->id }}]"
                                                   x-model="otherTexts[{{ $q->id }}]"
                                                   value="{{ old('other_answers.' . $q->id) }}"
                                                   placeholder="Please specify..."
                                                   class="w-full px-4 py-2.5 text-sm text-white bg-white/[0.05] border border-indigo-400/40 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400 placeholder-indigo-300/40 transition">
                                        </div>
                                    @endif
                                </div>

                            @elseif ($q->question_type === 'yes_no')
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="flex items-center justify-center gap-2 px-4 py-3 rounded-xl border cursor-pointer transition"
                                           :class="isPicked({{ $q->id }}, $el.querySelector('input').value) ? 'border-emerald-400/60 bg-emerald-500/15' : 'border-white/10 bg-white/[0.04] hover:bg-white/[0.08]'">
                                        <input type="radio" :name="'answers[{{ $q->id }}]'" value="yes"
                                               x-model="answers[{{ $q->id }}]"
                                               :required="isVisible({{ $q->id }}) && {{ $isReq }}"
                                               {{ old('answers.' . $q->id) === 'yes' ? 'checked' : '' }}
                                               class="accent-emerald-500 w-4 h-4 flex-shrink-0">
                                        <span class="text-sm font-medium text-white">Yes</span>
                                    </label>
                                    <label class="flex items-center justify-center gap-2 px-4 py-3 rounded-xl border cursor-pointer transition"
                                           :class="isPicked({{ $q->id }}, $el.querySelector('input').value) ? 'border-rose-400/60 bg-rose-500/15' : 'border-white/10 bg-white/[0.04] hover:bg-white/[0.08]'">
                                        <input type="radio" :name="'answers[{{ $q->id }}]'" value="no"
                                               x-model="answers[{{ $q->id }}]"
                                               :required="isVisible({{ $q->id }}) && {{ $isReq }}"
                                               {{ old('answers.' . $q->id) === 'no' ? 'checked' : '' }}
                                               class="accent-rose-500 w-4 h-4 flex-shrink-0">
                                        <span class="text-sm font-medium text-white">No</span>
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
                    <div class="mb-5 p-5 rounded-2xl bg-white/[0.04] border border-white/10">
                        <p class="text-sm font-semibold text-white mb-2">Rating</p>
                        <div class="flex items-center gap-2" x-data="{ rating: {{ old('rating', 0) }} }">
                            @for ($i = 1; $i <= 5; $i++)
                                <button type="button" @click="rating = {{ $i }}"
                                        class="p-1 transition hover:scale-110 focus:outline-none">
                                    <svg class="w-8 h-8" :class="rating >= {{ $i }} ? 'text-amber-400' : 'text-white/20'" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                            @endfor
                            <input type="hidden" name="rating" x-model="rating" value="{{ old('rating', 0) }}">
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="message" class="block text-sm font-semibold text-white mb-1.5">Message</label>
                        <textarea name="message" id="message" rows="4"
                                  class="w-full px-4 py-3 text-sm text-white bg-white/[0.04] border border-white/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400 placeholder-indigo-300/40 transition resize-none"
                                  placeholder="Share your thoughts about this session...">{{ old('message') }}</textarea>
                    </div>
                @endif

                <button type="submit"
                        class="w-full py-3.5 px-6 text-sm font-bold text-white bg-gradient-to-r from-indigo-500 to-purple-500 rounded-xl hover:from-indigo-600 hover:to-purple-600 focus:outline-none focus:ring-2 focus:ring-indigo-400/50 transition shadow-lg shadow-indigo-500/30 inline-flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Submit Feedback
                </button>
            </form>
            @endif
        </div>
        @endif

        {{-- Footer --}}
        <div class="text-center mt-8">
            <p class="text-[11px] tracking-wide text-indigo-300/50">© Copyright Metrodata. All Rights Reserved</p>
        </div>

        @if ($needsLogin)
            {{-- Login modal: password OR QR code (stays on the feedback page) --}}
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-show="showLogin" x-cloak>
                <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="showLogin = false; fbQrStop()"></div>
                <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl p-8 animate-modal-in">
                    <button type="button" @click="showLogin = false; fbQrStop()"
                            class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>

                    <div class="text-center mb-5">
                        <img src="{{ asset('img/logo-msd.png') }}" alt="MSD" style="height:40px;width:auto" class="mx-auto mb-3">
                        <h2 class="text-xl font-bold text-gray-900">Welcome Back</h2>
                        <p class="text-gray-500 text-sm" x-text="authMode === 'qr' ? 'Sign in by scanning your QR code' : 'Sign in to view and submit your feedback.'"></p>
                    </div>

                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-5 flex items-start gap-3 text-sm">
                            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    {{-- Tabs --}}
                    <div class="flex rounded-xl bg-gray-100 p-1 mb-6">
                        <button type="button" @click="authMode = 'password'; fbQrStop()"
                                class="flex-1 py-2 text-sm font-semibold rounded-lg transition"
                                :class="authMode === 'password' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700'">
                            Password
                        </button>
                        <button type="button" @click="authMode = 'qr'"
                                class="flex-1 py-2 text-sm font-semibold rounded-lg transition"
                                :class="authMode === 'qr' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700'">
                            QR Code
                        </button>
                    </div>

                    {{-- Password panel --}}
                    <div x-show="authMode === 'password'">
                        <form action="{{ route('login.attempt') }}" method="POST" class="space-y-5">
                            @csrf
                            <input type="hidden" name="redirect" value="{{ request()->getRequestUri() }}">

                            {{-- Email --}}
                            <div>
                                <label for="fb_email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email Address</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    </div>
                                    <input type="email" id="fb_email" name="email" value="{{ old('email') }}" required autofocus
                                           placeholder="email@example.com"
                                           class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition">
                                </div>
                            </div>

                            {{-- Password --}}
                            <div>
                                <label for="fb_password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    </div>
                                    <input type="password" id="fb_password" name="password" required placeholder="••••••••"
                                           class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition">
                                </div>
                            </div>

                            {{-- Remember --}}
                            <div class="flex items-center">
                                <input type="checkbox" id="fb_remember" name="remember"
                                       class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <label for="fb_remember" class="ml-2.5 text-sm text-gray-600">Remember me</label>
                            </div>

                            <button type="submit"
                                    class="w-full py-3 font-bold text-sm tracking-wide"
                                    style="background:linear-gradient(135deg,#ff3d6e,#e91e63);color:#fff;border-radius:999px;border:none;cursor:pointer;box-shadow:0 8px 24px rgba(233,30,99,0.35);transition:transform 0.25s,box-shadow 0.25s;"
                                    onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 12px 30px rgba(233,30,99,0.5)'"
                                    onmouseout="this.style.transform='';this.style.boxShadow='0 8px 24px rgba(233,30,99,0.35)'">
                                Sign In
                            </button>
                        </form>
                    </div>

                    {{-- QR panel --}}
                    <div x-show="authMode === 'qr'" x-cloak>
                        <div id="fbQrAlert" class="hidden"></div>

                        {{-- Step 1: email --}}
                        <div id="fbQrStepEmail" class="space-y-4">
                            <div>
                                <label for="fb_qr_email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email Address</label>
                                <div class="flex gap-2">
                                    <input type="email" id="fb_qr_email" placeholder="your@email.com" autocomplete="email"
                                           class="flex-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 focus:bg-white transition">
                                    <button type="button" id="fbQrVerifyBtn" onclick="fbQrVerifyEmail()"
                                            class="px-5 py-3 text-white text-sm font-bold rounded-full transition"
                                            style="background:linear-gradient(135deg,#ff3d6e,#e91e63);box-shadow:0 8px 24px rgba(233,30,99,0.35);">
                                        Next
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Step 2: scanner --}}
                        <div id="fbQrStepScanner" class="hidden">
                            <div class="bg-pink-50 rounded-xl border border-pink-100 p-3 mb-4 flex items-center gap-3">
                                <div id="fbQrAvatar" class="w-9 h-9 rounded-full flex items-center justify-center text-white font-bold text-sm" style="background:linear-gradient(135deg,#ff3d6e,#e91e63);"></div>
                                <div class="flex-1 min-w-0">
                                    <p id="fbQrName" class="text-sm font-semibold text-gray-900 truncate"></p>
                                    <p id="fbQrEmail" class="text-xs text-gray-500 truncate"></p>
                                </div>
                                <button type="button" onclick="fbQrReset()" class="text-gray-400 hover:text-gray-600 transition flex-shrink-0" title="Change email">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                            </div>
                            <div class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden">
                                <div id="fbQrReader" class="w-full"></div>
                            </div>
                            <p class="text-xs text-gray-400 text-center mt-3">Point your camera at the QR code to log in automatically</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <style>[x-cloak] { display: none !important; }
        @keyframes modalIn { from { opacity: 0; transform: translateY(12px) scale(.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
        .animate-modal-in { animation: modalIn .25s ease-out; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        function feedbackForm() {
            const parentMap = @json($parentMap ?? []);
            const triggerMap = @json($questions->mapWithKeys(fn($q) => [$q->id => $q->trigger_value])->toArray() ?? []);
            const ratingKeys = @json($questions->where('question_type', 'rating')->pluck('id')->toArray() ?? []);
            const multiKeys = @json($questions->where('question_type', 'multi_choice')->pluck('id')->toArray() ?? []);

            // Build reverse map: parent_id → [child_ids]
            const childrenOf = {};
            Object.entries(parentMap).forEach(([childId, parentId]) => {
                if (!childrenOf[parentId]) childrenOf[parentId] = [];
                childrenOf[parentId].push(parseInt(childId));
            });

            return {
                answers: {},
                otherTexts: {},
                questionCount: @json($questions->count()),
                answeredCount() {
                    let c = 0;
                    for (const k of Object.keys(this.answers)) {
                        const a = this.answers[k];
                        if (Array.isArray(a)) {
                            if (a.length > 0) c++;
                        } else if (a !== undefined && a !== null && a !== '' && a !== 0 && a !== '0') {
                            c++;
                        }
                    }
                    return c;
                },
                isPicked(qId, value) {
                    const a = this.answers[qId];
                    if (Array.isArray(a)) return a.includes(value);
                    return String(a ?? '') === String(value ?? '');
                },
                init() {
                    // Initialize scalar answers (e.g. rating hidden inputs)
                    const self = this;
                    document.querySelectorAll('input[type=hidden][name^="answers["]').forEach(el => {
                        const match = el.name.match(/^answers\[(\d+)\]$/);
                        if (match && el.value) self.answers[parseInt(match[1])] = el.value;
                    });
                    // Initialize radio buttons (choice / yes_no / other)
                    document.querySelectorAll('input[type=radio][name^="answers["]:checked').forEach(el => {
                        const match = el.name.match(/^answers\[(\d+)\]$/);
                        if (match) self.answers[parseInt(match[1])] = el.value;
                    });
                    // Initialize multi-choice arrays
                    multiKeys.forEach((id) => {
                        if (!Array.isArray(self.answers[id])) self.answers[id] = [];
                    });
                    document.querySelectorAll('input[type=checkbox][name^="answers["]:checked').forEach(el => {
                        const match = el.name.match(/^answers\[(\d+)\]\[\]$/);
                        if (match) {
                            const id = parseInt(match[1]);
                            if (!Array.isArray(self.answers[id])) self.answers[id] = [];
                            if (!self.answers[id].includes(el.value)) self.answers[id].push(el.value);
                        }
                    });
                    // Initialize "Other" free-text fields
                    document.querySelectorAll('input[name^="other_answers["]').forEach(el => {
                        const match = el.name.match(/^other_answers\[(\d+)\]$/);
                        if (match && el.value) self.otherTexts[parseInt(match[1])] = el.value;
                    });
                },
                isVisible(qId) {
                    const parentId = parentMap[qId];
                    if (!parentId) return true; // no parent = always visible
                    const parentAnswer = this.answers[parentId];
                    const triggerValue = String(triggerMap[qId] ?? '').trim().toLowerCase();
                    const norm = (v) => String(v ?? '').trim().toLowerCase();
                    if (Array.isArray(parentAnswer)) {
                        return parentAnswer.some((v) => norm(v) === triggerValue);
                    }
                    return norm(parentAnswer) === triggerValue;
                },
                setRating(qId, value) {
                    this.answers[qId] = value;
                },
                getRating(qId) {
                    return this.answers[qId] || 0;
                },
                toggleMulti(qId, value) {
                    if (!Array.isArray(this.answers[qId])) this.answers[qId] = [];
                    const arr = this.answers[qId].slice();
                    const idx = arr.indexOf(value);
                    if (idx > -1) arr.splice(idx, 1);
                    else arr.push(value);
                    this.answers[qId] = arr;
                },
                isSelected(qId, value) {
                    return Array.isArray(this.answers[qId]) && this.answers[qId].includes(value);
                },
                isOtherSelected(qId) {
                    const a = this.answers[qId];
                    if (Array.isArray(a)) return a.includes('__other__');
                    return a === '__other__';
                },
                isOtherVisible(qId) {
                    return this.isOtherSelected(qId);
                },
            }
        }
    </script>
    @if ($needsLogin)
    <script>
        // QR login embedded in the feedback modal (stays on this page).
        let fbQrReader = null;

        function fbQrShowAlert(type, text) {
            const box = document.getElementById('fbQrAlert');
            if (!box) return;
            box.className = 'mb-4 rounded-xl px-4 py-3 text-sm ' + (type === 'error' ? 'bg-red-50 border border-red-200 text-red-700' : 'bg-emerald-50 border border-emerald-200 text-emerald-700');
            box.textContent = text;
            box.classList.remove('hidden');
        }

        function fbQrStop() {
            if (fbQrReader) { fbQrReader.stop().catch(() => {}); fbQrReader = null; }
        }

        function fbQrReset() {
            fbQrStop();
            document.getElementById('fbQrStepScanner')?.classList.add('hidden');
            document.getElementById('fbQrStepEmail')?.classList.remove('hidden');
            document.getElementById('fbQrAlert')?.classList.add('hidden');
            const email = document.getElementById('fb_qr_email');
            if (email) email.value = '';
        }

        async function fbQrVerifyEmail() {
            const emailInput = document.getElementById('fb_qr_email');
            const email = emailInput ? emailInput.value.trim() : '';
            const btn = document.getElementById('fbQrVerifyBtn');
            if (!email) return fbQrShowAlert('error', 'Please enter your email address.');
            if (btn) { btn.disabled = true; btn.textContent = '...'; }
            try {
                const res = await fetch('{{ route('qr-login.verify-email') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ email })
                });
                const data = await res.json();
                if (data.success) {
                    document.getElementById('fbQrStepEmail')?.classList.add('hidden');
                    document.getElementById('fbQrName').textContent = data.name;
                    document.getElementById('fbQrEmail').textContent = email;
                    document.getElementById('fbQrAvatar').textContent = data.initial;
                    document.getElementById('fbQrStepScanner')?.classList.remove('hidden');
                    fbQrStartScanner();
                } else {
                    fbQrShowAlert('error', data.message || 'Verification failed.');
                }
            } catch (e) {
                fbQrShowAlert('error', 'Connection error. Please try again.');
            }
            if (btn) { btn.disabled = false; btn.textContent = 'Next'; }
        }

        function fbQrStartScanner() {
            if (typeof Html5Qrcode === 'undefined') { fbQrShowAlert('error', 'QR scanner unavailable. Please try again.'); return; }
            const reader = new Html5Qrcode('fbQrReader');
            fbQrReader = reader;
            reader.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: { width: 200, height: 200 }, aspectRatio: 1.0 },
                (decodedText) => {
                    reader.stop().catch(() => {});
                    fbQrAuthenticate(decodedText);
                },
                () => {}
            ).catch(() => {
                const el = document.getElementById('fbQrReader');
                if (el) el.innerHTML = '<p class="text-center text-gray-400 text-sm p-6">Camera access denied or unavailable.</p>';
            });
        }

        async function fbQrAuthenticate(code) {
            try {
                const res = await fetch('{{ route('qr-login.authenticate') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ scanned_code: code })
                });
                const data = await res.json();
                if (data.success) {
                    window.location.reload(); // stay on this page; now signed in
                } else {
                    fbQrShowAlert('error', data.message || 'Invalid QR code.');
                    fbQrStartScanner();
                }
            } catch (e) {
                fbQrShowAlert('error', 'Verification failed. Please try again.');
            }
        }
    </script>
    @endif
</body>
</html>
