<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>SBFP Feeding Day Notice</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 24px;">
        <div style="border-bottom: 2px solid #22c55e; padding-bottom: 12px; margin-bottom: 20px; text-align: center;">
            <img src="cid:nutrisight-logo@nutrisight" alt="NutriSight" width="56" height="56" style="display: block; border: 0; margin: 0 auto 10px;">
            <h2 style="color: #0f172a; margin: 0;">NutriSight SBFP Notice</h2>
        </div>
        
        <p>Dear Parent / Guardian of <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>,</p>

        <p><strong>{{ $student->first_name }} {{ $student->last_name }}</strong> is present in the school-based feeding program today and ate <strong>{{ $meal }}</strong>.</p>

        <div style="background: #f8fafc; border-left: 4px solid #22c55e; padding: 12px 16px; margin: 16px 0;">
            <p style="margin: 0 0 8px 0;"><strong>Attendance date:</strong> {{ $date }}</p>
            @if($notes)
                <p style="margin: 0;"><strong>Teacher's Notes:</strong> {{ $notes }}</p>
            @endif
        </div>

        <p>We appreciate your continuous support in ensuring our learners receive proper nutrition for better health and academic performance.</p>

        <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px 12px; margin-top: 20px; color: #475569; font-size: 12px;">
            <strong>Privacy Notice:</strong> This message contains personal and health-related information shared for School-Based Feeding Program coordination and your child's welfare. Under Republic Act No. 10173, the Data Privacy Act of 2012, please keep this information confidential, do not forward it, and contact Marisol Bliss Elementary School if you received it in error.
        </div>

        <p style="margin-top: 24px;">Warm regards,</p>
        <p style="margin: 0;"><strong>School-Based Feeding Program Coordinator</strong><br>Marisol Bliss Elementary School</p>
    </div>
</body>
</html>
