<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('pharmacies', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('district');            // Quartier
      $table->string('address')->nullable(); // Adresse (optionnel)
      $table->decimal('latitude', 10, 7)->nullable();
      $table->decimal('longitude', 10, 7)->nullable();
      $table->string('phone')->nullable();
      $table->string('email')->nullable();
      $table->boolean('is_on_duty')->default(false);
      $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
      $table->timestamps();

      $table->index(['name']);
      $table->index(['district']);
    });
  }
  public function down(): void { Schema::dropIfExists('pharmacies'); }
};
