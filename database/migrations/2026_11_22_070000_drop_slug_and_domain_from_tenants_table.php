<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropUnique('tenants_slug_unique');
            $table->dropUnique('tenants_domain_unique');
            $table->dropColumn(['slug', 'domain']);
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique();
            $table->string('domain')->nullable()->unique();
        });
    }
};
