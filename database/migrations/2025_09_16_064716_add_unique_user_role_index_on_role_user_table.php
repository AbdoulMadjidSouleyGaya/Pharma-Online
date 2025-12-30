<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $exists = collect(
            DB::select("SHOW INDEX FROM role_user WHERE Key_name = 'role_user_user_id_role_id_unique'")
        )->isNotEmpty();

        if (! $exists) {
            Schema::table('role_user', function (Blueprint $table) {
                $table->unique(['user_id','role_id'], 'role_user_user_id_role_id_unique');
            });
        }
    }

    public function down(): void
    {
        $exists = collect(
            DB::select("SHOW INDEX FROM role_user WHERE Key_name = 'role_user_user_id_role_id_unique'")
        )->isNotEmpty();

        if ($exists) {
            Schema::table('role_user', function (Blueprint $table) {
                $table->dropUnique('role_user_user_id_role_id_unique');
            });
        }
    }
};
