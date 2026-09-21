<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>SBFP Parent Approval</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #334155; padding: 20px;">
    @php($student = $approvalRequest->participant->enrollment->student)
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 24px;">
        <div style="border-bottom: 2px solid #2563eb; padding-bottom: 12px; margin-bottom: 20px;">
            <img src="{{ url('images/nutrisight-logo.png') }}" alt="NutriSight" width="56" height="56" style="display: block; border: 0; margin-bottom: 10px;">
            <h2 style="color: #0f172a; margin: 0;">NutriSight SBFP Parent Approval</h2>
        </div>
        <p>Dear Parent / Guardian of <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>,</p>
        <p>Your child may be eligible for the School-Based Feeding Program based on the baseline nutrition assessment below.</p>
        <div style="background: #f8fafc; border-left: 4px solid #2563eb; padding: 12px 16px; margin: 16px 0;">
            <p style="margin: 0 0 6px 0;"><strong>Weight:</strong> {{ $approvalRequest->weight }} kg</p>
            <p style="margin: 0 0 6px 0;"><strong>Height:</strong> {{ $approvalRequest->height }} cm</p>
            <p style="margin: 0 0 6px 0;"><strong>BMI:</strong> {{ $approvalRequest->bmi }}</p>
            <p style="margin: 0;"><strong>BMI status:</strong> {{ $approvalRequest->bmi_category }}</p>
        </div>
        <p>Please review the information and submit your approval or decision using the secure link below. The link expires in {{ \App\Services\SbfpParentApprovalService::LINK_TTL_HOURS }} hours.</p>
        <p style="margin: 24px 0;"><a href="{{ $approvalUrl }}" style="display: inline-block; background: #2563eb; color: #ffffff; padding: 12px 18px; border-radius: 6px; text-decoration: none; font-weight: bold;">Review and Respond</a></p>
        <p>If you did not expect this message or need assistance, please contact the school.</p>
        <p style="margin-top: 24px;">Warm regards,<br><strong>School-Based Feeding Program Coordinator</strong></p>
    </div>
</body>

</html>