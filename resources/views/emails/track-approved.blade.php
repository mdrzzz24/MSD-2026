<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Session Approved</title>
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
<tr><td style="text-align:center;padding-bottom:20px;">
<div style="display:inline-block;width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#10b981,#059669);line-height:48px;text-align:center;font-size:22px;">&#10003;</div>
</td></tr>
<tr><td style="text-align:center;padding-bottom:8px;">
<h1 style="margin:0;font-size:22px;font-weight:800;color:#050d2a;">Session Approved</h1>
</td></tr>
<tr><td style="text-align:center;padding-bottom:20px;">
<p style="margin:0;font-size:15px;color:#6b7280;line-height:1.6;">Hello <strong style="color:#050d2a;">{{ $registrant->display_name }}</strong>,</p>
<p style="margin:12px 0 0;font-size:15px;color:#6b7280;line-height:1.6;">Your registration for the session <strong style="color:#050d2a;">{{ $sessionName }}</strong> has been <strong style="color:#10b981;">approved</strong>.</p>
<p style="margin:12px 0 0;font-size:15px;color:#6b7280;line-height:1.6;">We look forward to seeing you at the session.</p>
</td></tr>
</table>
</td></tr>
<tr><td style="padding:0 36px 32px;">
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
