<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:'Inter',-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f4f6;">
        <tr>
            <td align="center" style="padding:32px 16px;">
                <table width="560" cellpadding="0" cellspacing="0" style="max-width:560px;width:100%;border-radius:16px;overflow:hidden;box-shadow:0 8px 32px rgba(0,0,0,0.1);">

                    {{-- HERO: Key Visual --}}
                    <tr>
                        <td>
                            <img src="{{ $message?->embed(public_path('img/QRHeader.png')) ?: asset('img/QRHeader.png') }}" alt="MSD 2026" style="width:100%;height:auto;display:block;max-width:560px;">
                        </td>
                    </tr>

                    {{-- BODY --}}
                    <tr>
                        <td style="background:#ffffff;padding:36px 32px;">
                            <p style="margin:0 0 6px;font-size:16px;color:#374151;line-height:1.6;">Hello <strong style="color:#e91e63;">{{ $name }}</strong>,</p>
                            <p style="margin:0 0 20px;font-size:15px;color:#4b5563;line-height:1.7;">
                                You have been invited to access the <strong>MSD 2026</strong> event portal.
                                Please set up your password to get started.
                            </p>

                            {{-- Info Box --}}
                            <table width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;border-radius:12px;border:1px solid #e5e7eb;margin-bottom:24px;">
                                <tr>
                                    <td style="padding:16px 20px;">
                                        <p style="margin:0;font-size:12px;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Your Login Email</p>
                                        <p style="margin:4px 0 0;font-size:16px;font-weight:600;color:#111827;">{{ $email }}</p>
                                    </td>
                                </tr>
                            </table>

                            {{-- CTA Button --}}
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="background:linear-gradient(135deg,#ff3d6e,#e91e63);border-radius:999px;box-shadow:0 8px 24px rgba(233,30,99,0.35);">
                                        <a href="{{ $setupUrl }}" style="display:inline-block;padding:14px 44px;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;letter-spacing:0.02em;">
                                            Set Up My Password
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            {{-- Expiry Note --}}
                            <p style="margin:20px 0 0;font-size:13px;color:#9ca3af;line-height:1.6;text-align:center;">
                                This link will expire in <strong style="color:#6b7280;">48 hours</strong>.
                                If you did not expect this invitation, please ignore this email.
                            </p>
                        </td>
                    </tr>

                    {{-- FOOTER --}}
                    <tr>
                        <td style="background:#1f2937;padding:24px 32px;text-align:center;">
                            <p style="margin:0 0 4px;font-size:12px;color:rgba(255,255,255,0.5);">
                                <strong style="color:rgba(255,255,255,0.7);">Metrodata Solution Day 2026</strong>
                            </p>
                            <p style="margin:0;font-size:11px;color:rgba(255,255,255,0.35);">
                                Jakarta, 20 August 2026 · Shangri-La Hotel
                            </p>
                        </td>
                    </tr>
                </table>
                <p style="margin:16px 0 0;font-size:11px;color:#9ca3af;text-align:center;">&copy; {{ date('Y') }} Metrodata Group. All rights reserved.</p>
            </td>
        </tr>
    </table>
</body>
</html>
