<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('customer_orders', function (Blueprint $table) {
            if (! Schema::hasColumn('customer_orders', 'prescription_path')) {
                $table->string('prescription_path')->nullable()->after('items');
            }

            if (! Schema::hasColumn('customer_orders', 'prescription_original_name')) {
                $table->string('prescription_original_name')->nullable()->after('prescription_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customer_orders', function (Blueprint $table) {
            if (Schema::hasColumn('customer_orders', 'prescription_original_name')) {
                $table->dropColumn('prescription_original_name');
            }

            if (Schema::hasColumn('customer_orders', 'prescription_path')) {
                $table->dropColumn('prescription_path');
            }
        });
    }
};
