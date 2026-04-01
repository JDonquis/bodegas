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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('sell_price', 15, 2)->change();
            $table->decimal('sell_price_bs', 15, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('sell_price', 8, 3)->change();
            $table->decimal('sell_price_bs', 15, 3)->change();
        });
    }
};
