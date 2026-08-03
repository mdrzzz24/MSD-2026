{{--
    Collapsible multi-select Profile (job_title) filter.
    Expects: $profiles (Collection|array of profile names as strings).
    Checkboxes are named profile[] so the backend filter receives an array.
--}}
@php
    $selectedProfiles = array_values(array_filter((array) request('profile', [])));
    $selectedCount = count($selectedProfiles);
@endphp
<div>
    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Profile</label>
    <div class="relative filter-dropdown" data-empty-label="All profiles">
        <button type="button" data-dropdown-toggle
                class="w-44 flex items-center justify-between gap-2 px-3 py-2 text-sm border border-gray-200 rounded-xl bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
            <span class="text-gray-600 truncate" data-dropdown-label>{{ $selectedCount ? $selectedCount . ' selected' : 'All profiles' }}</span>
            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div data-dropdown-panel class="absolute z-30 mt-1.5 w-64 bg-white border border-gray-200 rounded-xl shadow-lg hidden">
            <div class="flex items-center justify-between px-3 py-2 border-b border-gray-100">
                <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide">Select profiles</span>
                <button type="button" data-dropdown-clear class="text-[10px] font-medium text-indigo-500 hover:text-indigo-700">Clear</button>
            </div>
            <div class="max-h-52 overflow-y-auto p-1.5">
                @foreach ($profiles as $p)
                    <label class="flex items-center gap-2.5 px-2 py-1.5 rounded-lg hover:bg-gray-50 cursor-pointer select-none text-sm text-gray-700">
                        <input type="checkbox" name="profile[]" value="{{ $p }}"
                               @checked(in_array($p, $selectedProfiles, true))
                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="truncate">{{ $p }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>
</div>

@include('admin.partials.filter-dropdown-script')
