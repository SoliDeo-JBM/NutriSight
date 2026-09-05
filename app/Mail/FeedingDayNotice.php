<?php

namespace App\Mail;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FeedingDayNotice extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $meal;
    public $date;
    public $notes;

    public function __construct(Student $student, string $meal, string $date, ?string $notes = null)
    {
        $this->student = $student;
        $this->meal = $meal;
        $this->date = $date;
        $this->notes = $notes;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'School-Based Feeding Program (SBFP) Notice for ' . $this->student->first_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.feeding-day-notice',
        );
    }
}
