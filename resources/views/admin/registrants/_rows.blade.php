@forelse ($registrants as $r)
    <tr class="hover:bg-gray-50/50 transition search-row">
        @if (Auth::user()->canWrite())
        <td class="px-3 py-3">
            <input type="checkbox" class="registrant-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" value="{{ $r->id }}">
        </td>
        @endif
        <td class="px-3 py-4 max-w-0">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-[10px] font-bold flex-shrink-0">
                    {{ strtoupper(substr($r->name, 0, 1)) }}
                </div>
                <div class="min-w-0 truncate">
                    <a href="{{ route('admin.registrants.show', $r) }}" class="text-sm font-semibold text-gray-900 hover:text-indigo-600 transition search-name truncate block">
                        {{ $r->name }}
                    </a>
                    <p class="text-[11px] text-gray-500 truncate search-email">{{ $r->email }}</p>
                    @if ($r->phone)
                        <p class="text-[11px] text-gray-400 truncate">{{ $r->phone }}</p>
                    @endif
                </div>
            </div>
        </td>
        <td class="px-3 py-3 hidden xl:table-cell max-w-0">
            <div class="min-w-0 truncate">
                @if ($r->company || $r->job_title || $r->job_role)
                    @if ($r->company)
                        <p class="text-sm font-medium text-gray-800 truncate" title="{{ $r->company }}">{{ $r->company }}</p>
                    @endif
                    @if ($r->job_title)
                        <p class="text-[11px] text-gray-500 truncate" title="{{ $r->job_title }}">{{ $r->job_title }}</p>
                    @endif
                    @if ($r->job_role)
                        <p class="text-[11px] text-gray-400 truncate" title="{{ $r->job_role }}">{{ $r->job_role }}</p>
                    @endif
                @else
                    <span class="text-sm text-gray-400">—</span>
                @endif
            </div>
        </td>
        <td class="px-3 py-3 hidden sm:table-cell max-w-0">
            @if ($r->utm_source)
                <span class="inline-flex items-center gap-1 text-xs text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full truncate max-w-full" title="{{ $r->utm_source }}">
                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    <span class="truncate">{{ $r->utm_source }}</span>
                </span>
            @else
                <span class="text-xs text-gray-400">Direct</span>
            @endif
        </td>
        <td class="px-3 py-3">
            @if ($r->status === 'approved')
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 flex-shrink-0"></span> <span class="truncate">Approved</span>
                </span>
            @elseif ($r->status === 'rejected')
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 flex-shrink-0"></span> <span class="truncate">Rejected</span>
                </span>
            @else
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse flex-shrink-0"></span> <span class="truncate">Pending</span>
                </span>
            @endif
            @if (in_array($r->id, $remindedIds ?? [], true))
                <div class="mt-1.5">
                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-semibold bg-violet-50 text-violet-700 border border-violet-200" title="Gentle reminder has been sent to this registrant">
                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        Gentle Reminder
                    </span>
                </div>
            @endif
            @if ($r->hasClientRemark())
                <div class="mt-1.5 flex flex-col items-start gap-0.5">
                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-semibold border {{ $r->client_remark_action === 'approve' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($r->client_remark_action === 'reject' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-orange-50 text-orange-700 border-orange-200') }}">
                        @if ($r->client_remark_action === 'approve')
                            ✅ Marked Approve
                        @elseif ($r->client_remark_action === 'reject')
                            ❌ Marked Reject
                        @else
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Marked Waiting List
                            </span>
                        @endif
                        @if ($r->clientRemarkedBy)
                            <span class="font-normal text-gray-500">· {{ $r->clientRemarkedBy->name }}</span>
                        @endif
                    </span>
                    @if ($r->client_remark)
                        <span class="text-[10px] text-gray-500">{{ $r->client_remark }}</span>
                    @endif
                    @if ($r->client_remarked_at)
                        <span class="text-[10px] text-gray-400">{{ $r->client_remarked_at->copy()->addHours(7)->format('d M Y, H:i') }}</span>
                    @endif
                </div>
            @endif
        </td>
        <td class="px-3 py-3 hidden sm:table-cell">
            <span class="text-sm text-gray-500 whitespace-nowrap">{{ $r->created_at->copy()->addHours(7)->format('d M Y') }}</span>
        </td>
        <td class="px-3 py-3 text-center hidden sm:table-cell">
            @if ($r->email_logs_count > 0)
                <span class="inline-flex items-center gap-0.5 text-xs font-semibold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded-full" title="{{ $r->email_logs_count }} email(s) sent">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    {{ $r->email_logs_count }}
                </span>
            @else
                <span class="inline-flex items-center gap-0.5 text-xs text-gray-400" title="No emails sent">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>0</span>
                </span>
            @endif
        </td>
        <td class="px-3 py-3">
            <div class="flex items-center justify-center gap-1">
                <a href="{{ route('admin.registrants.show', $r) }}"
                   title="View"
                   class="p-1 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </a>
                @if (!Auth::user()->canWrite() && $r->isPending())
                @if (!$r->hasClientRemark())
                <div class="flex flex-col items-center gap-1">
                    <div class="flex items-center gap-1">
                        <button type="button" data-decision data-id="{{ $r->id }}" data-action="approve"
                                class="decision-toggle px-2 py-1 rounded-lg text-xs font-semibold transition whitespace-nowrap border border-gray-200 bg-gray-50 text-gray-500 hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-200" title="Mark as Approved">
                            ✅
                        </button>
                        <button type="button" data-decision data-id="{{ $r->id }}" data-action="reject"
                                class="decision-toggle px-2 py-1 rounded-lg text-xs font-semibold transition whitespace-nowrap border border-gray-200 bg-gray-50 text-gray-500 hover:bg-red-50 hover:text-red-600 hover:border-red-200" title="Mark as Rejected">
                            ❌
                        </button>
                        <button type="button" data-decision data-id="{{ $r->id }}" data-action="waitlist"
                                class="decision-toggle px-2 py-1 rounded-lg text-xs font-semibold transition whitespace-nowrap border border-gray-200 bg-gray-50 text-gray-500 hover:bg-orange-50 hover:text-orange-600 hover:border-orange-200" title="Mark as Waiting List">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </button>
                    </div>
                    <select data-reason data-id="{{ $r->id }}" class="decision-reason hidden mt-1 w-36 text-[10px] border border-gray-300 rounded-lg px-2 py-1 bg-white">
                        <option value="">— Reason —</option>
                        @foreach (config('client_reasons.reject') as $reason)
                            <option value="{{ $reason }}">{{ $reason }}</option>
                        @endforeach
                    </select>
                </div>
                @elseif ($r->client_remark_action === 'waitlist')
                <div class="flex flex-col items-center gap-1">
                    <div class="flex items-center gap-1">
                        <button type="button" onclick="changeWaitlistMark({{ $r->id }}, 'approve')"
                                class="px-2 py-1 rounded-lg text-xs font-semibold transition whitespace-nowrap border border-gray-200 bg-gray-50 text-gray-500 hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-200" title="Change Waiting List to Approved">
                            ✅
                        </button>
                        <button type="button" onclick="changeWaitlistMark({{ $r->id }}, 'reject')"
                                class="px-2 py-1 rounded-lg text-xs font-semibold transition whitespace-nowrap border border-gray-200 bg-gray-50 text-gray-500 hover:bg-red-50 hover:text-red-600 hover:border-red-200" title="Change Waiting List to Rejected">
                            ❌
                        </button>
                    </div>
                    <select data-wl-reason data-id="{{ $r->id }}" class="wl-reason hidden mt-1 w-36 text-[10px] border border-gray-300 rounded-lg px-2 py-1 bg-white">
                        <option value="">— Reason —</option>
                        @foreach (config('client_reasons.reject') as $reason)
                            <option value="{{ $reason }}">{{ $reason }}</option>
                        @endforeach
                    </select>
                </div>
                @else
                <span class="text-[10px] text-gray-400">Already marked</span>
                @endif
                @endif
                @if (Auth::user()->canWrite())
                <a href="{{ route('admin.registrants.edit', $r) }}"
                   title="Edit"
                   class="p-1 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </a>
                {{-- Approve - always available --}}
                <form action="{{ route('admin.registrants.approve', $r) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('Approve {{ addslashes($r->name) }}?')"
                            title="Approve"
                            class="p-1 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </button>
                </form>
                {{-- Reject - always available --}}
                <button onclick="openRejectModal('{{ $r->id }}', '{{ addslashes($r->name) }}')"
                        title="Reject"
                        class="p-1 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                {{-- Resend credentials - only for approved --}}
                @if ($r->status === 'approved')
                    <button onclick="resendCredentials('{{ $r->id }}', '{{ addslashes($r->name) }}')"
                            title="Resend"
                            class="p-1 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </button>
                @endif
                <form action="{{ route('admin.registrants.destroy', $r) }}" method="POST" class="inline" onsubmit="return confirm('Delete {{ addslashes($r->name) }} permanently?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            title="Delete"
                            class="p-1 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </form>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="px-5 py-16 text-center">
            <div class="flex flex-col items-center gap-2">
                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p class="text-gray-400 font-medium">No registrants found</p>
                <p class="text-xs text-gray-400">No registrants match the current filter</p>
            </div>
        </td>
    </tr>
@endforelse
