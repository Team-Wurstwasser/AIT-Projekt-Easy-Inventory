<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('Werkzeugtyp', function (Blueprint $table) {
            $table->id('Typ_ID');
            $table->string('Art');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('Werkzeugtyp');
    }
};
