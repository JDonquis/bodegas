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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id');
            $table->foreignId('entry_id');
            $table->decimal('cost', 8, 3);
            $table->decimal('cost_per_unit', 8, 3);
            $table->decimal('profits', 8, 3);
            $table->integer('sold');
            $table->integer('stock');
            $table->date('expired_date');
            $table->string('lote_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
