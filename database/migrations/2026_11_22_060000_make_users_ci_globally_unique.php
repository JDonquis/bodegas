<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('tenant_id', 'users_tenant_id_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_tenant_ci_unique');
            $table->unique('ci', 'users_ci_unique');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_ci_unique');
            $table->unique(['tenant_id', 'ci'], 'users_tenant_ci_unique');
        });
    }
};
