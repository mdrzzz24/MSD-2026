<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Agenda Item — <?php echo e(config('app.name')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
</head>
<body class="bg-gray-50 font-sans antialiased">
<div class="flex min-h-screen">
<?php echo $__env->make('admin.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<main class="flex-1 lg:ml-64">
<header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200"><div class="flex items-center h-16 px-4 sm:px-6 lg:px-8 gap-4"><a href="<?php echo e(route('admin.agenda.index')); ?>" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>Agenda</a><span class="text-gray-300">/</span><h1 class="text-lg font-bold text-gray-900">Edit: <?php echo e($agendum->title); ?></h1></div></header>
<div class="p-4 sm:p-6 lg:p-8"><div class="max-w-xl"><div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
    <?php if($errors->any()): ?>
        <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6"><svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><ul class="list-disc list-inside text-sm"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
    <?php endif; ?>
    <form action="<?php echo e(route('admin.agenda.update', $agendum)); ?>" method="POST" class="space-y-4">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Title</label><input type="text" name="title" value="<?php echo e(old('title', $agendum->title)); ?>" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition"></div>

        <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Description</label><textarea name="description" rows="3" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition"><?php echo e(old('description', $agendum->description)); ?></textarea></div>

        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Start Time</label><input type="time" name="start_time" value="<?php echo e(old('start_time', $agendum->start_time)); ?>" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition"></div>
            <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">End Time</label><input type="time" name="end_time" value="<?php echo e(old('end_time', $agendum->end_time)); ?>" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition"></div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Room</label>
                <select name="room" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition">
                    <option value="">— Full Row (all rooms) —</option>
                    <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($room->name); ?>" <?php echo e(old('room', $agendum->room) === $room->name ? 'selected' : ''); ?>><?php echo e($room->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Category</label>
                <select name="category" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition">
                    <option value="">— None —</option>
                    <option value="general" <?php echo e(old('category', $agendum->category)==='general'?'selected':''); ?>>General</option>
                    <option value="workshop" <?php echo e(old('category', $agendum->category)==='workshop'?'selected':''); ?>>Workshop</option>
                    <option value="platinum" <?php echo e(old('category', $agendum->category)==='platinum'?'selected':''); ?>>Platinum</option>
                    <option value="gold" <?php echo e(old('category', $agendum->category)==='gold'?'selected':''); ?>>Gold</option>
                    <option value="break" <?php echo e(old('category', $agendum->category)==='break'?'selected':''); ?>>Break</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Sort Order</label><input type="number" name="order" value="<?php echo e(old('order', $agendum->order)); ?>" min="0" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition"></div>
            <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Rowspan <span class="text-xs text-gray-400 font-normal">(↓ merge rows)</span></label><input type="number" name="rowspan" value="<?php echo e(old('rowspan', $agendum->rowspan)); ?>" min="1" max="12" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition"></div>
            <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Colspan <span class="text-xs text-gray-400 font-normal">(→ merge columns)</span></label><input type="number" name="colspan" value="<?php echo e(old('colspan', $agendum->colspan)); ?>" min="1" max="8" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition"></div>
        </div>

        
        <div class="border-t border-gray-100 pt-4 mt-2">
            <h3 class="text-sm font-bold text-gray-900 mb-3">Session Type & Registration</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Session Type</label>
                    <select name="agenda_type" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition">
                        <option value="">— None —</option>
                        <option value="track" <?php echo e(old('agenda_type', $agendum->agenda_type)==='track'?'selected':''); ?>>Track</option>
                        <option value="workshop" <?php echo e(old('agenda_type', $agendum->agenda_type)==='workshop'?'selected':''); ?>>Workshop</option>
                        <option value="keynote" <?php echo e(old('agenda_type', $agendum->agenda_type)==='keynote'?'selected':''); ?>>Keynote</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Link to Track <span class="text-xs text-gray-400 font-normal">(optional)</span></label>
                    <?php $trackList = \App\Models\Track::orderBy('title')->get(); ?>
                    <select name="track_id" id="trackSelect" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition" onchange="onTrackSelect(this)">
                        <option value="">— None —</option>
                        <option value="__new__" style="font-weight:700;color:#4f46e5;">+ Create New Track</option>
                        <?php $__currentLoopData = $trackList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($tr->id); ?>" data-name="<?php echo e(e($tr->name)); ?>" data-title="<?php echo e(e($tr->title)); ?>" data-desc="<?php echo e(e($tr->description)); ?>" <?php echo e(old('track_id', $agendum->track_id)==$tr->id?'selected':''); ?>><?php echo e($tr->name ?: $tr->title); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <div id="newTrackFields" class="hidden mt-2 p-3 bg-indigo-50 border border-indigo-200 rounded-xl space-y-2">
                        <p class="text-xs font-semibold text-indigo-700">Create New Track</p>
                        <input type="text" name="new_track_name" placeholder="Track name..." class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                        <input type="text" name="new_track_title" placeholder="Track title..." class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                        <textarea name="new_track_desc" rows="2" placeholder="Track description..." class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500/20"></textarea>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Link to Workshop <span class="text-xs text-gray-400 font-normal">(optional)</span></label>
                    <?php $workshopList = \App\Models\Workshop::orderBy('title')->get(); ?>
                    <select name="workshop_id" id="workshopSelect" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition" onchange="onWorkshopSelect(this)">
                        <option value="">— None —</option>
                        <option value="__new__" style="font-weight:700;color:#4f46e5;">+ Create New Workshop</option>
                        <?php $__currentLoopData = $workshopList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ws): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($ws->id); ?>" data-title="<?php echo e(e($ws->title)); ?>" data-desc="<?php echo e(e($ws->description)); ?>" data-room="<?php echo e(e($ws->room ?? '')); ?>" data-start="<?php echo e($ws->start_time); ?>" data-end="<?php echo e($ws->end_time); ?>" data-capacity="<?php echo e($ws->capacity); ?>" <?php echo e(old('workshop_id', $agendum->workshop_id)==$ws->id?'selected':''); ?>><?php echo e($ws->name ?: $ws->title); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <div id="newWorkshopFields" class="hidden mt-2 p-3 bg-indigo-50 border border-indigo-200 rounded-xl space-y-2">
                        <p class="text-xs font-semibold text-indigo-700">Create New Workshop</p>
                        <input type="text" name="new_workshop_name" placeholder="Workshop name (appears in agenda)..." class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                        <input type="text" name="new_workshop_title" placeholder="Workshop title..." class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                        <textarea name="new_workshop_desc" rows="2" placeholder="Workshop description..." class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500/20"></textarea>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mt-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Capacity <span class="text-xs text-gray-400 font-normal">(0 = unlimited)</span></label>
                    <input type="number" name="capacity" value="<?php echo e(old('capacity', $agendum->capacity)); ?>" min="0" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition">
                </div>
                <div class="flex items-end gap-4 pb-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="is_registrable" value="0">
                        <input type="checkbox" name="is_registrable" value="1" <?php echo e(old('is_registrable', $agendum->is_registrable) ? 'checked' : ''); ?> class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm font-medium text-gray-700">Open for Registration</span>
                    </label>
                </div>
            </div>
        </div>

        
        <div class="border-t border-gray-100 pt-4 mt-2">
            <h3 class="text-sm font-bold text-gray-900 mb-3">Speaker & Content</h3>
            <?php
                $allSpeakers = \App\Models\Speaker::active()->orderBy('name')->get();
                $selectedSpeakers = old('speaker_ids', $agendum->speakers->pluck('id')->toArray());
                $pivotData = [];
                foreach ($agendum->speakers as $sp) {
                    $pivotData[$sp->id] = [
                        'key_highlights' => $sp->pivot->key_highlights ?? '',
                        'presentation_title' => $sp->pivot->presentation_title ?? '',
                        'presentation_description' => $sp->pivot->presentation_description ?? '',
                    ];
                }
            ?>
            <?php if($allSpeakers->isNotEmpty()): ?>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Assign Speakers</label>
                <div class="space-y-3 max-h-80 overflow-y-auto border border-gray-200 rounded-xl p-3 bg-gray-50">
                    <?php $__currentLoopData = $allSpeakers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-start gap-3 p-2 rounded-lg hover:bg-white transition">
                        <label class="flex items-center gap-2 cursor-pointer flex-shrink-0 pt-1">
                            <input type="checkbox" name="speaker_ids[]" value="<?php echo e($sp->id); ?>" <?php echo e(in_array($sp->id, $selectedSpeakers) ? 'checked' : ''); ?> class="w-4 h-4 rounded border-gray-300 text-indigo-600 speaker-check">
                        </label>
                        <div class="flex-1 min-w-0 space-y-2">
                            <p class="text-sm font-semibold text-gray-900"><?php echo e($sp->name); ?></p>
                            <?php if($sp->title || $sp->company): ?><p class="text-xs text-gray-500"><?php echo e($sp->title); ?><?php echo e($sp->company ? ' · '.$sp->company : ''); ?></p><?php endif; ?>
                            <input type="text" name="speaker_presentation_title[<?php echo e($sp->id); ?>]" value="<?php echo e(old('speaker_presentation_title.'.$sp->id, $pivotData[$sp->id]['presentation_title'] ?? '')); ?>" placeholder="Presentation title..." class="w-full px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                            <textarea name="speaker_presentation_desc[<?php echo e($sp->id); ?>]" rows="2" placeholder="Presentation description..." class="w-full px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"><?php echo e(old('speaker_presentation_desc.'.$sp->id, $pivotData[$sp->id]['presentation_description'] ?? '')); ?></textarea>
                            <textarea name="speaker_highlights[<?php echo e($sp->id); ?>]" rows="2" placeholder="Key highlights (one per line)..." class="w-full px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"><?php echo e(old('speaker_highlights.'.$sp->id, $pivotData[$sp->id]['key_highlights'] ?? '')); ?></textarea>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <p class="text-xs text-gray-400 mt-1">Manage speakers in <a href="<?php echo e(route('admin.speakers.index')); ?>" target="_blank" class="text-indigo-600 hover:underline">Speakers</a>.</p>
            </div>
            <?php endif; ?>
        </div>

        <button type="submit" class="w-full py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/25 transition-all text-sm">Update Item</button>
    </form>

<script>
function onWorkshopSelect(sel) {
    var newFields = document.getElementById('newWorkshopFields');
    var titleInput = document.querySelector('[name="title"]');
    var descInput = document.querySelector('[name="description"]');
    var startTime = document.querySelector('[name="start_time"]');
    var endTime = document.querySelector('[name="end_time"]');
    var room = document.querySelector('[name="room"]');
    var capacity = document.querySelector('[name="capacity"]');
    var agendaType = document.querySelector('[name="agenda_type"]');
    if (sel.value === '__new__') { newFields.classList.remove('hidden'); }
    else if (sel.value) {
        newFields.classList.add('hidden');
        var opt = sel.options[sel.selectedIndex];
        if (opt.getAttribute('data-title')) titleInput.value = opt.getAttribute('data-title');
        if (opt.getAttribute('data-desc')) descInput.value = opt.getAttribute('data-desc');
        var wsStart = opt.getAttribute('data-start');
        var wsEnd = opt.getAttribute('data-end');
        var wsRoom = opt.getAttribute('data-room');
        var wsCapacity = opt.getAttribute('data-capacity');
        if (wsStart) startTime.value = wsStart;
        if (wsEnd) endTime.value = wsEnd;
        if (wsRoom) {
            for (var i = 0; i < room.options.length; i++) {
                if (room.options[i].value === wsRoom) {
                    room.selectedIndex = i;
                    break;
                }
            }
        }
        if (wsCapacity) capacity.value = wsCapacity;
        if (agendaType) agendaType.value = 'workshop';
    } else { newFields.classList.add('hidden'); }
}
function onTrackSelect(sel) {
    var newFields = document.getElementById('newTrackFields');
    var titleInput = document.querySelector('[name="title"]');
    var descInput = document.querySelector('[name="description"]');
    var agendaType = document.querySelector('[name="agenda_type"]');
    if (sel.value === '__new__') { newFields.classList.remove('hidden'); }
    else if (sel.value) {
        newFields.classList.add('hidden');
        var opt = sel.options[sel.selectedIndex];
        if (opt.getAttribute('data-title')) titleInput.value = opt.getAttribute('data-title');
        if (opt.getAttribute('data-desc')) descInput.value = opt.getAttribute('data-desc');
        if (agendaType) agendaType.value = 'track';
    } else { newFields.classList.add('hidden'); }
}
</script>
</div></div></div>
</main>
</div>
</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/agenda/edit.blade.php ENDPATH**/ ?>