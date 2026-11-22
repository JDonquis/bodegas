<?php

namespace Database\Seeders;

use App\Models\SuperAdmin;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('SUPERADMIN_EMAIL', 'superadmin@mibodega.local');
        $password = env('SUPERADMIN_PASSWORD', 'superadmin123');

        SuperAdmin::updateOrCreate(
            ['email' => $email],
            [
                'name' => env('SUPERADMIN_NAME', 'Super Administrador'),
                'password' => $password,
            ]
        );
    }
}
