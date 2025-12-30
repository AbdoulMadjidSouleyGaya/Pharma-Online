<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('guard_rotation_settings', function (Blueprint $table) {
            $table->id();
            $table->dateTime('rotation_start_at'); // 2025-12-13 13:00:00
            $table->unsignedTinyInteger('start_group')->default(1); // 1..5
            $table->unsignedTinyInteger('cycle_length')->default(5); // 5
            $table->unsignedSmallInteger('period_days')->default(7); // 7 jours
            $table->string('region')->nullable(); // Niamey (optionnel)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guard_rotation_settings');
    }
};
