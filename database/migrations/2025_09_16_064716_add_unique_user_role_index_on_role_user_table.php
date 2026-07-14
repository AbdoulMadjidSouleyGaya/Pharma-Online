<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasIndex('role_user', 'role_user_user_id_role_id_unique')) {
            Schema::table('role_user', function (Blueprint $table) {
                $table->unique(['user_id','role_id'], 'role_user_user_id_role_id_unique');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasIndex('role_user', 'role_user_user_id_role_id_unique')) {
            Schema::table('role_user', function (Blueprint $table) {
                $table->dropUnique('role_user_user_id_role_id_unique');
            });
        }
    }
};
