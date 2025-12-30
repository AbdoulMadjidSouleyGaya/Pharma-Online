<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pharma_products', function (Blueprint $table) {
            // seuil d’alerte
            if (!Schema::hasColumn('pharma_products', 'min_quantity')) {
                $table->unsignedInteger('min_quantity')
                    ->default(20)
                    ->after('quantity');
            }

            // on crée juste la colonne supplier_id ICI (sans contrainte)
            if (!Schema::hasColumn('pharma_products', 'supplier_id')) {
                $table->unsignedBigInteger('supplier_id')
                    ->nullable()
                    ->after('min_quantity');
            }

            if (!Schema::hasColumn('pharma_products', 'expires_at')) {
                $table->date('expires_at')->nullable()->after('supplier_id');
            }

            if (!Schema::hasColumn('pharma_products', 'location')) {
                $table->string('location')->nullable()->after('expires_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pharma_products', function (Blueprint $table) {
            if (Schema::hasColumn('pharma_products', 'location')) {
                $table->dropColumn('location');
            }

            if (Schema::hasColumn('pharma_products', 'expires_at')) {
                $table->dropColumn('expires_at');
            }

            if (Schema::hasColumn('pharma_products', 'supplier_id')) {
                // au cas où une FK serait déjà là, on la drop d'abord
                try {
                    $table->dropForeign(['supplier_id']);
                } catch (\Throwable $e) {
                    // on ignore si la contrainte n'existe pas
                }
                $table->dropColumn('supplier_id');
            }

            if (Schema::hasColumn('pharma_products', 'min_quantity')) {
                $table->dropColumn('min_quantity');
            }
        });
    }
};
