<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pharmacy_id')->index();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->foreign('pharmacy_id')
                ->references('id')
                ->on('pharmacies')
                ->onDelete('cascade');
        });

        // 🔗 Maintenant que suppliers existe, on ajoute la FK sur pharma_products.supplier_id
        Schema::table('pharma_products', function (Blueprint $table) {
            if (Schema::hasColumn('pharma_products', 'supplier_id')) {
                // on essaie d’attacher la FK proprement
                $table->foreign('supplier_id')
                    ->references('id')
                    ->on('suppliers')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        // On retire la FK avant de drop la table suppliers
        Schema::table('pharma_products', function (Blueprint $table) {
            if (Schema::hasColumn('pharma_products', 'supplier_id')) {
                try {
                    $table->dropForeign(['supplier_id']);
                } catch (\Throwable $e) {
                    // ignore si déjà supprimée
                }
            }
        });

        Schema::dropIfExists('suppliers');
    }
};
