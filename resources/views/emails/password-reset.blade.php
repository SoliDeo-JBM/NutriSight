<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset your NutriSight password</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #334155; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 24px;">
        <div style="border-bottom: 2px solid #2563eb; padding-bottom: 12px; margin-bottom: 20px; text-align: center;">
            <img src="cid:nutrisight-logo@nutrisight" alt="NutriSight" width="56" height="56" style="display: block; border: 0; margin: 0 auto 10px;">
            <h2 style="color: #0f172a; margin: 0;">Reset your NutriSight password</h2>
        </div>
        <p>You are receiving this email because we received a password reset request for your account.</p>
        <p style="margin: 24px 0; text-align: center;"><a href="{{ $url }}" style="display: inline-block; background: #2563eb; color: #ffffff; padding: 12px 18px; border-radius: 6px; text-decoration: none; font-weight: bold;">Reset Password</a></p>
        <p>This password reset link will expire in {{ $expireMinutes }} minutes.</p>
        <p>If you did not request a password reset, no further action is required.</p>
        <p style="margin-top: 24px;">Warm regards,<br><strong>NutriSight</strong></p>
    </div>
</body>
</html>