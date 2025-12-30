<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('customer_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('number');          // N° lisible (1..N par user)
            $table->unsignedInteger('count');           // nb produits
            $table->unsignedBigInteger('total');        // total en F
            $table->string('status', 20)                // en_attente | en_cours | valide | rejete | annulee
                  ->default('en_attente')->index();
            $table->json('items');                      // [{id, libelle, prix}, ...]
            // Optionnel pour plus tard : rattacher à une pharmacie
            $table->foreignId('pharmacy_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('customer_orders');
    }
};
