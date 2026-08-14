<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Preview: <?php echo e($template->name); ?> — <?php echo e(config('app.name')); ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #fff; }
    </style>
</head>
<body>
    <div style="position:fixed;top:12px;left:12px;z-index:9999;">
        <a href="<?php echo e(route('admin.templates.index')); ?>" style="display:inline-flex;align-items:center;gap:4px;padding:6px 14px;background:rgba(255,255,255,0.95);border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;color:#374151;text-decoration:none;box-shadow:0 1px 3px rgba(0,0,0,0.1);transition:background 0.15s;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='rgba(255,255,255,0.95)'">&larr; Back</a>
    </div>
    <?php echo $html; ?>

</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/admin/templates/preview.blade.php ENDPATH**/ ?>