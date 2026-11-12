<?php

namespace App\Console\Commands;

use App\Mail\ReminderMail;
use App\Models\Ausleihe;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendReminderEmails extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Sendet Erinnerungs-E-Mails für Ausleihen, die in den nächsten 48 Stunden fällig sind';

    public function handle(): int
    {
        $abgaben = Ausleihe::faelligIn48h();

        foreach ($abgaben as $abgabe) {
            $name = $abgabe->Vorname . ' ' . $abgabe->Nachname;

            try {
                Mail::send(new ReminderMail(
                    toEmail: $abgabe->Email,
                    toName: $name,
                    reminderTitle: 'Rückgabe fällig: ' . $abgabe->Bezeichnung,
                    reminderMessage: 'Dies ist eine automatische Erinnerung für ' . $abgabe->Bezeichnung . '.',
                    gegenstand: $abgabe->Bezeichnung . ' (Barcode: ' . $abgabe->Barcode . ')',
                    rueckgabeTermin: (string)$abgabe->Fälligkeitsdatum,
                ));

                Ausleihe::whereKey($abgabe->Ausleih_ID)->update(['EmailVersendet' => 1]);
                $this->info("Erinnerung an {$abgabe->Email} gesendet.");
            } catch (Throwable $e) {
                report($e);
                $this->error("Versand an {$abgabe->Email} fehlgeschlagen: " . $e->getMessage());
            }
        }

        if ($abgaben->isEmpty()) {
            $this->info('Keine anstehenden Rückgaben.');
        }

        return self::SUCCESS;
    }
}
