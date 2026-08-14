<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Name Badges — <?php echo e(config('app.name')); ?></title>
<link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<style>
    * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    html, body { font-family: 'Inter', system-ui, sans-serif; background: #f3f4f6; margin: 0; }
    .toolbar { position: fixed; top: 0; left: 0; right: 0; z-index: 50; display: flex; align-items: center; gap: 12px; padding: 12px 20px; background: rgba(255,255,255,.95); backdrop-filter: blur(6px); border-bottom: 1px solid #e5e7eb; }
    .toolbar .title { font-weight: 700; color: #111827; font-size: 14px; }
    .toolbar .sub { color: #6b7280; font-size: 12px; }
    .btn { padding: 9px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; transition: background .15s; }
    .btn-primary { background: #4f46e5; color: #fff; border: none; }
    .btn-primary:hover { background: #4338ca; }
    .btn-ghost { background: #fff; color: #374151; border: 1px solid #e5e7eb; }
    .btn-ghost:hover { background: #f9fafb; }
    .sheet { padding: 96px 24px 24px; max-width: 210mm; margin: 0 auto; }
    .badge-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10mm; }
    .badge {
        border-radius: 14px; background: #fff; overflow: hidden;
        border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,.06);
        display: flex; flex-direction: column; page-break-inside: avoid; break-inside: avoid;
    }
    .badge-header {
        background: linear-gradient(135deg, #050d2a 0%, #12307a 55%, #1e4bb8 100%);
        color: #fff; padding: 9px 12px; display: flex; align-items: center; justify-content: space-between;
    }
    .badge-header .brand { display: flex; align-items: center; gap: 8px; }
    .badge-header .brand img { height: 26px; width: auto; filter: brightness(0) invert(1); }
    .badge-header .brand .evt { line-height: 1; }
    .badge-header .brand .evt .t { font-size: 13px; font-weight: 800; letter-spacing: .02em; }
    .badge-header .brand .evt .s { font-size: 8px; font-weight: 600; opacity: .8; letter-spacing: .14em; text-transform: uppercase; }
    .badge-header .year { font-size: 13px; font-weight: 800; background: rgba(255,255,255,.15); padding: 3px 9px; border-radius: 999px; }
    .badge-body { padding: 12px 12px 10px; flex: 1; display: flex; flex-direction: column; }
    .badge-name { font-size: 17px; font-weight: 800; color: #111827; line-height: 1.15; margin-bottom: 2px; word-break: break-word; }
    .badge-company { font-size: 11.5px; font-weight: 700; color: #1e4bb8; margin-bottom: 1px; }
    .badge-title { font-size: 10px; font-weight: 500; color: #6b7280; }
    .badge-foot { margin-top: auto; padding-top: 8px; display: flex; align-items: center; justify-content: space-between; border-top: 1px dashed #e5e7eb; }
    .badge-foot .qr img { width: 34px; height: 34px; }
    .badge-foot .meta { text-align: right; }
    .badge-foot .meta .code { font-size: 9px; font-weight: 700; color: #374151; font-family: ui-monospace, monospace; letter-spacing: .04em; }
    .badge-foot .meta .cap { font-size: 7px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: .12em; }
    .empty-state { background: #fff; border-radius: 16px; padding: 60px 20px; text-align: center; color: #6b7280; }

    /* ── Print ── */
    @page { size: A4; margin: 0; }
    @media print {
        body { background: #fff; }
        .toolbar { display: none; }
        .sheet { padding: 6mm; max-width: none; }
        .badge-grid { gap: 4mm; }
        .badge { box-shadow: none; border-color: #d1d5db; page-break-inside: avoid; }
    }
</style>
</head>
<body>

<div class="toolbar">
    <span class="title">Name Badges</span>
    <span class="sub"><?php echo e($registrants->count()); ?> participant(s) · <?php echo e(config('app.name')); ?> 2026</span>
    <div style="margin-left:auto; display:flex; gap:8px;">
        <button class="btn btn-ghost" onclick="window.close()">Close</button>
        <button class="btn btn-primary" onclick="window.print()">🖨 Print</button>
    </div>
</div>

<div class="sheet">
    <?php if($registrants->isEmpty()): ?>
        <div class="empty-state">
            <p style="font-size:15px;font-weight:700;color:#374151;margin-bottom:6px;">No participants</p>
            <p style="font-size:13px;">Nothing to print.</p>
        </div>
    <?php else: ?>
        <div class="badge-grid">
            <?php $__currentLoopData = $registrants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="badge">
                <div class="badge-header">
                    <div class="brand">
                        <img src="<?php echo e(asset('img/logo-msd.png')); ?>" alt="MSD">
                        <div class="evt">
                            <div class="t">METRODATA SOLUTION DAY</div>
                            <div class="s">Jakarta · Shangri-La Hotel</div>
                        </div>
                    </div>
                    <div class="year">2026</div>
                </div>
                <div class="badge-body">
                    <div class="badge-name"><?php echo e($r->name); ?></div>
                    <?php if($r->company): ?><div class="badge-company"><?php echo e($r->company); ?></div><?php endif; ?>
                    <?php if($r->job_title): ?><div class="badge-title"><?php echo e($r->job_title); ?></div><?php endif; ?>
                    <div class="badge-foot">
                        <div class="qr"><img src="<?php echo e($r->qr_code_url); ?>" alt="QR"></div>
                        <div class="meta">
                            <div class="cap">Reg Code</div>
                            <div class="code"><?php echo e($r->unique_code); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>
</div>

<script>
window.onload = function () {
    // Do not auto-print immediately so the user can adjust; but keep the Print button handy.
    // (Uncomment the line below to auto-open the print dialog on load.)
    // setTimeout(() => window.print(), 400);
};
</script>
</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/onsite/badges.blade.php ENDPATH**/ ?>