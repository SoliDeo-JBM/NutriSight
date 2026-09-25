<?php

namespace App\Mail;

use App\Models\Student;
use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;

class FeedingDayNotice extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = [10, 60, 300];

    public $student;
    public $meal;
    public $date;
    public $notes;
    public $scannedAt;
    public $mealPeriod;

    public function __construct(Student $student, string $meal, string $date, ?string $notes = null, ?CarbonInterface $scannedAt = null, string $mealPeriod = 'morning')
    {
        $this->student = $student;
        $this->meal = $meal;
        $this->date = $date;
        $this->notes = $notes;
        $this->scannedAt = $scannedAt;
        $this->mealPeriod = $mealPeriod;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'SBFP ' . ucfirst($this->mealPeriod) . ' attendance notice for ' . $this->student->first_name . ' ' . $this->student->last_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.feeding-day-notice',
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
