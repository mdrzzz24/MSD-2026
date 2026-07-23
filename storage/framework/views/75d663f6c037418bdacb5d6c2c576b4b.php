<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Agenda Item — <?php echo e(config('app.name')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
</head>
<body class="bg-gray-50 font-sans antialiased">
<div class="flex min-h-screen">
<?php echo $__env->make('admin.partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<main class="flex-1 lg:ml-64">
<header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200"><div class="flex items-center h-16 px-4 sm:px-6 lg:px-8 gap-4"><a href="<?php echo e(route('admin.agenda.index')); ?>" class="inline-flex items-center gap-1.5 text-sm text-indigo-600 hover:text-indigo-800 font-medium transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>Agenda</a><span class="text-gray-300">/</span><h1 class="text-lg font-bold text-gray-900">Add Item</h1></div></header>
<div class="p-4 sm:p-6 lg:p-8"><div class="max-w-xl"><div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
    <?php if($errors->any()): ?>
        <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6"><svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><ul class="list-disc list-inside text-sm"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
    <?php endif; ?>
    <form action="<?php echo e(route('admin.agenda.store')); ?>" method="POST" class="space-y-4">
        <?php echo csrf_field(); ?>

        
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2.5">Input Mode</label>
            <div class="grid grid-cols-2 gap-3">
                <label class="relative flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all <?php $mode = old('_mode', 'scratch'); ?> <?php echo e($mode === 'scratch' ? 'border-indigo-500 bg-indigo-50 shadow-sm' : 'border-gray-200 bg-white hover:border-gray-300'); ?>">
                    <input type="radio" name="_mode" value="scratch" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500" <?php echo e($mode === 'scratch' ? 'checked' : ''); ?> onchange="toggleFormMode('scratch')">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Fill from Scratch</p>
                        <p class="text-xs text-gray-500 mt-0.5">Manually enter all agenda details</p>
                    </div>
                </label>
                <label class="relative flex items-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all <?php echo e($mode === 'existing' ? 'border-indigo-500 bg-indigo-50 shadow-sm' : 'border-gray-200 bg-white hover:border-gray-300'); ?>">
                    <input type="radio" name="_mode" value="existing" class="w-4 h-4 text-indigo-600 focus:ring-indigo-500" <?php echo e($mode === 'existing' ? 'checked' : ''); ?> onchange="toggleFormMode('existing')">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Use Existing Track / Workshop</p>
                        <p class="text-xs text-gray-500 mt-0.5">Pre-fill from a saved track or workshop</p>
                    </div>
                </label>
            </div>
            <div id="existingSourceSection" class="mt-3 p-4 bg-indigo-50 border border-indigo-200 rounded-xl <?php echo e($mode === 'existing' ? '' : 'hidden'); ?>">
                <label class="block text-sm font-semibold text-indigo-700 mb-2">Select Track or Workshop</label>
                <select id="existingSourceSelect" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500" onchange="onExistingSourceSelect(this)">
                    <option value="">— Select —</option>
                    <optgroup label="🗂️ Tracks">
                        <?php $__currentLoopData = $tracks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="track_<?php echo e($tr->id); ?>"
                            data-name="<?php echo e(e($tr->name)); ?>"
                            data-title="<?php echo e(e($tr->title)); ?>"
                            data-desc="<?php echo e(e($tr->description)); ?>"
                            data-type="track"><?php echo e($tr->name ?: $tr->title); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </optgroup>
                    <optgroup label="🔧 Workshops">
                        <?php $__currentLoopData = $workshops; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ws): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="workshop_<?php echo e($ws->id); ?>"
                            data-title="<?php echo e(e($ws->title)); ?>"
                            data-desc="<?php echo e(e($ws->description)); ?>"
                            data-type="workshop"
                            data-room="<?php echo e(e($ws->room ?? '')); ?>"
                            data-start="<?php echo e($ws->start_time); ?>"
                            data-end="<?php echo e($ws->end_time); ?>"
                            data-capacity="<?php echo e($ws->capacity); ?>"
                            data-regopen="<?php echo e($ws->registration_open ? '1' : '0'); ?>"><?php echo e($ws->name ?: $ws->title); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </optgroup>
                </select>
                <p class="text-xs text-indigo-500 mt-1.5">The selected data will auto-fill the form. You can still edit any field afterwards.</p>
            </div>
        </div>

        <div id="formFields" class="space-y-4 <?php $showForm = old('_mode') || $errors->any(); ?> <?php echo e($showForm ? '' : 'hidden'); ?>">
        <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Title</label><input type="text" name="title" id="inputTitle" value="<?php echo e(old('title')); ?>" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition"></div>

        <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Description</label><textarea name="description" id="inputDescription" rows="3" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition"><?php echo e(old('description')); ?></textarea></div>

        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Start Time</label><input type="time" name="start_time" id="inputStartTime" value="<?php echo e(old('start_time', request('start_time'))); ?>" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition"></div>
            <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">End Time</label><input type="time" name="end_time" id="inputEndTime" value="<?php echo e(old('end_time', request('end_time'))); ?>" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition"></div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Room</label>
                <select name="room" id="inputRoom" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition">
                    <option value="">— Full Row (all rooms) —</option>
                    <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($room->name); ?>" <?php echo e(old('room', request('room')) === $room->name ? 'selected' : ''); ?>><?php echo e($room->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Category</label>
                <select name="category" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition">
                    <option value="">— None —</option>
                    <option value="general" <?php echo e(old('category')==='general'?'selected':''); ?>>General</option>
                    <option value="workshop" <?php echo e(old('category')==='workshop'?'selected':''); ?>>Workshop</option>
                    <option value="platinum" <?php echo e(old('category')==='platinum'?'selected':''); ?>>Platinum</option>
                    <option value="gold" <?php echo e(old('category')==='gold'?'selected':''); ?>>Gold</option>
                    <option value="break" <?php echo e(old('category')==='break'?'selected':''); ?>>Break</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Sort Order</label><input type="number" name="order" value="<?php echo e(old('order', 0)); ?>" min="0" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition"></div>
            <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Rowspan <span class="text-xs text-gray-400 font-normal">(↓ merge rows)</span></label><input type="number" name="rowspan" value="<?php echo e(old('rowspan', 1)); ?>" min="1" max="12" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition"></div>
            <div><label class="block text-sm font-semibold text-gray-700 mb-1.5">Colspan <span class="text-xs text-gray-400 font-normal">(→ merge columns)</span></label><input type="number" name="colspan" value="<?php echo e(old('colspan', 1)); ?>" min="1" max="8" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition"></div>
        </div>

        
        <div class="border-t border-gray-100 pt-4 mt-2">
            <h3 class="text-sm font-bold text-gray-900 mb-3">Session Type & Registration</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Session Type</label>
                    <select name="agenda_type" id="agendaTypeSelect" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition">
                        <option value="">— None —</option>
                        <option value="track" <?php echo e(old('agenda_type')==='track'?'selected':''); ?>>Track</option>
                        <option value="workshop" <?php echo e(old('agenda_type')==='workshop'?'selected':''); ?>>Workshop</option>
                        <option value="keynote" <?php echo e(old('agenda_type')==='keynote'?'selected':''); ?>>Keynote</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Link to Track <span class="text-xs text-gray-400 font-normal">(optional)</span></label>
                    <?php $trackList = \App\Models\Track::orderBy('title')->get(); ?>
                    <select name="track_id" id="trackSelect" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition" onchange="onTrackSelect(this)">
                        <option value="">— None —</option>
                        <option value="__new__" style="font-weight:700;color:#4f46e5;">+ Create New Track</option>
                        <?php $__currentLoopData = $trackList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($tr->id); ?>" data-title="<?php echo e(e($tr->title)); ?>" data-desc="<?php echo e(e($tr->description)); ?>" <?php echo e(old('track_id')==$tr->id?'selected':''); ?>><?php echo e($tr->title); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <div id="newTrackFields" class="hidden mt-2 p-3 bg-indigo-50 border border-indigo-200 rounded-xl space-y-2">
                        <p class="text-xs font-semibold text-indigo-700">Create New Track</p>
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
                            <option value="<?php echo e($ws->id); ?>" data-title="<?php echo e(e($ws->title)); ?>" data-desc="<?php echo e(e($ws->description)); ?>" data-room="<?php echo e(e($ws->room ?? '')); ?>" data-start="<?php echo e($ws->start_time); ?>" data-end="<?php echo e($ws->end_time); ?>" data-capacity="<?php echo e($ws->capacity); ?>" <?php echo e(old('workshop_id')==$ws->id?'selected':''); ?>><?php echo e($ws->name ?: $ws->title); ?></option>
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
                    <input type="number" name="capacity" id="inputCapacity" value="<?php echo e(old('capacity', 0)); ?>" min="0" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 focus:bg-white transition">
                </div>
                <div class="flex items-end gap-4 pb-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="is_registrable" value="0">
                        <input type="checkbox" name="is_registrable" id="inputIsRegistrable" value="1" <?php echo e(old('is_registrable') ? 'checked' : ''); ?> class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm font-medium text-gray-700">Open for Registration</span>
                    </label>
                </div>
            </div>
        </div>

        
        <div class="border-t border-gray-100 pt-4 mt-2">
            <h3 class="text-sm font-bold text-gray-900 mb-3">Speaker & Content</h3>
            <?php $allSpeakers = \App\Models\Speaker::active()->orderBy('name')->get(); ?>
            <?php if($allSpeakers->isNotEmpty()): ?>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Assign Speakers</label>
                <div class="space-y-3 max-h-80 overflow-y-auto border border-gray-200 rounded-xl p-3 bg-gray-50">
                    <?php $__currentLoopData = $allSpeakers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-start gap-3 p-2 rounded-lg hover:bg-white transition">
                        <label class="flex items-center gap-2 cursor-pointer flex-shrink-0 pt-1">
                            <input type="checkbox" name="speaker_ids[]" value="<?php echo e($sp->id); ?>" <?php echo e(in_array($sp->id, old('speaker_ids', [])) ? 'checked' : ''); ?> class="w-4 h-4 rounded border-gray-300 text-indigo-600 speaker-check">
                        </label>
                        <div class="flex-1 min-w-0 space-y-2">
                            <p class="text-sm font-semibold text-gray-900"><?php echo e($sp->name); ?></p>
                            <?php if($sp->title || $sp->company): ?><p class="text-xs text-gray-500"><?php echo e($sp->title); ?><?php echo e($sp->company ? ' · '.$sp->company : ''); ?></p><?php endif; ?>
                            <input type="text" name="speaker_presentation_title[<?php echo e($sp->id); ?>]" value="<?php echo e(old('speaker_presentation_title.'.$sp->id)); ?>" placeholder="Presentation title..." class="w-full px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                            <textarea name="speaker_presentation_desc[<?php echo e($sp->id); ?>]" rows="2" placeholder="Presentation description..." class="w-full px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"><?php echo e(old('speaker_presentation_desc.'.$sp->id)); ?></textarea>
                            <textarea name="speaker_highlights[<?php echo e($sp->id); ?>]" rows="2" placeholder="Key highlights (one per line)..." class="w-full px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"><?php echo e(old('speaker_highlights.'.$sp->id)); ?></textarea>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <p class="text-xs text-gray-400 mt-1">Check speakers and add their session-specific key highlights. Manage speakers in <a href="<?php echo e(route('admin.speakers.index')); ?>" target="_blank" class="text-indigo-600 hover:underline">Speakers</a>.</p>
            </div>
            <?php else: ?>
            <p class="text-sm text-gray-400">No speakers available. <a href="<?php echo e(route('admin.speakers.index')); ?>" target="_blank" class="text-indigo-600 hover:underline">Add speakers first</a>.</p>
            <?php endif; ?>
        </div>

        <button type="submit" class="w-full py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg shadow-indigo-500/25 transition-all text-sm">Save Item</button>
        </div>
    </form>

<script>
// ── Existing: Workshop/Track inline create ──
function onWorkshopSelect(sel) {
    var newFields = document.getElementById('newWorkshopFields');
    var titleInput = document.getElementById('inputTitle');
    var descInput = document.getElementById('inputDescription');
    var startTime = document.getElementById('inputStartTime');
    var endTime = document.getElementById('inputEndTime');
    var room = document.getElementById('inputRoom');
    var capacity = document.getElementById('inputCapacity');
    var isReg = document.getElementById('inputIsRegistrable');

    if (sel.value === '__new__') {
        newFields.classList.remove('hidden');
    } else if (sel.value) {
        newFields.classList.add('hidden');
        var opt = sel.options[sel.selectedIndex];
        var wsTitle = opt.getAttribute('data-title');
        var wsDesc = opt.getAttribute('data-desc');
        var wsStart = opt.getAttribute('data-start');
        var wsEnd = opt.getAttribute('data-end');
        var wsRoom = opt.getAttribute('data-room');
        var wsCapacity = opt.getAttribute('data-capacity');
        if (wsTitle) titleInput.value = wsTitle;
        if (wsDesc) descInput.value = wsDesc;
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
    } else {
        newFields.classList.add('hidden');
    }
}
function onTrackSelect(sel) {
    var newFields = document.getElementById('newTrackFields');
    var titleInput = document.getElementById('inputTitle');
    var descInput = document.getElementById('inputDescription');

    if (sel.value === '__new__') {
        newFields.classList.remove('hidden');
    } else if (sel.value) {
        newFields.classList.add('hidden');
        var opt = sel.options[sel.selectedIndex];
        var trTitle = opt.getAttribute('data-title');
        var trDesc = opt.getAttribute('data-desc');
        if (trTitle) titleInput.value = trTitle;
        if (trDesc) descInput.value = trDesc;
    } else {
        newFields.classList.add('hidden');
    }
}

// ── Mode toggle ──
function toggleFormMode(mode) {
    var section = document.getElementById('existingSourceSection');
    var formFields = document.getElementById('formFields');
    if (mode === 'existing') {
        section.classList.remove('hidden');
        formFields.classList.add('hidden');
        document.getElementById('existingSourceSelect').value = '';
    } else {
        section.classList.add('hidden');
        formFields.classList.remove('hidden');
    }
}

// ── Existing source data pre-fill ──
function onExistingSourceSelect(sel) {
    var opt = sel.options[sel.selectedIndex];
    if (!opt || !opt.value) return;

    // Show the form fields
    document.getElementById('formFields').classList.remove('hidden');

    var titleInput = document.getElementById('inputTitle');
    var descInput = document.getElementById('inputDescription');
    var agendaType = document.getElementById('agendaTypeSelect');
    var startTime = document.getElementById('inputStartTime');
    var endTime = document.getElementById('inputEndTime');
    var room = document.getElementById('inputRoom');
    var capacity = document.getElementById('inputCapacity');
    var isReg = document.getElementById('inputIsRegistrable');

    var type = opt.getAttribute('data-type');
    var wsTitle = opt.getAttribute('data-title');
    var wsDesc = opt.getAttribute('data-desc');

    // Always fill title & description
    if (wsTitle) titleInput.value = wsTitle;
    if (wsDesc) descInput.value = wsDesc;

    if (type === 'track') {
        agendaType.value = 'track';
        // Don't clear time or room — keep pre-filled values from URL params
        capacity.value = '0';
        isReg.checked = false;
        // Also set the track_id dropdown to match
        var trackSelect = document.getElementById('trackSelect');
        var trackId = opt.value.replace('track_', '');
        for (var i = 0; i < trackSelect.options.length; i++) {
            if (trackSelect.options[i].value === trackId) {
                trackSelect.selectedIndex = i;
                break;
            }
        }
    } else if (type === 'workshop') {
        agendaType.value = 'workshop';
        var wsStart = opt.getAttribute('data-start');
        var wsEnd = opt.getAttribute('data-end');
        var wsRoom = opt.getAttribute('data-room');
        var wsCapacity = opt.getAttribute('data-capacity');
        var wsRegOpen = opt.getAttribute('data-regopen');

        if (wsStart) startTime.value = wsStart;
        if (wsEnd) endTime.value = wsEnd;
        if (wsRoom) {
            // Try to select matching room option
            for (var i = 0; i < room.options.length; i++) {
                if (room.options[i].value === wsRoom) {
                    room.selectedIndex = i;
                    break;
                }
            }
        }
        if (wsCapacity) capacity.value = wsCapacity;
        if (wsRegOpen === '1') {
            isReg.checked = true;
        }
        // Also set the workshop_id dropdown to match
        var workshopSelect = document.getElementById('workshopSelect');
        var workshopId = opt.value.replace('workshop_', '');
        for (var i = 0; i < workshopSelect.options.length; i++) {
            if (workshopSelect.options[i].value === workshopId) {
                workshopSelect.selectedIndex = i;
                break;
            }
        }
    }
}

// ── On load ──
document.addEventListener('DOMContentLoaded', function() {
    // Show form fields for the pre-selected mode
    var checkedMode = document.querySelector('input[name="_mode"]:checked');
    if (checkedMode) {
        toggleFormMode(checkedMode.value);
    }
    var sel = document.getElementById('workshopSelect');
    if (sel && sel.value && sel.value !== '__new__') onWorkshopSelect(sel);
    var tsel = document.getElementById('trackSelect');
    if (tsel && tsel.value && tsel.value !== '__new__') onTrackSelect(tsel);
});
</script>
</div></div></div>
</main>
</div>
</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/agenda/create.blade.php ENDPATH**/ ?>