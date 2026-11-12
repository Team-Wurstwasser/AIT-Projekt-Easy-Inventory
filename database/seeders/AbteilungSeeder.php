<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AbteilungSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('Abteilung')->exists()) {
            return;
        }

        DB::table('Abteilung')->insert(array_map(
            fn($n) => ['Name' => $n],
            ['Produktion', 'Entwicklung', 'Lager', 'Verwaltung', 'Qualitätssicherung', 'Vertrieb']
        ));
    }
}
