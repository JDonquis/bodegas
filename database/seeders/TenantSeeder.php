<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        if (Tenant::where('is_default', true)->exists()) {
            return;
        }

        Tenant::create([
            'name' => config('app.name') ?: 'Bodega Principal',
            'is_active' => true,
            'is_default' => true,
        ]);
    }
}
