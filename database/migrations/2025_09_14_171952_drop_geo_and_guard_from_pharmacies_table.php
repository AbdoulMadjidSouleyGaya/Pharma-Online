<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // On teste l'existence des colonnes avant de drop pour éviter les erreurs
        if (Schema::hasColumn('pharmacies', 'latitude')) {
            Schema::table('pharmacies', function (Blueprint $table) {
                $table->dropColumn('latitude');
            });
        }
        if (Schema::hasColumn('pharmacies', 'longitude')) {
            Schema::table('pharmacies', function (Blueprint $table) {
                $table->dropColumn('longitude');
            });
        }
        if (Schema::hasColumn('pharmacies', 'is_on_duty')) {
            Schema::table('pharmacies', function (Blueprint $table) {
                $table->dropColumn('is_on_duty');
            });
        }
    }

    public function down(): void
    {
        Schema::table('pharmacies', function (Blueprint $table) {
            if (!Schema::hasColumn('pharmacies','latitude')) {
                $table->decimal('latitude', 10, 7)->nullable()->after('address');
            }
            if (!Schema::hasColumn('pharmacies','longitude')) {
                $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('pharmacies','is_on_duty')) {
                $table->boolean('is_on_duty')->default(false)->after('email');
            }
        });
    }
};
