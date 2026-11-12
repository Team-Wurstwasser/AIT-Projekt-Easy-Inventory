<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WerkzeugtypSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::table('Werkzeugtyp')->exists()) {
            return;
        }

        DB::table('Werkzeugtyp')->insert(array_map(
            fn($a) => ['Art' => $a],
            ['Hammer', 'Schraubendreher', 'Bohrmaschine', 'Zange', 'Säge', 'Messgerät', 'Schlüssel']
        ));
    }
}
