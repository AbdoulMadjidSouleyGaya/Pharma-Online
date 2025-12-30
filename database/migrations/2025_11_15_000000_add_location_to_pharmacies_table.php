<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * ⚠ Contexte :
         * - Les colonnes latitude / longitude sont déjà créées dans
         *   2025_09_14_170303_create_pharmacies_table.php
         *   puis éventuellement manipulées dans 2025_09_14_171952_drop_geo_and_guard_from_pharmacies_table.php
         *
         * Cette migration doit donc être 100 % "safe" :
         * - ne PAS planter si les colonnes existent déjà,
         * - seulement les ajouter si elles n’existent pas.
         */

        Schema::table('pharmacies', function (Blueprint $table) {
            // On vérifie avant d'ajouter pour éviter les erreurs de type "Column already exists"
            if (! Schema::hasColumn('pharmacies', 'latitude')) {
                // Si la colonne n'existe pas encore, on l'ajoute (idéalement après api_token)
                $table->decimal('latitude', 10, 7)
                    ->nullable()
                    ->after('api_token');
            }

            if (! Schema::hasColumn('pharmacies', 'longitude')) {
                $table->decimal('longitude', 10, 7)
                    ->nullable()
                    ->after('latitude');
            }
        });
    }

    public function down(): void
    {
        /**
         * ⚠ Important :
         * On ne veut pas supprimer des colonnes qui pourraient avoir été créées
         * par une migration précédente.
         *
         * Donc ici on ne drop que si elles existent, et uniquement dans le cadre
         * d'un rollback intentionnel de cette migration.
         */

        Schema::table('pharmacies', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('pharmacies', 'latitude')) {
                $columnsToDrop[] = 'latitude';
            }
            if (Schema::hasColumn('pharmacies', 'longitude')) {
                $columnsToDrop[] = 'longitude';
            }

            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
