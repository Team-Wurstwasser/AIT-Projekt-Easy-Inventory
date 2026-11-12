<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MitarbeiterSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('Mitarbeiter')->exists()) {
            return;
        }

        DB::table('Mitarbeiter')->insert([
            ['Vorname' => 'Max',   'Nachname' => 'Mustermann',  'Email' => 'alexander.goetzhome@gmail.com', 'Abteilung_ID' => 1],
            ['Vorname' => 'Erika', 'Nachname' => 'Mustertrans', 'Email' => 'erika.mustertrans@example.com', 'Abteilung_ID' => 2],
            ['Vorname' => 'Dörk',  'Nachname' => 'Frenzen',     'Email' => 'dörk.frenzen@example.com',      'Abteilung_ID' => 3],
            ['Vorname' => 'Lena',  'Nachname' => 'Meier',       'Email' => 'lena.meier@example.com',        'Abteilung_ID' => 4],
            ['Vorname' => 'Tom',   'Nachname' => 'Möller',      'Email' => 'tom.moeller@example.com',       'Abteilung_ID' => 5],
            ['Vorname' => 'Anna',  'Nachname' => 'Huber',       'Email' => 'anna.huber@example.com',        'Abteilung_ID' => 6],
        ]);
    }
}
