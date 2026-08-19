@forelse ($sessions as $s)
    @php
        $type = $s->agenda_type === 'workshop' || !empty($s->workshop_id) ? 'Workshop' : ($s->agenda_type === 'track' || !empty($s->track_id) ? 'Track' : 'General');
        $typeClass = $type === 'Workshop' ? 'bg-fuchsia-50 text-fuchsia-700 border-fuchsia-200' : ($type === 'Track' ? 'bg-sky-50 text-sky-700 border-sky-200' : 'bg-gray-100 text-gray-600 border-gray-200');
        $company = null;
        if (in_array($s->agenda_type, ['track', 'workshop'], true) || !empty($s->track_id) || !empty($s->workshop_id)) {
            $company = $s->workshop ? trim((string) $s->workshop->name) : null;
            if ((empty($company) || $company === '-') && $s->track) {
                $company = trim((string) ($s->track->name ?: $s->track->title));
            }
            if (empty($company) || $company === '-') { $company = null; }
        }
        $displayTitle = $company ? $company . ' - ' . $s->title : $s->title;
        $respondents = $s->feedback->map(fn ($fb) => $fb->registrant)->filter();
        $rowId = 'sess-' . $s->id;
    @endphp
    <tr class="hover:bg-gray-50/50 transition cursor-pointer session-main-row"
        onclick="toggleSessionDetail(event, '{{ $rowId }}')"
        data-expanded="0">
        <td class="px-5 py-3">
            <div class="flex items-start gap-2">
                <span class="session-chevron mt-0.5 text-gray-400 flex-shrink-0 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </span>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium border {{ $typeClass }}">{{ $type }}</span>
                        @if ($s->room)
                            <span class="text-xs text-gray-400">{{ $s->room }}</span>
                        @endif
                        <span class="text-xs text-gray-400">{{ $s->timeLabel() }}</span>
                    </div>
                    <p class="text-sm font-semibold text-gray-900 mt-1 truncate" title="{{ $displayTitle }}">{{ $displayTitle }}</p>
                </div>
            </div>
        </td>
        <td class="px-3 py-3">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                {{ $s->feedback_count }} response{{ $s->feedback_count === 1 ? '' : 's' }}
            </span>
        </td>
        <td class="px-3 py-3">
            <div class="flex flex-wrap gap-1">
                @forelse ($respondents->take(3) as $reg)
                    <a href="{{ route('admin.feedback-registrants.show', $reg) }}"
                       title="{{ $reg->display_name ?: $reg->name }} ({{ $reg->email }})"
                       onclick="event.stopPropagation()"
                       class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-50 text-gray-700 border border-gray-200 hover:bg-indigo-50 hover:text-indigo-700 hover:border-indigo-200 transition max-w-full">
                        <span class="w-4 h-4 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-[8px] font-bold flex-shrink-0">
                            {{ strtoupper(mb_substr($reg->display_name ?: $reg->name, 0, 1)) }}
                        </span>
                        <span class="truncate">{{ $reg->display_name ?: $reg->name }}</span>
                    </a>
                @empty
                    <span class="text-xs text-gray-400">—</span>
                @endforelse
                @if ($respondents->count() > 3)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-600">+{{ $respondents->count() - 3 }}</span>
                @endif
            </div>
        </td>
        <td class="px-3 py-3 hidden lg:table-cell">
            @php $last = $s->feedback->sortByDesc('created_at')->first(); @endphp
            <p class="text-xs text-gray-500">{{ $last && $last->created_at ? $last->created_at->format('d M Y, H:i') : '—' }}</p>
        </td>
        <td class="px-3 py-3 text-center">
            <a href="{{ route('feedback.form', $s) }}" target="_blank" rel="noopener" onclick="event.stopPropagation()"
               class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Feedback
            </a>
        </td>
    </tr>
    <tr class="hidden session-detail-row" id="{{ $rowId }}">
        <td colspan="5" class="px-5 py-4 bg-gray-50/60 border-t border-gray-100">
            <div class="space-y-3">
                @forelse ($s->feedback->sortByDesc('created_at') as $fb)
                    @php $reg = $fb->registrant; @endphp
                    <div class="bg-white rounded-xl border border-gray-100 p-4">
                        <div class="flex items-start justify-between gap-3 flex-wrap">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                                    {{ strtoupper(mb_substr($reg?->display_name ?: $reg?->name ?: ($fb->name ?: '?'), 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 truncate">
                                        {{ $reg?->display_name ?: $reg?->name ?: $fb->name }}
                                    </p>
                                    <p class="text-xs text-gray-500 truncate">{{ $fb->email }}</p>
                                    @if ($reg?->phone || $reg?->company)
                                        <p class="text-xs text-gray-400 truncate">
                                            {{ collect([$reg->phone, $reg->company])->filter()->implode(' · ') }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0 flex items-center gap-2">
                                <span class="text-[11px] text-gray-400">{{ $fb->created_at ? $fb->created_at->format('d M Y, H:i') : '' }}</span>
                                @if ($reg)
                                    <a href="{{ route('admin.feedback-registrants.show', $reg) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        View
                                    </a>
                                @endif
                            </div>
                        </div>

                        @if ($fb->answers->count() > 0)
                            <div class="mt-3 space-y-1.5 bg-gray-50 rounded-xl p-3">
                                @foreach ($fb->answers as $answer)
                                    @php $q = $answer->question; @endphp
                                    <div class="text-sm">
                                        <span class="text-xs font-semibold text-gray-500">{{ $q?->question_text ?? 'Answer' }}:</span>
                                        @if ($q?->question_type === 'rating')
                                            <span class="inline-flex items-center gap-0.5 ml-1 align-middle">
                                                @for ($i = 1; $i <= ($q->rating_max ?: 5); $i++)
                                                    <svg class="w-3.5 h-3.5 {{ $i <= (int) $answer->answer_value ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                                <span class="text-xs text-gray-400 ml-1">({{ (int) $answer->answer_value }}/{{ $q->rating_max ?: 5 }})</span>
                                            </span>
                                        @elseif ($q?->question_type === 'yes_no')
                                            <span class="ml-1 font-medium {{ strtolower((string) $answer->answer_value) === 'yes' ? 'text-emerald-600' : 'text-red-500' }}">
                                                {{ ucfirst((string) $answer->answer_value) }}
                                            </span>
                                        @elseif ($q?->question_type === 'multi_choice')
                                            <span class="text-gray-700 ml-1">
                                                @php $selected = json_decode($answer->answer_value, true); @endphp
                                                {{ is_array($selected) ? implode(', ', $selected) : $answer->answer_value }}
                                            </span>
                                        @else
                                            <span class="text-gray-700 ml-1">{{ $answer->answer_value }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-gray-400 mt-2">No answers recorded for this response.</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-4">No feedback responses for this session.</p>
                @endforelse
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="px-5 py-16 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            <p class="text-sm text-gray-400 mt-3">No sessions found with submitted feedback.</p>
        </td>
    </tr>
@endforelse
