<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('Abteilung', function (Blueprint $table) {
            $table->id('Abteilung_ID');
            $table->string('Name')->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Abteilung');
    }
};
