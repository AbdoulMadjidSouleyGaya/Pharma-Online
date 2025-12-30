<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'pharmacy_id')) {
                $table->unsignedBigInteger('pharmacy_id')->nullable()->after('id');
                $table->foreign('pharmacy_id')->references('id')->on('pharmacies')->onDelete('set null');
            }
            if (!Schema::hasColumn('users', 'password_is_temp')) {
                $table->boolean('password_is_temp')->default(false)->after('password');
            }
            if (!Schema::hasColumn('users', 'temp_password_expires_at')) {
                $table->timestamp('temp_password_expires_at')->nullable()->after('password_is_temp');
            }
        });
    }

    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'temp_password_expires_at')) $table->dropColumn('temp_password_expires_at');
            if (Schema::hasColumn('users', 'password_is_temp')) $table->dropColumn('password_is_temp');
            if (Schema::hasColumn('users', 'pharmacy_id')) {
                $table->dropForeign(['pharmacy_id']);
                $table->dropColumn('pharmacy_id');
            }
        });
    }
};
