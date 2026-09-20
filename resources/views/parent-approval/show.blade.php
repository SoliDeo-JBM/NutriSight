<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SBFP Parent Approval</title>
</head>

<body style="font-family: Arial, sans-serif; background:#f1f5f9; color:#334155; padding:24px;">
    <div style="max-width:560px;margin:0 auto;background:#fff;border:1px solid #e2e8f0;border-radius:8px;padding:24px;">
        <h1 style="margin-top:0;color:#0f172a;font-size:24px;">SBFP Parent Approval</h1>
        <p>Please review the baseline nutrition information for <strong>{{ $approvalRequest->participant->enrollment->student->first_name }} {{ $approvalRequest->participant->enrollment->student->last_name }}</strong>.</p>
        <div style="background:#f8fafc;padding:14px 16px;border-left:4px solid #2563eb;">
            <p><strong>Weight:</strong> {{ $approvalRequest->weight }} kg</p>
            <p><strong>Height:</strong> {{ $approvalRequest->height }} cm</p>
            <p><strong>BMI:</strong> {{ $approvalRequest->bmi }}</p>
            <p><strong>Status:</strong> {{ $approvalRequest->bmi_category }}</p>
        </div>
        <form method="POST" action="{{ route('parent.sbfp.approval.respond', [$approvalRequest, $token]) }}" style="margin-top:24px;">
            @csrf
            <label style="display:block;margin-bottom:8px;"><input type="radio" name="decision" value="approved" required> I approve participation.</label>
            <label style="display:block;margin-bottom:14px;"><input type="radio" name="decision" value="disapproved" required> I do not approve participation.</label>
            <label for="reason" style="display:block;margin-bottom:6px;">Reason, if not approving</label>
            <textarea id="reason" name="reason" rows="4" style="width:100%;box-sizing:border-box;padding:8px;border:1px solid #cbd5e1;border-radius:4px;"></textarea>
            <button type="submit" style="margin-top:16px;background:#2563eb;color:#fff;border:0;border-radius:6px;padding:12px 18px;font-weight:bold;cursor:pointer;">Submit decision</button>
        </form>
    </div>
</body>

</html>