<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('Status', function (Blueprint $table) {
            $table->id('Status_ID');
            $table->string('Bezeichnung');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Status');
    }
};
