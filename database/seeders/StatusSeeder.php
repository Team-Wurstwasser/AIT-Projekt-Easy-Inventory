<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('Status')->exists()) {
            return;
        }

        DB::table('Status')->insert(array_map(
            fn($b) => ['Bezeichnung' => $b],
            ['Verfügbar', 'Ausgeliehen', 'In Reparatur', 'Defekt', 'Verloren']
        ));
    }
}
