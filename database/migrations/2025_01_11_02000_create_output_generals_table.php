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
        Schema::create('output_generals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id');
            $table->integer('quantity_products');
            $table->decimal('total_sold', 8, 2);
            $table->decimal('total_profit', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('output_generals');
    }
};
