<!DOCTYPE html>
<html>
<head>
    <link rel="icon" type="image/png" href="<?php echo e(asset('img/metrodata.png')); ?>">
<meta charset="utf-8"></head>
<body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 30px;">
        <h2 style="color: #166534; margin-top: 0;">✅ Registration Approved</h2>
        <p>Halo <strong><?php echo e($registrant->display_name); ?></strong>,</p>
        <p>Congratulations! Your registration has been <strong>approved</strong>.</p>
        <p>Here are your login credentials:</p>
        <div style="background: #fff; border: 1px solid #ddd; border-radius: 8px; padding: 16px; margin: 16px 0;">
            <p style="margin: 4px 0;"><strong>Email:</strong> <?php echo e($registrant->email); ?></p>
            <p style="margin: 4px 0;"><strong>Password:</strong> <code style="background: #f3f4f6; padding: 2px 8px; border-radius: 4px;"><?php echo e($plainPassword); ?></code></p>
        </div>
        <p>Anda dapat login di: <a href="<?php echo e(route('registrant.login')); ?>"><?php echo e(route('registrant.login')); ?></a></p>
        <p>After logging in, you can register for available workshops and sessions.</p>
        <hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">
        <p style="color: #666; font-size: 12px;">This is an automated email. Please do not reply.</p>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\2026-Testing\resources\views/emails/registrant-credentials.blade.php ENDPATH**/ ?>