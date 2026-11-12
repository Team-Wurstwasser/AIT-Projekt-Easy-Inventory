<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('Werkzeuge', function (Blueprint $table) {
            $table->char('Barcode', 13)->primary();
            $table->string('Bezeichnung');
            $table->date('Anschaffungsdatum')->nullable();
            $table->foreignId('Status_ID')->constrained('Status', 'Status_ID');
            $table->foreignId('Typ_ID')->constrained('Werkzeugtyp', 'Typ_ID');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Werkzeuge');
    }
};
