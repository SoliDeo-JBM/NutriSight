<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SBFP Approval Notice</title>
</head>
<body style="margin: 0; background: #f4f7f4; color: #1f2933; font-family: Arial, sans-serif;">
    <div style="max-width: 640px; margin: 32px auto; padding: 24px; background: #ffffff; border: 1px solid #d9e2dc;">
        <div style="text-align: center; margin-bottom: 24px;">
            <img src="{{ asset('images/nutrisight-logo.png') }}" alt="NutriSight" style="max-width: 180px; height: auto;">
        </div>

        <h1 style="margin: 0 0 16px; color: #176b45; font-size: 24px;">SBFP approval notice</h1>

        <p>Dear Parent or Guardian,</p>

        <p>
            The school has approved <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
            for inclusion in the School-Based Feeding Program as a
            {{ $gradeLevel === 0 ? 'Kindergarten' : 'Grade ' . $gradeLevel }} learner.
        </p>

        <p>The school recorded this approval after reviewing the learner for SBFP participation. Baseline assessment details are included for your reference.</p>

        <h2 style="color: #176b45; font-size: 18px;">Baseline information</h2>
        @if($measurement)
        <ul>
            <li>Weight: {{ number_format((float) $measurement->weight, 2) }} kg</li>
            <li>Height: {{ number_format((float) $measurement->height, 2) }} cm</li>
            <li>BMI: {{ number_format((float) $measurement->bmi, 2) }}</li>
            <li>BMI status: {{ $measurement->bmi_category ?: 'Not recorded' }}</li>
        </ul>
        @else
        <p>Baseline measurements were not available when this notice was sent.</p>
        @endif

        <p>Please contact the school if you have questions.</p>

        <p style="font-size: 13px; color: #52606d;">
            NutriSight processes information for School-Based Feeding Program coordination in accordance with
            Republic Act No. 10173, the Data Privacy Act of 2012.
        </p>
    </div>
</body>
</html>