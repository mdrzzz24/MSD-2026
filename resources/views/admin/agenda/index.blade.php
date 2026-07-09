<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Agenda — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
    <style>
        .agenda-table { border-collapse: collapse; min-width: 900px; font-size: 13px; }
        .agenda-table th, .agenda-table td { border: 1px solid #e5e7eb; padding: 6px 8px; vertical-align: top; }
        .agenda-table th { background: #f9fafb; font-weight: 600; text-align: center; color: #374151; white-space: nowrap; }
        .agenda-table .time { white-space: nowrap; font-weight: 600; color: #374151; width: 80px; text-align: center; background: #f9fafb; }
        .agenda-table .full { text-align: center; font-style: italic; color: #6b7280; background: #fafafa; }
        .agenda-table .cell-item { display: flex; flex-direction: column; gap: 2px; }
        .agenda-table .cell-title { font-weight: 600; font-size: 12px; line-height: 1.3; }
        .tag { display:inline-block; padding:2px 8px; border-radius:999px; font-size:10px; font-weight:600; white-space:nowrap; }
        .tag.plat { background:#e0e7ff; color:#3730a3; }
        .tag.gold { background:#fef3c7; color:#92400e; }
        .tag.ws { background:#dcfce7; color:#166534; }
        .tag-general { background:#dbeafe; color:#1e40af; }
        .tag-break { background:#f3f4f6; color:#4b5563; }
        .cell-actions { display: flex; gap: 4px; margin-top: 3px; flex-wrap: wrap; }
        .cell-actions a, .cell-actions button { font-size: 10px; padding: 1px 6px; border-radius: 4px; cursor: pointer; transition: 0.15s; text-decoration: none; }
        .btn-merge { display: inline-flex; align-items: center; justify-content: center; width: 18px; height: 18px; font-size: 10px; border-radius: 3px; cursor: pointer; transition: 0.15s; text-decoration: none; }
        .btn-add-cell { display: inline-flex; align-items: center; gap: 2px; font-size: 10px; padding: 2px 8px; border-radius: 4px; background: #eef2ff; color: #4f46e5; cursor: pointer; transition: 0.15s; text-decoration: none; }
        .btn-add-cell:hover { background: #e0e7ff; }
        .cell-empty { min-height: 48px; display: flex; align-items: center; justify-content: center; }
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased">
<div class="flex min-h-screen">
@include('admin.partials.sidebar')
<main class="flex-1 lg:ml-64">
<header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
        <div><h1 class="text-lg font-bold text-gray-900">Manage Agenda</h1><p class="text-xs text-gray-500">Visual schedule editor — click to edit any cell</p></div>
        <a href="{{ route('admin.agenda.create') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium bg-indigo-500 text-white rounded-xl hover:bg-indigo-600 shadow-sm shadow-indigo-200 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Add Item</a>
    </div>
</header>
<div class="p-4 sm:p-6 lg:p-8">
    @if (session('success'))
        <div class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl mb-6"><svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span class="text-sm">{!! session('success') !!}</span></div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 overflow-x-auto">
        <table class="agenda-table w-full">
            <thead>
                <tr>
                    <th rowspan="2">Time</th>
                    @php $floorGroups = $rooms->groupBy(fn($r) => $r->floorRelation?->name ?? 'Other'); @endphp
                    @foreach ($floorGroups as $floorName => $floorRooms)
                        <th colspan="{{ $floorRooms->count() }}" style="background:{{ $loop->first ? '#eef2ff' : '#fefce8' }}; color:{{ $loop->first ? '#4338ca' : '#a16207' }};">{{ $floorName }}</th>
                    @endforeach
                </tr>
                <tr>
                    @foreach ($rooms as $rm)
                        <th>{{ $rm->name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php
                    $skipMap = [];
                    $roomNames = $rooms->pluck('name')->toArray();
                @endphp
                @forelse ($timeSlots as $ts)
                    @php
                        $slotKey = $ts->start_time . '-' . $ts->end_time;
                        $slotItems = collect($itemMap[$slotKey] ?? []);
                        $startT = $ts->start_time;
                        $endT   = $ts->end_time;
                        $fullRow = $slotItems->firstWhere(fn($i) => $i->isFullRow());
                        $hasPerRoom = $slotItems->contains(fn($i) => !$i->isFullRow());
                        // If there are per-room items, don't render as full-row even if a full-row exists
                        if ($hasPerRoom) $fullRow = null;
                    @endphp
                    <tr>
                        <td class="time">{{ $ts->label() }}</td>

                        @if ($fullRow)
                            <td class="full" colspan="{{ $rooms->count() }}">
                                <div class="cell-item items-center">
                                    <span class="cell-title">
                                        @if ($fullRow->category)
                                            <span class="tag {{ \App\Models\AgendaItem::categoryClass($fullRow->category) }}">{{ $fullRow->title }}</span>
                                        @else
                                            {{ $fullRow->title }}
                                        @endif
                                    </span>
                                    <div class="cell-actions justify-center">
                                        <a href="{{ route('admin.agenda.edit', $fullRow) }}" class="bg-amber-100 text-amber-700 hover:bg-amber-200">Edit</a>
                                        @if ($fullRow->is_registrable)
                                        <form action="{{ route('admin.agenda.toggle-registration', $fullRow) }}" method="POST" style="display:inline">
                                            @csrf
                                            <button class="{{ $fullRow->registration_open ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-red-100 text-red-600 hover:bg-red-200' }}">{{ $fullRow->registration_open ? 'Open' : 'Closed' }}</button>
                                        </form>
                                        @endif
                                        <form action="{{ route('admin.agenda.destroy', $fullRow) }}" method="POST" onsubmit="return confirm('Delete &quot;{{ $fullRow->title }}&quot;?')">
                                            @csrf @method('DELETE')
                                            <button class="bg-red-100 text-red-600 hover:bg-red-200">Delete</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="mt-1 flex justify-center gap-1 flex-wrap border-t border-dashed border-gray-200 pt-1">
                                    @foreach ($roomNames as $rmName)
                                        @if (!$slotItems->firstWhere('room', $rmName) && !isset($skipMap[$rmName]))
                                            <a href="{{ route('admin.agenda.create', ['room' => $rmName, 'start_time' => $startT, 'end_time' => $endT]) }}" class="btn-add-cell" title="Add to {{ $rmName }}">+ {{ $rmName }}</a>
                                        @endif
                                    @endforeach
                                </div>
                            </td>
                        @else
                            @php
                                $cells = []; // collect cell HTML strings
                                $colCovered = []; // rooms covered by colspan
                                foreach ($roomNames as $rm) {
                                    if (isset($colCovered[$rm])) { unset($colCovered[$rm]); continue; }
                                    $item = $slotItems->firstWhere('room', $rm);
                                    if (isset($skipMap[$rm])) { continue; }

                                    if ($item) {
                                        $attrs = '';
                                        if ($item->rowspan > 1) {
                                            $attrs .= ' rowspan="' . $item->rowspan . '"';
                                            $skipMap[$rm] = $item->rowspan;
                                        }
                                        if ($item->colspan > 1) {
                                            $attrs .= ' colspan="' . $item->colspan . '"';
                                            $idx = array_search($rm, $roomNames);
                                            for ($i = 1; $i < $item->colspan; $i++) {
                                                if (isset($roomNames[$idx + $i])) {
                                                    $colCovered[$roomNames[$idx + $i]] = true;
                                                    if ($item->rowspan > 1) {
                                                        $skipMap[$roomNames[$idx + $i]] = $item->rowspan;
                                                    }
                                                }
                                            }
                                        }
                                        $tag = $item->category ? '<span class="tag ' . \App\Models\AgendaItem::categoryClass($item->category) . '">' . e($item->title) . '</span>' : e($item->title);
                                        $badge = '';
                                        if ($item->rowspan > 1) $badge .= ' <span class="text-[9px] text-indigo-400 font-normal">↕x' . $item->rowspan . '</span>';
                                        if ($item->colspan > 1) $badge .= ' <span class="text-[9px] text-amber-500 font-normal">↔x' . $item->colspan . '</span>';
                                        $mergeRight = '<form action="' . route('admin.agenda.merge', [$item, 'dir' => 'right']) . '" method="POST" style="display:inline">' . csrf_field() . '<button type="submit" class="btn-merge bg-amber-100 text-amber-600 hover:bg-amber-200" title="Merge right (colspan+1)">→</button></form>';
                                        $mergeDown  = '<form action="' . route('admin.agenda.merge', [$item, 'dir' => 'down']) . '" method="POST" style="display:inline">' . csrf_field() . '<button type="submit" class="btn-merge bg-indigo-100 text-indigo-600 hover:bg-indigo-200" title="Merge down (rowspan+1)">↓</button></form>';
                                        $unRight = $item->colspan > 1 ? '<form action="' . route('admin.agenda.merge', [$item, 'dir' => 'unright']) . '" method="POST" style="display:inline">' . csrf_field() . '<button type="submit" class="btn-merge bg-amber-50 text-amber-400 hover:bg-amber-100" title="Unmerge right">←</button></form>' : '';
                                        $unDown  = $item->rowspan > 1 ? '<form action="' . route('admin.agenda.merge', [$item, 'dir' => 'undown']) . '" method="POST" style="display:inline">' . csrf_field() . '<button type="submit" class="btn-merge bg-indigo-50 text-indigo-400 hover:bg-indigo-100" title="Unmerge down">↑</button></form>' : '';
                                        $toggleBtn = $item->is_registrable ? '<form action="' . route('admin.agenda.toggle-registration', $item) . '" method="POST" style="display:inline">' . csrf_field() . '<button class="' . ($item->registration_open ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-red-100 text-red-600 hover:bg-red-200') . '">' . ($item->registration_open ? 'Open' : 'Closed') . '</button></form>' : '';
                                        $cells[] = '<td' . $attrs . '>
                                            <div class="cell-item">
                                                <span class="cell-title">' . $tag . $badge . '</span>
                                                <div class="cell-actions">
                                                    <a href="' . route('admin.agenda.edit', $item) . '" class="bg-amber-100 text-amber-700 hover:bg-amber-200">Edit</a>
                                                    <form action="' . route('admin.agenda.destroy', $item) . '" method="POST" style="display:inline" onsubmit="return confirm(\'Delete &quot;' . e($item->title) . '&quot;?\')">
                                                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        <button class="bg-red-100 text-red-600 hover:bg-red-200">Delete</button>
                                                    </form>
                                                    ' . $toggleBtn . '
                                                    <a href="' . route('admin.agenda.create', ['room' => $rm, 'start_time' => $startT, 'end_time' => $endT]) . '" class="bg-indigo-50 text-indigo-600 hover:bg-indigo-100">+</a>
                                                    ' . $unRight . $mergeRight . $unDown . $mergeDown . '
                                                </div>
                                            </div>
                                        </td>';
                                    } else {
                                        $cells[] = '<td>
                                            <div class="cell-empty">
                                                <a href="' . route('admin.agenda.create', ['room' => $rm, 'start_time' => $startT, 'end_time' => $endT]) . '" class="btn-add-cell">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                                    Add
                                                </a>
                                            </div>
                                        </td>';
                                    }
                                }
                            @endphp
                            {!! implode("\n", $cells) !!}
                        @endif
                    </tr>
                    @php
                        // Decrement skip counters after row is processed
                        foreach ($skipMap as $rm => $rem) {
                            $skipMap[$rm] = $rem - 1;
                            if ($skipMap[$rm] <= 0) unset($skipMap[$rm]);
                        }
                    @endphp
                @endforeach

                {{-- Always show one extra blank row for adding new time slots --}}
                <tr>
                    <td class="time">
                        <a href="{{ route('admin.agenda.create') }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium flex items-center justify-center gap-1 p-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            New
                        </a>
                    </td>
                    <td class="text-center text-gray-300" colspan="{{ $rooms->count() }}">Click <strong class="text-indigo-500">+ Add Item</strong> above or pick a cell</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
</main>
</div>
</body>
</html>
