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
        Schema::create('inventory_generals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id');
            $table->integer('stock');
            $table->integer('entries')->default(0);
            $table->integer('outputs')->default(0);
            $table->decimal('expense', 8, 3)->default(0);
            $table->decimal('sold', 8, 3)->default(0);
            $table->decimal('profits', 8, 3)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_generals');
    }
};
