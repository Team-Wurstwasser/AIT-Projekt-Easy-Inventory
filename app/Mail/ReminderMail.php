<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ReminderMail extends Mailable
{
    public function __construct(
        public string $toEmail,
        public string $toName,
        public string $reminderTitle,
        public string $reminderMessage,
        public string $gegenstand,
        public string $rueckgabeTermin,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            to: [new Address($this->toEmail, $this->toName)],
            subject: 'Erinnerung: ' . $this->reminderTitle,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.reminder');
    }
}
