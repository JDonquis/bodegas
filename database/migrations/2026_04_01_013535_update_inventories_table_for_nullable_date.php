<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->decimal('cost', 15, 2)->change();
            $table->decimal('cost_per_unit', 15, 2)->change();
            $table->decimal('profits', 15, 2)->change();
            $table->date('expired_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->decimal('cost', 8, 3)->change();
            $table->decimal('cost_per_unit', 8, 4)->change();
            $table->decimal('profits', 8, 3)->change();
            $table->date('expired_date')->nullable(false)->change();
        });
    }
};
