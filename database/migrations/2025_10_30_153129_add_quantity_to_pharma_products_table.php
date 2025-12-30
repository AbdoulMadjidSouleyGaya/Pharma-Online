<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pharma_products', function (Blueprint $table) {
            // quantité réelle, 0 par défaut
            $table->unsignedInteger('quantity')->default(0)->after('stock');
        });
    }

    public function down(): void
    {
        Schema::table('pharma_products', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
};
