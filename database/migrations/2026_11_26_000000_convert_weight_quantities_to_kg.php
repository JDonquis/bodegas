<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->decimal('quantity', 12, 3)->change();
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->decimal('stock', 12, 3)->change();
            $table->decimal('sold', 12, 3)->change();
            $table->decimal('cost_per_unit', 15, 6)->change();
        });

        Schema::table('inventory_generals', function (Blueprint $table) {
            $table->decimal('stock', 12, 3)->change();
            $table->decimal('entries', 12, 3)->change();
            $table->decimal('outputs', 12, 3)->change();
        });

        Schema::table('outputs', function (Blueprint $table) {
            $table->decimal('quantity', 12, 3)->change();
        });

        Schema::table('expense_items', function (Blueprint $table) {
            $table->decimal('quantity', 12, 3)->change();
        });

        DB::statement("
            UPDATE entries e
            JOIN products p ON p.id = e.product_id
            SET e.quantity = e.quantity / 1000
            WHERE p.sale_type = 'weight'
        ");

        DB::statement("
            UPDATE inventories i
            JOIN products p ON p.id = i.product_id
            SET i.stock = i.stock / 1000,
                i.sold = i.sold / 1000
            WHERE p.sale_type = 'weight'
        ");

        DB::statement("
            UPDATE inventory_generals ig
            JOIN products p ON p.id = ig.product_id
            SET ig.stock = ig.stock / 1000,
                ig.entries = ig.entries / 1000,
                ig.outputs = ig.outputs / 1000
            WHERE p.sale_type = 'weight'
        ");

        DB::statement("
            UPDATE outputs o
            JOIN products p ON p.id = o.product_id
            SET o.quantity = o.quantity / 1000
            WHERE p.sale_type = 'weight'
        ");

        DB::statement("
            UPDATE expense_items
            SET quantity = quantity / 1000,
                cost_per_unit = cost_per_unit * 1000,
                sell_price_unit = sell_price_unit * 1000
            WHERE sale_type = 'weight'
        ");

        DB::statement("
            UPDATE inventories i
            JOIN entries e ON e.id = i.entry_id
            SET i.cost_per_unit = CASE
                WHEN e.quantity > 0 THEN ROUND(i.cost / e.quantity, 6)
                ELSE 0
            END
            WHERE i.product_id IN (
                SELECT id FROM products WHERE sale_type = 'weight'
            )
        ");

        $marginProducts = DB::table('products')
            ->where('sale_type', 'weight')
            ->where('pricing_mode', 'margin')
            ->get();

        foreach ($marginProducts as $product) {
            $latestInventory = DB::table('inventories')
                ->where('product_id', $product->id)
                ->orderByDesc('id')
                ->first();

            if (! $latestInventory) {
                continue;
            }

            $price = round(
                (float) $latestInventory->cost_per_unit * (1 + ((float) $product->profit_margin / 100)),
                2
            );

            DB::table('products')->where('id', $product->id)->update([
                'sell_price' => $price,
                'price_per_kg' => $price,
            ]);
        }
    }

    public function down(): void
    {
        DB::statement("
            UPDATE entries e
            JOIN products p ON p.id = e.product_id
            SET e.quantity = e.quantity * 1000
            WHERE p.sale_type = 'weight'
        ");

        DB::statement("
            UPDATE inventories i
            JOIN products p ON p.id = i.product_id
            SET i.stock = i.stock * 1000,
                i.sold = i.sold * 1000,
                i.cost_per_unit = i.cost_per_unit / 1000
            WHERE p.sale_type = 'weight'
        ");

        DB::statement("
            UPDATE inventory_generals ig
            JOIN products p ON p.id = ig.product_id
            SET ig.stock = ig.stock * 1000,
                ig.entries = ig.entries * 1000,
                ig.outputs = ig.outputs * 1000
            WHERE p.sale_type = 'weight'
        ");

        DB::statement("
            UPDATE outputs o
            JOIN products p ON p.id = o.product_id
            SET o.quantity = o.quantity * 1000
            WHERE p.sale_type = 'weight'
        ");

        DB::statement("
            UPDATE expense_items
            SET quantity = quantity * 1000,
                cost_per_unit = cost_per_unit / 1000,
                sell_price_unit = sell_price_unit / 1000
            WHERE sale_type = 'weight'
        ");

        Schema::table('entries', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->integer('stock')->change();
            $table->integer('sold')->change();
            $table->decimal('cost_per_unit', 8, 4)->change();
        });

        Schema::table('inventory_generals', function (Blueprint $table) {
            $table->integer('stock')->change();
            $table->integer('entries')->change();
            $table->integer('outputs')->change();
        });

        Schema::table('outputs', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });

        Schema::table('expense_items', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });
    }
};
