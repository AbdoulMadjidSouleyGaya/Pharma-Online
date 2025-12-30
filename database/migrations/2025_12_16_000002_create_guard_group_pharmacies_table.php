<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('guard_group_pharmacies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guard_group_id')->constrained('guard_groups')->cascadeOnDelete();
            $table->string('name');
            $table->string('district')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();

            $table->index(['name','district']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guard_group_pharmacies');
    }
};
