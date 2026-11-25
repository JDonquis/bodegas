<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('expense_id')->constrained('expenses')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('inventory_id')->nullable()->constrained('inventories')->nullOnDelete();
            $table->string('product_name');
            $table->string('sale_type')->default('unit');
            $table->integer('quantity');
            $table->decimal('cost_per_unit', 15, 4)->default(0);
            $table->decimal('sell_price_unit', 15, 4)->default(0);
            $table->decimal('subtotal_cost', 15, 2)->default(0);
            $table->decimal('subtotal_retail', 15, 2)->default(0);
            $table->timestamps();

            $table->index(['tenant_id', 'expense_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_items');
    }
};
