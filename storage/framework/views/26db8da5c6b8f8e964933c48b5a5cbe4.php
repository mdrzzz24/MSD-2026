<!DOCTYPE html>
<html>
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
<meta charset="utf-8"></head>
<body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 30px;">
        <h2 style="color: #991b1b; margin-top: 0;">❌ Registration Rejected</h2>
        <p>Halo <strong><?php echo e($registrant->name); ?></strong>,</p>
        <p>We regret to inform you that your registration has been <strong>rejected</strong>.</p>
        <?php if($registrant->admin_notes): ?>
            <p><strong>Reason:</strong> <?php echo e($registrant->admin_notes); ?></p>
        <?php endif; ?>
        <hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">
        <p style="color: #666; font-size: 12px;">Email ini dikirim otomatis. Mohon tidak membalas.</p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\2026-Testing\resources\views/emails/registrant-rejected.blade.php ENDPATH**/ ?>