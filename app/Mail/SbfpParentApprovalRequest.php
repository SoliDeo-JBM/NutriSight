<?php

namespace App\Mail;

use App\Models\SbfpParentApprovalRequest as ApprovalRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SbfpParentApprovalRequest extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = [10, 60, 300];

    public function __construct(
        public ApprovalRequest $approvalRequest,
        public string $token,
    ) {
        $this->afterCommit();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Parent approval needed for SBFP participation',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.sbfp-parent-approval',
            with: [
                'approvalUrl' => route('parent.sbfp.approval.show', [
                    'approvalRequest' => $this->approvalRequest,
                    'token' => $this->token,
                ]),
            ],
        );
    }
}
