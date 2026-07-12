<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registration Received</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f9;font-family:'Inter','Segoe UI',Arial,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f9;">
<tr><td align="center" style="padding:30px 15px;">
<table role="presentation" width="100%" style="max-width:600px;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.06);">
<tr><td style="padding:0;">
<img src="{{ asset('img/QRHeader.png') }}" alt="MSD 2026" width="100%" style="display:block;width:100%;height:auto;max-width:600px;">
</td></tr>
<tr><td style="padding:40px 36px 24px;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0">
<tr><td style="text-align:center;padding-bottom:8px;">
<h1 style="margin:0;font-size:22px;font-weight:800;color:#050d2a;">Thank You for Registering</h1>
</td></tr>
<tr><td style="text-align:center;padding-bottom:20px;">
<p style="margin:0;font-size:15px;color:#6b7280;line-height:1.6;">Dear Mr./Ms. <strong style="color:#050d2a;">{{ $name ?? $registrant->name ?? '' }}</strong>,</p>
<p style="margin:12px 0 0;font-size:15px;color:#6b7280;line-height:1.6;">Thank you for your registration request for <strong style="color:#050d2a;">MSD 2026</strong>.</p>
<p style="margin:12px 0 0;font-size:15px;color:#6b7280;line-height:1.6;"><em>Winning with AI: Build, Run, and Scale for Measurable Impact</em></p>
<p style="margin:16px 0 0;font-size:15px;color:#6b7280;line-height:1.6;">Your registration request has been successfully submitted, and it is currently being processed.</p>
<p style="margin:16px 0 0;font-size:15px;color:#6b7280;line-height:1.6;">You will receive a confirmation email with the QR Code once your registration is approved and confirmed.</p>
</td></tr>
</table>
</td></tr>
<tr><td style="padding:0 36px;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0">
<tr><td style="padding:16px 20px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;text-align:center;">
<p style="margin:0;font-size:14px;color:#374151;">Questions? Email us at <a href="mailto:metrodatasolutionday2026@jovenindo.co.id" style="color:#ff3d6e;text-decoration:underline;">metrodatasolutionday2026@jovenindo.co.id</a></p>
</td></tr>
</table>
</td></tr>
<tr><td style="padding:20px 36px 32px;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0">
<tr><td style="border-top:1px solid #e5e7eb;padding-top:20px;text-align:center;">
<p style="margin:0;font-size:12px;color:#9ca3af;">Yours Sincerely,</p>
<p style="margin:4px 0 0;font-size:12px;color:#9ca3af;font-weight:600;">PT Metrodata Electronics, Tbk</p>
</td></tr>
</table>
</td></tr>
</table>
</td></tr>
</table>
</body>
</html>
