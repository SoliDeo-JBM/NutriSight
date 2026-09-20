<?php

namespace App\Http\Controllers;

use App\Models\SbfpParentApprovalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class SbfpParentApprovalController extends Controller
{
    public function show(SbfpParentApprovalRequest $approvalRequest, string $token)
    {
        if (!$this->isValid($approvalRequest, $token)) {
            return view('parent-approval.invalid');
        }

        return view('parent-approval.show', compact('approvalRequest', 'token'));
    }

    public function respond(Request $request, SbfpParentApprovalRequest $approvalRequest, string $token)
    {
        $validated = $request->validate([
            'decision' => ['required', 'in:approved,disapproved'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validated['decision'] === 'disapproved' && blank($validated['reason'] ?? null)) {
            throw ValidationException::withMessages(['reason' => 'Please provide a reason for disapproval.']);
        }

        DB::transaction(function () use ($approvalRequest, $token, $validated) {
            $requestRecord = SbfpParentApprovalRequest::query()
                ->whereKey($approvalRequest->id)
                ->lockForUpdate()
                ->with('participant')
                ->firstOrFail();

            if (!$this->isValid($requestRecord, $token)) {
                throw ValidationException::withMessages(['decision' => 'This approval link is no longer valid.']);
            }

            $requestRecord->participant->update([
                'parent_consent' => $validated['decision'],
                'disapproval_reason' => $validated['decision'] === 'disapproved' ? $validated['reason'] : null,
            ]);

            $requestRecord->update([
                'status' => $validated['decision'],
                'responded_at' => Carbon::now(),
                'decision_reason' => $validated['decision'] === 'disapproved' ? $validated['reason'] : null,
            ]);

            $requestRecord->participant->approvalRequests()
                ->where('status', 'pending')
                ->where('id', '!=', $requestRecord->id)
                ->update([
                    'status' => 'superseded',
                    'closed_reason' => 'parent_responded_elsewhere',
                ]);
        });

        return view('parent-approval.complete', ['decision' => $validated['decision']]);
    }

    private function isValid(SbfpParentApprovalRequest $approvalRequest, string $token): bool
    {
        return $approvalRequest->status === 'pending'
            && $approvalRequest->expires_at?->isFuture()
            && $approvalRequest->participant?->parent_consent === null
            && hash_equals($approvalRequest->token_hash, hash('sha256', $token));
    }
}
