<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Workshop Registration Update</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f9;font-family:'Inter','Segoe UI',Arial,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f9;">
<tr><td align="center" style="padding:30px 15px;">
<table role="presentation" width="100%" style="max-width:600px;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.06);">
<tr><td style="padding:0;">
<img src="<?php echo e(asset('img/QRHeader.png')); ?>" alt="MSD 2026" width="100%" style="display:block;width:100%;height:auto;max-width:600px;">
</td></tr>
<tr><td style="padding:36px 36px 24px;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0">
<tr><td style="text-align:center;padding-bottom:16px;">
<div style="display:inline-block;width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#ef4444,#dc2626);line-height:48px;text-align:center;font-size:22px;">&#10007;</div>
</td></tr>
<tr><td style="padding-bottom:16px;">
<p style="margin:0;font-size:15px;color:#374151;line-height:1.7;">Dear <strong style="color:#050d2a;">Mr./Ms. <?php echo e($registrant->display_name); ?></strong>,</p>
</td></tr>
<tr><td style="padding-bottom:8px;">
<p style="margin:0;font-size:15px;color:#374151;line-height:1.7;">Thank you for your interest in registering for the following workshop:</p>
</td></tr>
<tr><td style="padding:16px 0;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;">
<tr><td style="padding:16px 20px;">
<p style="margin:0 0 4px;font-size:16px;font-weight:700;color:#050d2a;"><?php echo e($workshop_name ?? $workshopName); ?> – <?php echo e($workshop_title ?? $workshopName); ?></p>
<p style="margin:4px 0;font-size:14px;color:#6b7280;"><?php echo e($workshop_room ?? ''); ?></p>
<p style="margin:4px 0;font-size:14px;color:#6b7280;"><?php echo e($workshop_time ?? ''); ?></p>
</td></tr>
</table>
</td></tr>
<tr><td style="padding-bottom:16px;">
<p style="margin:0;font-size:15px;color:#374151;line-height:1.7;">We regret to inform you that your workshop registration has been <strong style="color:#dc2626;">rejected</strong>.</p>
<?php if($adminNotes): ?>
<p style="margin:12px 0 0;padding:14px 18px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;font-size:14px;color:#991b1b;line-height:1.5;"><strong>Reason:</strong> <?php echo e($adminNotes); ?></p>
<?php endif; ?>
</td></tr>
</table>
</td></tr>
<tr><td style="padding:0 36px 32px;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0">
<tr><td style="border-top:1px solid #e5e7eb;padding-top:20px;text-align:center;">
<p style="margin:0;font-size:13px;color:#6b7280;line-height:1.6;">Yours Sincerely,</p>
<p style="margin:4px 0 0;font-size:13px;font-weight:600;color:#050d2a;">PT Metrodata Electronics, Tbk.</p>
</td></tr>
</table>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>
<?php /**PATH /Users/mdrz/2026/MSD26/resources/views/emails/workshop-rejected.blade.php ENDPATH**/ ?>