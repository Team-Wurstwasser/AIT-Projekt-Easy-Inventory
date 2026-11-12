<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('Mitarbeiter', function (Blueprint $table) {
            $table->id('Mitarbeiter_ID');
            $table->string('Vorname');
            $table->string('Nachname');
            $table->string('Email')->unique();
            $table->foreignId('Abteilung_ID')->constrained('Abteilung', 'Abteilung_ID');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Mitarbeiter');
    }
};
