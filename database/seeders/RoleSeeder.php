<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['admin','pharmacist','customer'] as $r) {
            Role::firstOrCreate(['name'=>$r]);
        }
    }
}
