<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AusleiheSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('Ausleihe')->exists()) {
            return;
        }

        DB::table('Ausleihe')->insert([
            ['Ausleihdatum' => '2024-05-01', 'Ausleihdauer' => 4, 'Rückgabedatum' => '2024-05-03', 'ZustandBeiRückgabe' => 'Guter Zustand',           'Mitarbeiter_ID' => 1, 'Barcode' => '2004778818704', 'EmailVersendet' => 0],
            ['Ausleihdatum' => '2024-04-25', 'Ausleihdauer' => 3, 'Rückgabedatum' => '2024-04-28', 'ZustandBeiRückgabe' => 'Leichte Gebrauchsspuren', 'Mitarbeiter_ID' => 2, 'Barcode' => '2002408367394', 'EmailVersendet' => 0],
            ['Ausleihdatum' => '2024-01-01', 'Ausleihdauer' => 3, 'Rückgabedatum' => '2024-04-02', 'ZustandBeiRückgabe' => 'Guter Zustand',           'Mitarbeiter_ID' => 3, 'Barcode' => '2005757906979', 'EmailVersendet' => 0],
            ['Ausleihdatum' => '2024-05-02', 'Ausleihdauer' => 1, 'Rückgabedatum' => '2024-05-02', 'ZustandBeiRückgabe' => 'Defekt zurückgegeben',    'Mitarbeiter_ID' => 4, 'Barcode' => '2009817029400', 'EmailVersendet' => 0],
            ['Ausleihdatum' => '2024-05-06', 'Ausleihdauer' => 3, 'Rückgabedatum' => '2024-07-01', 'ZustandBeiRückgabe' => 'Guter Zustand',           'Mitarbeiter_ID' => 5, 'Barcode' => '2007907636590', 'EmailVersendet' => 0],
        ]);
    }
}
