<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'users',
            'clients',
            'client_debts',
            'client_payments',
            'products',
            'entries',
            'entry_generals',
            'outputs',
            'output_generals',
            'inventories',
            'inventory_generals',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('tenant_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('tenants')
                    ->cascadeOnDelete();
            });
        }

        $defaultTenantId = DB::table('tenants')->where('is_default', true)->value('id')
            ?? DB::table('tenants')->min('id');

        if ($defaultTenantId) {
            foreach ($tables as $tableName) {
                DB::table($tableName)
                    ->whereNull('tenant_id')
                    ->update(['tenant_id' => $defaultTenantId]);
            }
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_ci_unique');
            $table->unique(['tenant_id', 'ci'], 'users_tenant_ci_unique');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique('products_barcode_unique');
            $table->unique(['tenant_id', 'barcode'], 'products_tenant_barcode_unique');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_tenant_ci_unique');
            $table->unique('ci', 'users_ci_unique');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique('products_tenant_barcode_unique');
            $table->unique('barcode', 'products_barcode_unique');
        });

        $tables = [
            'users',
            'clients',
            'client_debts',
            'client_payments',
            'products',
            'entries',
            'entry_generals',
            'outputs',
            'output_generals',
            'inventories',
            'inventory_generals',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropConstrainedForeignId('tenant_id');
            });
        }
    }
};
