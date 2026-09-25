<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>SBFP Enrollment Notification</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #334155; padding: 20px;">
    @php($student = $approvalRequest->participant->enrollment->student)
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 24px;">
        <div style="border-bottom: 2px solid #2563eb; padding-bottom: 12px; margin-bottom: 20px; text-align: center;">
            <img src="cid:nutrisight-logo@nutrisight" alt="NutriSight" width="56" height="56" style="display: block; border: 0; margin: 0 auto 10px;">
            <h2 style="color: #0f172a; margin: 0;">NutriSight School-Based Feeding Program</h2>
        </div>
        <p>Dear Parent / Guardian of <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>,</p>
        <p>We are pleased to inform you that your child is now part of the School-Based Feeding Program because of the baseline nutrition assessment below.</p>
        <div style="background: #f8fafc; border-left: 4px solid #2563eb; padding: 12px 16px; margin: 16px 0;">
            <p style="margin: 0 0 6px 0;"><strong>Weight:</strong> {{ $approvalRequest->weight }} kg</p>
            <p style="margin: 0 0 6px 0;"><strong>Height:</strong> {{ $approvalRequest->height }} cm</p>
            <p style="margin: 0 0 6px 0;"><strong>BMI:</strong> {{ $approvalRequest->bmi }}</p>
            <p style="margin: 0;"><strong>BMI status:</strong> {{ $approvalRequest->bmi_category }}</p>
        </div>
        <p>The measurements above are the basis for your child's inclusion in the program. Please contact the school if you have questions or need more information.</p>
        <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px 12px; margin-top: 20px; color: #475569; font-size: 12px;">
            <strong>Privacy Notice:</strong> This message contains personal and health-related information shared for School-Based Feeding Program coordination and your child's welfare. Under Republic Act No. 10173, the Data Privacy Act of 2012, please keep this information confidential, do not forward it, and contact Marisol Bliss Elementary School if you received it in error.
        </div>
        <p style="margin-top: 24px;">Warm regards,<br><strong>School-Based Feeding Program Coordinator</strong></p>
    </div>
</body>

</html>