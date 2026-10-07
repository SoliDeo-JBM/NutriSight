<?php

namespace App\Mail;

use App\Models\NutritionMeasurement;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;

class SbfpAutomaticEnrollmentNotice extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = [10, 60, 300];

    public function __construct(
        public Student $student,
        public NutritionMeasurement $measurement,
        public int $gradeLevel,
    ) {
        $this->afterCommit();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your child has been enrolled in the School-Based Feeding Program',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.sbfp-automatic-enrollment',
        );
    }

    public function build(): static
    {
        return $this->withSymfonyMessage(function ($message) {
            $message->addPart(
                (new DataPart(new File(public_path('images/nutrisight-logo.png')), 'nutrisight-logo.png', 'image/png'))
                    ->asInline()
                    ->setContentId('nutrisight-logo@nutrisight')
            );
        });
    }
}