<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pharma_products', function (Blueprint $table) {
            $table->id();
            $table->string('remote_id')->nullable()->index(); // id distant / fournisseur
            $table->string('libelle'); // libellé du produit
            $table->decimal('prix', 10, 2)->default(0); // prix
            $table->enum('stock', ['Disponible', 'Rupture'])->default('Disponible'); // statut stock
            $table->timestamp('synced_at')->nullable(); // date de synchronisation distante
            $table->unsignedBigInteger('pharmacy_id')->nullable()->index();
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pharma_products');
    }
};