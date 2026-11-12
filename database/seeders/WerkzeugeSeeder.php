<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WerkzeugeSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('Werkzeuge')->exists()) {
            return;
        }

        DB::table('Werkzeuge')->insert([
            ['Barcode' => '2002408367394', 'Bezeichnung' => 'Klauenhammer 500g',                'Anschaffungsdatum' => '2023-01-15', 'Status_ID' => 1, 'Typ_ID' => 1],
            ['Barcode' => '2005757906979', 'Bezeichnung' => 'Kreuzschlitzschraubendreher PH2',  'Anschaffungsdatum' => '2023-02-20', 'Status_ID' => 1, 'Typ_ID' => 2],
            ['Barcode' => '2004778818704', 'Bezeichnung' => 'Akku-Bohrmaschine 18V',            'Anschaffungsdatum' => '2023-03-10', 'Status_ID' => 1, 'Typ_ID' => 3],
            ['Barcode' => '2007907636590', 'Bezeichnung' => 'Kombizange 180mm',                 'Anschaffungsdatum' => '2023-04-05', 'Status_ID' => 1, 'Typ_ID' => 4],
            ['Barcode' => '2009817029400', 'Bezeichnung' => 'Fuchsschwanzsäge',                 'Anschaffungsdatum' => '2023-05-01', 'Status_ID' => 3, 'Typ_ID' => 5],
            ['Barcode' => '2000428796279', 'Bezeichnung' => 'Digitales Multimeter',             'Anschaffungsdatum' => '2023-06-12', 'Status_ID' => 1, 'Typ_ID' => 6],
            ['Barcode' => '2007729632619', 'Bezeichnung' => 'Rollgabelschlüssel 250mm',        'Anschaffungsdatum' => '2023-07-01', 'Status_ID' => 1, 'Typ_ID' => 7],
        ]);
    }
}
