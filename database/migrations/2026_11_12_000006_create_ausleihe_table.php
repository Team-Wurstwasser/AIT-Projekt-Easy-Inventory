<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('Ausleihe', function (Blueprint $table) {
            $table->id('Ausleih_ID');
            $table->date('Ausleihdatum');
            $table->integer('Ausleihdauer');
            $table->date('Rückgabedatum')->nullable();
            $table->text('ZustandBeiRückgabe')->nullable();
            $table->foreignId('Mitarbeiter_ID')->constrained('Mitarbeiter', 'Mitarbeiter_ID')->cascadeOnDelete();
            $table->char('Barcode', 13);
            $table->boolean('EmailVersendet')->default(false);

            $table->foreign('Barcode')->references('Barcode')->on('Werkzeuge')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Ausleihe');
    }
};
