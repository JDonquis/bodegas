<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\InventoryGeneral;
use App\Models\Product;
use App\Models\Tenant;
use App\Services\EntryService;
use App\Services\ExpenseService;
use App\Services\OutputService;
use App\Tenancy\TenantContext;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class WeightKgTest extends TestCase
{
    use DatabaseTransactions;

    protected function tearDown(): void
    {
        app(TenantContext::class)->clear();

        parent::tearDown();
    }

    public function test_weight_products_are_bought_and_sold_in_kg(): void
    {
        $tenant = Tenant::create([
            'name' => 'Bodega Peso Test',
            'is_active' => true,
            'is_default' => false,
        ]);

        app(TenantContext::class)->set($tenant);

        $product = Product::create([
            'name' => 'Mortadela 1kg',
            'barcode' => 'MORT-TEST-' . uniqid(),
            'sell_price' => 15,
            'sell_price_bs' => 0,
            'sale_type' => 'weight',
            'pricing_mode' => 'fixed',
            'profit_margin' => null,
            'price_per_kg' => 15,
            'low_stock_threshold' => 1,
        ]);

        (new EntryService)->create([
            [
                'productID' => $product->id,
                'quantity' => 2,
                'expiredDate' => null,
                'cost' => 20,
                'cost_bs' => 0,
                'lote_number' => 'LOTE-KG',
            ],
        ]);

        $inventory = Inventory::where('product_id', $product->id)->firstOrFail();

        $this->assertEqualsWithDelta(2, $inventory->stock, 0.0001);
        $this->assertEqualsWithDelta(10, $inventory->cost_per_unit, 0.0001);

        $sale = (new OutputService)->create([
            [
                'productID' => $product->id,
                'inventoryID' => $inventory->id,
                'quantity' => 0.750,
                'is_credit' => 0,
            ],
        ], null, 11.25, null, 'Efectivo Dolares');

        $inventory->refresh();
        $inventoryGeneral = InventoryGeneral::where('product_id', $product->id)->firstOrFail();
        $output = $sale->outputs()->firstOrFail();

        $this->assertEqualsWithDelta(0.750, $output->quantity, 0.0001);
        $this->assertEqualsWithDelta(1.250, $inventory->stock, 0.0001);
        $this->assertEqualsWithDelta(0.750, $inventoryGeneral->outputs, 0.0001);
        $this->assertEqualsWithDelta(1.250, $inventoryGeneral->stock, 0.0001);
        $this->assertEqualsWithDelta(3.75, $sale->total_profit, 0.01);
    }

    public function test_margin_price_uses_cost_per_kg_and_does_not_round_to_zero(): void
    {
        Cache::put('bcv_usd_rate', 0, 3600);

        $tenant = Tenant::create([
            'name' => 'Bodega Margen Test',
            'is_active' => true,
            'is_default' => false,
        ]);

        app(TenantContext::class)->set($tenant);

        $product = Product::create([
            'name' => 'Mortadela margen',
            'barcode' => 'MORT-MARGIN-' . uniqid(),
            'sell_price' => 0,
            'sell_price_bs' => 0,
            'sale_type' => 'weight',
            'pricing_mode' => 'margin',
            'profit_margin' => 20,
            'price_per_kg' => null,
            'low_stock_threshold' => 1,
        ]);

        (new EntryService)->create([
            [
                'productID' => $product->id,
                'quantity' => 1,
                'expiredDate' => null,
                'cost' => 2,
                'cost_bs' => 0,
                'lote_number' => 'LOTE-MARGIN',
            ],
        ]);

        $product->refresh();

        $this->assertEqualsWithDelta(2.4, $product->price_per_kg, 0.0001);
        $this->assertEqualsWithDelta(2.4, $product->sell_price, 0.0001);
    }

    public function test_owner_expenses_accept_fractional_kg(): void
    {
        $tenant = Tenant::create([
            'name' => 'Bodega Gasto Peso Test',
            'is_active' => true,
            'is_default' => false,
        ]);

        app(TenantContext::class)->set($tenant);

        $product = Product::create([
            'name' => 'Mortadela gasto',
            'barcode' => 'MORT-EXP-' . uniqid(),
            'sell_price' => 15,
            'sell_price_bs' => 0,
            'sale_type' => 'weight',
            'pricing_mode' => 'fixed',
            'profit_margin' => null,
            'price_per_kg' => 15,
            'low_stock_threshold' => 1,
        ]);

        (new EntryService)->create([
            [
                'productID' => $product->id,
                'quantity' => 2,
                'expiredDate' => null,
                'cost' => 20,
                'cost_bs' => 0,
                'lote_number' => 'LOTE-EXP',
            ],
        ]);

        $inventory = Inventory::where('product_id', $product->id)->firstOrFail();

        $expense = (new ExpenseService)->create([
            [
                'productID' => $product->id,
                'inventoryID' => $inventory->id,
                'quantity' => 0.500,
            ],
        ], 'Consumo de prueba');

        $inventory->refresh();

        $this->assertEqualsWithDelta(1.5, $inventory->stock, 0.0001);
        $this->assertEqualsWithDelta(5, $expense->total_cost, 0.01);
        $this->assertEqualsWithDelta(7.5, $expense->total_retail, 0.01);
    }
}
