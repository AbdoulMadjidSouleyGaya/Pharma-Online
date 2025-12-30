<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('guard_groups', function (Blueprint $table) {
            $table->id(); // 1..5
            $table->string('label')->nullable(); // "Groupe 1"...
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guard_groups');
    }
};
