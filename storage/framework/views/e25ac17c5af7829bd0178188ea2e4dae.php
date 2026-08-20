<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Credentials</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f9;font-family:'Inter','Segoe UI',Arial,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f9;">
<tr><td align="center" style="padding:30px 15px;">
<table role="presentation" width="100%" style="max-width:600px;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.06);">
<tr><td style="padding:0;">
<img src="<?php echo e(asset('img/QRHeader.png')); ?>" alt="MSD 2026" width="100%" style="display:block;width:100%;height:auto;max-width:600px;">
</td></tr>
<tr><td style="padding:40px 36px 24px;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0">
<tr><td style="text-align:center;padding-bottom:8px;">
<h1 style="margin:0;font-size:22px;font-weight:800;color:#050d2a;">Registration Approved</h1>
</td></tr>
<tr><td style="text-align:center;padding-bottom:20px;">
<p style="margin:0;font-size:15px;color:#6b7280;line-height:1.6;">Hello <strong style="color:#050d2a;"><?php echo e($registrant->display_name); ?></strong>,</p>
<p style="margin:12px 0 0;font-size:15px;color:#6b7280;line-height:1.6;">Congratulations! Your registration has been <strong style="color:#10b981;">approved</strong>.</p>
<p style="margin:12px 0 0;font-size:15px;color:#6b7280;line-height:1.6;">Here are your login credentials:</p>
</td></tr>
</table>
</td></tr>
<tr><td style="padding:0 36px;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;">
<tr><td style="padding:20px;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0">
<tr><td style="padding:4px 0;font-size:14px;color:#374151;"><strong style="color:#050d2a;">Email:</strong> <?php echo e($registrant->email); ?></td></tr>
<tr><td style="padding:4px 0;font-size:14px;color:#374151;"><strong style="color:#050d2a;">Password:</strong> <span style="font-family:monospace;background:#e5e7eb;padding:2px 10px;border-radius:6px;font-size:13px;color:#050d2a;font-weight:700;"><?php echo e($plainPassword); ?></span></td></tr>
</table>
</td></tr>
</table>
</td></tr>
<tr><td style="padding:24px 36px 0;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0">
<tr><td style="text-align:center;">
<a href="<?php echo e(route('registrant.login')); ?>" style="display:inline-block;padding:12px 36px;background:linear-gradient(135deg,#ff3d6e,#e91e63);color:#ffffff;text-decoration:none;border-radius:999px;font-size:14px;font-weight:700;box-shadow:0 4px 14px rgba(233,30,99,0.3);">Login Now</a>
</td></tr>
<tr><td style="text-align:center;padding-top:14px;">
<p style="margin:0;font-size:13px;color:#6b7280;line-height:1.5;">After logging in, you can register for available workshops and sessions.</p>
</td></tr>
</table>
</td></tr>
<tr><td style="padding:20px 36px 32px;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0">
<tr><td style="border-top:1px solid #e5e7eb;padding-top:20px;text-align:center;">
<p style="margin:0;font-size:12px;color:#9ca3af;">This is an automated email. Please do not reply.</p>
<p style="margin:4px 0 0;font-size:12px;color:#9ca3af;">PT Metrodata Electronics, Tbk</p>
</td></tr>
</table>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/emails/registrant-credentials.blade.php ENDPATH**/ ?>