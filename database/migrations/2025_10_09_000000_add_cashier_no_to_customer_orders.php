<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->unsignedTinyInteger('cashier_no')->nullable()->after('status');
        });
    }
    public function down(): void
    {
        Schema::table('customer_orders', function (Blueprint $table) {
            $table->dropColumn('cashier_no');
        });
    }
};