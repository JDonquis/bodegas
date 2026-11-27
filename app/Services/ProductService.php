<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryGeneral;
use App\Models\Product;
use Exception;
class ProductService
{
    public function get()
    {
        $products = Product::query()
            ->with('inventoryGeneral')
            ->when(request()->input('search'), function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(name) LIKE ?', [strtolower('%'.$search.'%')])
                        ->orWhere('barcode', 'LIKE', '%'.$search.'%');
                });
            })
            ->when(request()->input('type') === 'unit', function ($query) {
                return $query->where('sale_type', 'unit');
            })
            ->when(request()->input('type') === 'weight', function ($query) {
                return $query->where('sale_type', 'weight');
            })
            ->when(request()->input('type') === 'low', function ($query) {
                return $query->where(function ($q) {
                    $q->whereDoesntHave('inventoryGeneral')
                        ->orWhereHas('inventoryGeneral', function ($inventory) {
                            $inventory->whereColumn('inventory_generals.stock', '<=', 'products.low_stock_threshold');
                        });
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        return $products;
    }

    public function store($data)
    {

        $pricingMode = $data->pricingMode ?? 'fixed';
        $isMargin = $pricingMode === 'margin';

        $product = Product::create([
            'name' => ucwords(strtolower($data->productName)),
            'barcode' => $data->barcode,
            'sell_price' => $isMargin ? 0 : round(floatval($data->sellPrice), 2),
            'sell_price_bs' => $isMargin ? 0 : ($data->sellPriceBs ? round(floatval($data->sellPriceBs), 2) : null),
            'sale_type' => $data->saleType ?? 'unit',
            'pricing_mode' => $pricingMode,
            'profit_margin' => $isMargin ? round(floatval($data->profitMargin), 2) : null,
            'price_per_kg' => ! $isMargin && $data->saleType === 'weight' ? round(floatval($data->sellPrice), 2) : null,
            'low_stock_threshold' => (int) $data->lowStockThreshold,
        ]);

        return $product;

    }

    public function update($data, $product)
    {

        $pricingMode = $data->pricingMode ?? 'fixed';
        $isMargin = $pricingMode === 'margin';

        $attributes = [
            'name' => ucwords(strtolower($data->productName)),
            'barcode' => $data->barcode,
            'sale_type' => $data->saleType ?? 'unit',
            'pricing_mode' => $pricingMode,
            'profit_margin' => $isMargin ? round(floatval($data->profitMargin), 2) : null,
            'low_stock_threshold' => (int) $data->lowStockThreshold,
        ];

        if (! $isMargin) {
            $attributes['sell_price'] = round(floatval($data->sellPrice), 2);
            $attributes['sell_price_bs'] = $data->sellPriceBs ? round(floatval($data->sellPriceBs), 2) : null;
            $attributes['price_per_kg'] = $data->saleType === 'weight' ? round(floatval($data->sellPrice), 2) : null;
        }

        $product->update($attributes);

        if ($isMargin) {
            $this->recalculateMarginPrice($product);
        }

        return $product;

    }

    public function applyMarginPrice(Product $product, float $costPerUnit)
    {
        if (! $product->isMarginPricing() || $product->profit_margin === null) {
            return;
        }

        $margin = (float) $product->profit_margin;
        $unitPrice = round($costPerUnit * (1 + ($margin / 100)), 2);

        if ($product->sale_type === 'weight') {
            $attributes = ['sell_price' => $unitPrice, 'price_per_kg' => $unitPrice];
            $displayPrice = $unitPrice;
        } else {
            $attributes = ['sell_price' => $unitPrice];
            $displayPrice = $unitPrice;
        }

        $usdRate = (new BCVService)->getUSDValue();

        if ($usdRate) {
            $attributes['sell_price_bs'] = round($displayPrice * $usdRate, 2);
        }

        $product->update($attributes);
    }

    public function recalculateMarginPrice(Product $product)
    {
        if (! $product->isMarginPricing()) {
            return;
        }

        $inventory = Inventory::where('product_id', $product->id)
            ->orderByDesc('id')
            ->first();

        if (! $inventory) {
            return;
        }

        $this->applyMarginPrice($product, (float) $inventory->cost_per_unit);
    }

    public function delete($product)
    {

        if ($product->entries()->exists()) {
            throw new Exception('No se puede eliminar el producto porque tiene compras registradas.', 400);
        }

        if ($product->outputs()->exists()) {
            throw new Exception('No se puede eliminar el producto porque tiene ventas registradas.', 400);
        }

        Inventory::where('product_id', $product->id)->delete();
        InventoryGeneral::where('product_id', $product->id)->delete();

        $product->delete();

        return 0;

    }

    // public function create($products){
    //     $entryGeneral = EntryGeneral::create(['quantity_products' => count($products) ]);

    //     $condition = 1;
    //     foreach ($products as $product) {

    //         $entry = Entry::create([
    //             'product_id' => $product['productID'],     'quantity' => $product['quantity'],
    //             'expired_date' => $product['expiredDate'], 'entry_general_id' => $entryGeneral->id
    //         ]);

    //         Inventory::create([
    //                             'product_id' => $product['productID'], 'expired_date' => $product['expiredDate'],
    //                             'stock' => $product['quantity'] ,      'condition_id' => $condition,
    //                             'entry_id' => $entry->id,
    //                         ]);

    //         $inventoryGeneral = InventoryGeneral::where('product_id',$product['productID'])->first();
    //         if(!isset($inventoryGeneral->id)){

    //             InventoryGeneral::create(['product_id' => $product['productID'],'stock' => $product['quantity'] ,'entries' => $product['quantity'] ]);
    //         }
    //         else{

    //             $inventoryGeneral->update([
    //                 'stock' => $inventoryGeneral->stock + $product['quantity'],
    //                 'entries' => $inventoryGeneral->entries + $product['quantity']

    //             ]);
    //         }

    //     }

    // }

    // public function delete($entry){

    //     $entry->load('entries');
    //     $this->deleteInventory($entry);
    //     $entry->entries()->delete();
    //     $entry->delete();

    // }

    // private function deleteInventory($entry){
    //     $entries = $entry->entries;

    //     $entryIds = $entries->pluck('id')->toArray();

    //     foreach($entries as $entryDetail){

    //         $inventoryGeneral = InventoryGeneral::with('product')->where('product_id',$entryDetail->product_id)->first();
    //         $newStock = $inventoryGeneral->stock - $entryDetail->quantity;
    //         $newEntries = $inventoryGeneral->entries - $entryDetail->quantity;

    //         if($newStock < 0)
    //             throw new Exception("No se puede eliminar esta entrada ya que el producto: ". $inventoryGeneral->product->name . ' quedaría en negativo', 500);

    //         $inventoryGeneral->update(['stock' => $newStock, 'entries' => $newEntries]);
    //     }

    //     Inventory::whereIn('entry_id',$entryIds)->delete();
    // }

}
