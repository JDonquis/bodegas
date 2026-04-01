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
        Schema::table('output_generals', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->change();
            $table->string('client_name')->nullable()->after('client_id');
            $table->decimal('total_sold', 15, 2)->change();
            $table->decimal('total_profit', 15, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('output_generals', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable(false)->change();
            $table->dropColumn('client_name');
            $table->decimal('total_sold', 8, 3)->change();
            $table->decimal('total_profit', 8, 3)->change();
        });
    }
};
