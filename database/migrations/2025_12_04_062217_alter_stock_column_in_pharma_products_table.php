<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pharma_products', function (Blueprint $table) {
            // On force la colonne stock en VARCHAR(32) avec une valeur par défaut cohérente
            $table->string('stock', 32)->default('Disponible')->change();
        });
    }

    public function down(): void
    {
        Schema::table('pharma_products', function (Blueprint $table) {
            // Si tu veux revenir en arrière, adapte ici en fonction de ton ancien type
            // Exemple si c'était un VARCHAR(10) :
            // $table->string('stock', 10)->default('Disponible')->change();
        });
    }
};
