<?php

namespace App\Services;

use App\Mail\SbfpParentApprovalRequest as ApprovalRequestMail;
use App\Models\NutritionMeasurement;
use App\Models\SbfpParentApprovalRequest;
use App\Models\SbfpParticipant;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SbfpParentApprovalService
{
    public const LINK_TTL_HOURS = 72;

    public function syncBaseline(SbfpParticipant $participant, NutritionMeasurement $measurement): ?SbfpParentApprovalRequest
    {
        if (!$this->isEligible($measurement)) {
            if ($participant->parent_consent === null) {
                $this->closePending($participant, 'no_longer_eligible');
            }

            return null;
        }

        if ($participant->parent_consent !== null) {
            return null;
        }

        $now = Carbon::now();
        $participant->approvalRequests()
            ->where('status', 'pending')
            ->where('expires_at', '<=', $now)
            ->update([
                'status' => 'expired',
                'closed_reason' => 'expired',
            ]);

        $hasActiveRequest = $participant->approvalRequests()
            ->where('status', 'pending')
            ->where('expires_at', '>', $now)
            ->exists();

        $email = $participant->enrollment?->student?->guardian_email;
        if ($hasActiveRequest || blank($email)) {
            return null;
        }

        $request = $participant->approvalRequests()->create([
            'email' => $email,
            'token_hash' => hash('sha256', Str::random(64)),
            'weight' => $measurement->weight,
            'height' => $measurement->height,
            'bmi' => $measurement->bmi,
            'bmi_category' => $measurement->bmi_category,
            'status' => 'pending',
            'expires_at' => $now->copy()->addHours(self::LINK_TTL_HOURS),
            'sent_at' => $now,
        ]);

        Mail::to($email)->queue(new ApprovalRequestMail($request));

        return $request;
    }

    public function resendForGuardianEmailChange(SbfpParticipant $participant, NutritionMeasurement $measurement, ?int $userId = null): ?SbfpParentApprovalRequest
    {
        if (!$this->isEligible($measurement) || $participant->parent_consent !== null) {
            return null;
        }

        $email = $participant->enrollment?->student?->guardian_email;
        if (blank($email)) {
            return null;
        }

        $this->closePending($participant, 'guardian_email_changed', $userId);

        return $this->syncBaseline($participant, $measurement);
    }

    public function closePending(SbfpParticipant $participant, string $reason, ?int $userId = null): int
    {
        return $participant->approvalRequests()
            ->where('status', 'pending')
            ->update([
                'status' => 'superseded',
                'closed_reason' => $reason,
                'closed_by_user_id' => $userId,
            ]);
    }

    public function isEligible(NutritionMeasurement $measurement): bool
    {
        return in_array($measurement->bmi_category, ['Wasted', 'Severely Wasted'], true);
    }
}
