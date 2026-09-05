<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SBFP Feeding Day Notice</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 24px;">
        <h2 style="color: #0f172a; border-bottom: 2px solid #22c55e; padding-bottom: 8px; margin-top: 0;">NutriSight SBFP Notice</h2>
        
        <p>Dear Parent / Guardian of <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>,</p>

        <p>This is to inform you that your child is scheduled to participate in the <strong>School-Based Feeding Program (SBFP)</strong> on <strong>{{ $date }}</strong>.</p>

        <div style="background: #f8fafc; border-left: 4px solid #22c55e; padding: 12px 16px; margin: 16px 0;">
            <p style="margin: 0 0 8px 0;"><strong>Meal to be Served:</strong> {{ $meal }}</p>
            @if($notes)
                <p style="margin: 0;"><strong>Teacher's Notes:</strong> {{ $notes }}</p>
            @endif
        </div>

        <p>We appreciate your continuous support in ensuring our learners receive proper nutrition for better health and academic performance.</p>

        <p style="margin-top: 24px;">Warm regards,</p>
        <p style="margin: 0;"><strong>School-Based Feeding Program Coordinator</strong><br>Marisol Bliss Elementary School</p>
    </div>
</body>
</html>
