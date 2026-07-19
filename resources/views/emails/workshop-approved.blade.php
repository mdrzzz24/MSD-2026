<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Workshop Confirmation</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f9;font-family:'Inter','Segoe UI',Arial,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f9;">
<tr><td align="center" style="padding:30px 15px;">
<table role="presentation" width="100%" style="max-width:600px;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.06);">
<tr><td style="padding:0;">
<img src="{{ asset('img/QRHeader.png') }}" alt="MSD 2026" width="100%" style="display:block;width:100%;height:auto;max-width:600px;">
</td></tr>
<tr><td style="padding:36px 36px 24px;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0">
<tr><td style="padding-bottom:16px;">
<p style="margin:0;font-size:15px;color:#374151;line-height:1.7;">Dear <strong style="color:#050d2a;">Mr./Ms. {{ $registrant->display_name }}</strong>,</p>
</td></tr>
<tr><td style="padding-bottom:8px;">
<p style="margin:0;font-size:15px;color:#374151;line-height:1.7;">Thank you for registering.</p>
<p style="margin:8px 0 0;font-size:15px;color:#374151;line-height:1.7;">This email confirms your registration for the following workshop:</p>
</td></tr>
<tr><td style="padding:16px 0;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;">
<tr><td style="padding:16px 20px;">
<p style="margin:0 0 4px;font-size:16px;font-weight:700;color:#050d2a;">{{ $workshop_name ?? $workshopName }} – {{ $workshop_title ?? $workshopName }}</p>
<p style="margin:4px 0;font-size:14px;color:#6b7280;">{{ $workshop_room ?? '' }}</p>
<p style="margin:4px 0;font-size:14px;color:#6b7280;">{{ $workshop_time ?? '' }}</p>
</td></tr>
</table>
</td></tr>
<tr><td style="padding-bottom:8px;">
<p style="margin:0;font-size:15px;color:#374151;line-height:1.7;">We look forward to seeing you at the workshop!</p>
</td></tr>
<tr><td style="padding-bottom:16px;">
<p style="margin:0;font-size:13px;color:#dc2626;font-weight:600;line-height:1.6;">Room Capacity max {{ $workshop_capacity ?? '35' }} pax. Please come on time. First Come, First Serve.</p>
</td></tr>
<tr><td style="padding-bottom:16px;text-align:center;">
@if ($registrant->qr_token)
<img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($registrant->unique_code ?? $registrant->qr_token) }}" alt="QR Code" style="width:150px;height:150px;display:block;margin:0 auto;">
@endif
</td></tr>
<tr><td style="padding-bottom:8px;text-align:center;">
<p style="margin:0;font-size:13px;color:#6b7280;font-style:italic;line-height:1.5;">
<strong>Please show the QR Code to Registration Counter on the Venue and</strong><br>
<strong>get your badge to join the workshop.</strong>
</p>
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
