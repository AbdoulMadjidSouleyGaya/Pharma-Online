<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('guard_pharmacies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guard_schedule_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('district')->nullable();   // Quartier
            $table->string('address')->nullable();    // Localisation
            $table->string('phone')->nullable();      // Contact
            $table->timestamps();
            $table->index(['name','district']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('guard_pharmacies');
    }
};
