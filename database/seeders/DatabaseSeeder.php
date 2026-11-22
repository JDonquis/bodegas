<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Tenancy\TenantContext;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TenantSeeder::class,
            SuperAdminSeeder::class,
        ]);

        $tenant = Tenant::where('is_default', true)->first();
        $context = app(TenantContext::class);

        if ($tenant) {
            $context->set($tenant);
        }

        try {
            $this->call([
                TypeUserSeeder::class,
                RoleSeeder::class,
                UserSeeder::class,
                ClientSeeder::class,
                ProductSeeder::class,
            ]);
        } finally {
            $context->clear();
        }
    }
}
