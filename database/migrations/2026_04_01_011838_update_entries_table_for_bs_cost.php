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
        Schema::table('entries', function (Blueprint $table) {
            $table->decimal('cost', 15, 2)->change();
            $table->decimal('cost_bs', 15, 2)->nullable()->after('cost');
            $table->date('expired_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->decimal('cost', 8, 3)->change();
            $table->dropColumn('cost_bs');
            $table->date('expired_date')->nullable(false)->change();
        });
    }
};
