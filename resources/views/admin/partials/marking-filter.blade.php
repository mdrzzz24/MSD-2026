{{--
    Collapsible multi-select Client Marking filter (approve / reject / waiting list).
    Checkboxes are named marking[]; values match registrants.client_remark_action.
--}}
@php
    $selectedMarkings = array_values(array_filter((array) request('marking', [])));
    $selectedCount = count($selectedMarkings);
    $markingOptions = [
        'approve'  => '✅ Approve',
        'reject'   => '❌ Reject',
        'waitlist' => 'Waiting List',
    ];
@endphp
<div>
    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Client Marking</label>
    <div class="relative filter-dropdown" data-empty-label="All markings">
        <button type="button" data-dropdown-toggle
                class="w-44 flex items-center justify-between gap-2 px-3 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
            <span class="text-gray-600 truncate" data-dropdown-label>{{ $selectedCount ? $selectedCount . ' selected' : 'All markings' }}</span>
            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div data-dropdown-panel class="absolute z-30 mt-1.5 w-64 bg-white border border-gray-200 rounded-xl shadow-lg hidden">
            <div class="flex items-center justify-between px-3 py-2 border-b border-gray-100">
                <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Select marking</span>
                <button type="button" data-dropdown-clear class="text-[10px] font-medium text-indigo-500 hover:text-indigo-700">Clear</button>
            </div>
            <div class="max-h-52 overflow-y-auto p-1.5">
                @foreach ($markingOptions as $value => $label)
                    <label class="flex items-center gap-2.5 px-2 py-1.5 rounded-lg hover:bg-gray-50 cursor-pointer select-none text-sm text-gray-700">
                        <input type="checkbox" name="marking[]" value="{{ $value }}"
                               @checked(in_array($value, $selectedMarkings, true))
                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        @if ($value === 'waitlist')
                            <svg class="w-3.5 h-3.5 text-orange-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        @endif
                        <span class="truncate">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>
</div>

@include('admin.partials.filter-dropdown-script')
