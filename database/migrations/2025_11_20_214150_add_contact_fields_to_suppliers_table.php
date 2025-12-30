<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            // 💡 On ajoute contact, phone et address si elles n'existent pas encore
            if (!Schema::hasColumn('suppliers', 'contact')) {
                $table->string('contact')->nullable()->after('name');
            }

            if (!Schema::hasColumn('suppliers', 'phone')) {
                $table->string('phone', 50)->nullable()->after('contact');
            }

            if (!Schema::hasColumn('suppliers', 'address')) {
                $table->string('address', 500)->nullable()->after('email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            if (Schema::hasColumn('suppliers', 'contact')) {
                $table->dropColumn('contact');
            }
            if (Schema::hasColumn('suppliers', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('suppliers', 'address')) {
                $table->dropColumn('address');
            }
        });
    }
};
