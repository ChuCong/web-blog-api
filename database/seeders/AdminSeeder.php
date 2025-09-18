<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Admin;
use App\Core\AppConst;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::updateOrCreate(['id' => AppConst::ID_SUPER_ADMIN], [
            'name' => 'Super Admin',
            'user_name' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin@2025'),
            'created_at' => now(),
            'updated_at' => now(),
            'is_super_admin' => 1,
        ]);
    }
}
