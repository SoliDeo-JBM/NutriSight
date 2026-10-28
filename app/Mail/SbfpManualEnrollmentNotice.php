<?php

namespace App\Mail;

use App\Models\Student;
use App\Models\NutritionMeasurement;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class SbfpManualEnrollmentNotice extends Mailable
{
    public function __construct(
        public Student $student,
        public int $gradeLevel,
        public ?NutritionMeasurement $measurement = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your child has been approved for the School-Based Feeding Program',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.sbfp-manual-enrollment',
        );
    }
}