<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Agenda — <?php echo e(config('app.name')); ?></title>
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
<?php echo $__env->make('admin.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<main class="flex-1 lg:ml-64">
<header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
        <div><h1 class="text-lg font-bold text-gray-900">Manage Agenda</h1><p class="text-xs text-gray-500">Visual schedule editor — click to edit any cell</p></div>
        <a href="<?php echo e(route('admin.agenda.create')); ?>" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium bg-indigo-500 text-white rounded-xl hover:bg-indigo-600 shadow-sm shadow-indigo-200 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>Add Item</a>
    </div>
</header>
<div class="p-4 sm:p-6 lg:p-8">
    <?php echo $__env->make('admin.partials.notification', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 overflow-x-auto">
        <table class="agenda-table w-full">
            <thead>
                <tr>
                    <th rowspan="2">Time</th>
                    <?php $floorGroups = $rooms->groupBy(fn($r) => $r->floorRelation?->name ?? 'Other'); ?>
                    <?php $__currentLoopData = $floorGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $floorName => $floorRooms): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th colspan="<?php echo e($floorRooms->count()); ?>" style="background:<?php echo e($loop->first ? '#eef2ff' : '#fefce8'); ?>; color:<?php echo e($loop->first ? '#4338ca' : '#a16207'); ?>;"><?php echo e($floorName); ?></th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
                <tr>
                    <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <th><?php echo e($rm->name); ?></th>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tr>
            </thead>
            <tbody>
                <?php
                    $skipMap = [];
                    $roomNames = $rooms->pluck('name')->toArray();
                ?>
                <?php $__empty_1 = true; $__currentLoopData = $timeSlots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ts): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $slotKey = $ts->start_time . '-' . $ts->end_time;
                        $slotItems = collect($itemMap[$slotKey] ?? []);
                        $startT = $ts->start_time;
                        $endT   = $ts->end_time;
                        $fullRow = $slotItems->firstWhere(fn($i) => $i->isFullRow());
                        $hasPerRoom = $slotItems->contains(fn($i) => !$i->isFullRow());
                        // If there are per-room items, don't render as full-row even if a full-row exists
                        if ($hasPerRoom) $fullRow = null;
                    ?>
                    <tr>
                        <td class="time"><?php echo e($ts->label()); ?></td>

                        <?php if($fullRow): ?>
                            <td class="full" colspan="<?php echo e($rooms->count()); ?>">
                                <div class="cell-item items-center">
                                    <span class="cell-title">
                                        <?php $fullDisplay = $fullRow->workshop ? ($fullRow->workshop->name ?: $fullRow->workshop->title) : $fullRow->title; ?>
                                        <?php if($fullRow->category): ?>
                                            <span class="tag <?php echo e(\App\Models\AgendaItem::categoryClass($fullRow->category)); ?>"><?php echo e($fullDisplay); ?></span>
                                        <?php else: ?>
                                            <?php echo e($fullDisplay); ?>

                                        <?php endif; ?>
                                        <?php if($fullRow->feedback_enabled): ?>
                                            <span class="text-[9px] text-emerald-500 font-normal ml-1">📋</span>
                                        <?php endif; ?>
                                    </span>
                                    <div class="cell-actions justify-center">
                                        <a href="<?php echo e(route('admin.agenda.edit', $fullRow)); ?>" class="bg-amber-100 text-amber-700 hover:bg-amber-200">Edit</a>
                                        <form action="<?php echo e(route('admin.agenda.feedback.toggle', $fullRow)); ?>" method="POST" style="display:inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="<?php echo e($fullRow->feedback_enabled ? 'bg-emerald-100 text-emerald-600 hover:bg-emerald-200' : 'bg-gray-100 text-gray-400 hover:bg-gray-200'); ?>" style="font-size:10px;padding:2px 6px;border-radius:3px;border:none;cursor:pointer">
                                                <?php echo e($fullRow->feedback_enabled ? 'FB ON' : 'FB OFF'); ?>

                                            </button>
                                        </form>
                                        <a href="<?php echo e(route('admin.agenda.scan', $fullRow)); ?>" class="bg-purple-100 text-purple-700 hover:bg-purple-200">Scan QR</a>
                                        <form action="<?php echo e(route('admin.agenda.destroy', $fullRow)); ?>" method="POST" onsubmit="return confirm('Delete &quot;<?php echo e($fullRow->title); ?>&quot;?')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button class="bg-red-100 text-red-600 hover:bg-red-200">Delete</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="mt-1 flex justify-center gap-1 flex-wrap border-t border-dashed border-gray-200 pt-1">
                                    <?php $__currentLoopData = $roomNames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rmName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(!$slotItems->firstWhere('room', $rmName) && !isset($skipMap[$rmName])): ?>
                                            <a href="<?php echo e(route('admin.agenda.create', ['room' => $rmName, 'start_time' => $startT, 'end_time' => $endT])); ?>" class="btn-add-cell" title="Add to <?php echo e($rmName); ?>">+ <?php echo e($rmName); ?></a>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </td>
                        <?php else: ?>
                            <?php
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
                                        $displayTitle = $item->workshop ? ($item->workshop->name ?: $item->workshop->title) : $item->title;
                                        $tag = $item->category ? '<span class="tag ' . \App\Models\AgendaItem::categoryClass($item->category) . '">' . e($displayTitle) . '</span>' : e($displayTitle);
                                        $badge = '';
                                        if ($item->rowspan > 1) $badge .= ' <span class="text-[9px] text-indigo-400 font-normal">↕x' . $item->rowspan . '</span>';
                                        if ($item->colspan > 1) $badge .= ' <span class="text-[9px] text-amber-500 font-normal">↔x' . $item->colspan . '</span>';
                                        $mergeRight = '<form action="' . route('admin.agenda.merge', [$item, 'dir' => 'right']) . '" method="POST" style="display:inline">' . csrf_field() . '<button type="submit" class="btn-merge bg-amber-100 text-amber-600 hover:bg-amber-200" title="Merge right (colspan+1)">→</button></form>';
                                        $mergeDown  = '<form action="' . route('admin.agenda.merge', [$item, 'dir' => 'down']) . '" method="POST" style="display:inline">' . csrf_field() . '<button type="submit" class="btn-merge bg-indigo-100 text-indigo-600 hover:bg-indigo-200" title="Merge down (rowspan+1)">↓</button></form>';
                                        $unRight = $item->colspan > 1 ? '<form action="' . route('admin.agenda.merge', [$item, 'dir' => 'unright']) . '" method="POST" style="display:inline">' . csrf_field() . '<button type="submit" class="btn-merge bg-amber-50 text-amber-400 hover:bg-amber-100" title="Unmerge right">←</button></form>' : '';
                                        $unDown  = $item->rowspan > 1 ? '<form action="' . route('admin.agenda.merge', [$item, 'dir' => 'undown']) . '" method="POST" style="display:inline">' . csrf_field() . '<button type="submit" class="btn-merge bg-indigo-50 text-indigo-400 hover:bg-indigo-100" title="Unmerge down">↑</button></form>' : '';

                                        $fbOn = $item->feedback_enabled;
                                        $fbToggle = '<form action="' . route('admin.agenda.feedback.toggle', $item) . '" method="POST" style="display:inline">' . csrf_field() . '<button type="submit" class="' . ($fbOn ? 'bg-emerald-100 text-emerald-600 hover:bg-emerald-200' : 'bg-gray-100 text-gray-400 hover:bg-gray-200') . '" style="display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;font-size:9px;border-radius:3px;cursor:pointer;transition:0.15s;text-decoration:none;border:none" title="' . ($fbOn ? 'Feedback ON - click to turn off' : 'Feedback OFF - click to turn on') . '">' . ($fbOn ? 'FB' : 'fb') . '</button></form>';

                                        $cells[] = '<td' . $attrs . '>
                                            <div class="cell-item">
                                                <span class="cell-title">' . $tag . $badge . ($fbOn ? ' <span class="text-[9px] text-emerald-500 font-normal">📋</span>' : '') . '</span>
                                                <div class="cell-actions">
                                                    <a href="' . route('admin.agenda.edit', $item) . '" class="bg-amber-100 text-amber-700 hover:bg-amber-200">Edit</a>
                                                    <form action="' . route('admin.agenda.destroy', $item) . '" method="POST" style="display:inline" onsubmit="return confirm(\'Delete &quot;' . e($item->title) . '&quot;?\')">
                                                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        <button class="bg-red-100 text-red-600 hover:bg-red-200">Delete</button>
                                                    </form>
                                                    <a href="' . route('admin.agenda.create', ['room' => $rm, 'start_time' => $startT, 'end_time' => $endT]) . '" class="bg-indigo-50 text-indigo-600 hover:bg-indigo-100">+</a>
                                                    ' . $unRight . $mergeRight . $unDown . $mergeDown . $fbToggle . '
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
                            ?>
                            <?php echo implode("\n", $cells); ?>

                        <?php endif; ?>
                    </tr>
                    <?php
                        // Decrement skip counters after row is processed
                        foreach ($skipMap as $rm => $rem) {
                            $skipMap[$rm] = $rem - 1;
                            if ($skipMap[$rm] <= 0) unset($skipMap[$rm]);
                        }
                    ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                
                <tr>
                    <td class="time">
                        <a href="<?php echo e(route('admin.agenda.create')); ?>" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium flex items-center justify-center gap-1 p-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            New
                        </a>
                    </td>
                    <td class="text-center text-gray-300" colspan="<?php echo e($rooms->count()); ?>">Click <strong class="text-indigo-500">+ Add Item</strong> above or pick a cell</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
</main>
</div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\2026-Testing\resources\views/admin/agenda/index.blade.php ENDPATH**/ ?>