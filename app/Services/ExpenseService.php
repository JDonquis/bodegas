<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\ExpenseItem;
use App\Models\Inventory;
use App\Models\InventoryGeneral;
use Exception;

class ExpenseService
{
    public function create($items, $description = null)
    {
        $expense = Expense::create([
            'description' => $description,
            'total_cost' => 0,
            'total_retail' => 0,
        ]);

        $totalCost = 0;
        $totalRetail = 0;

        foreach ($items as $item) {
            $inventory = Inventory::with('product')->whereKey($item['inventoryID'])->first();

            if (! $inventory || ! $inventory->product) {
                throw new Exception('El producto seleccionado no existe en el inventario.');
            }

            if ((int) $inventory->product_id !== (int) $item['productID']) {
                throw new Exception('El producto no corresponde al lote de inventario seleccionado.');
            }

            $quantity = (int) $item['quantity'];

            if ($quantity < 1) {
                throw new Exception('La cantidad del producto debe ser mayor a cero.');
            }

            if ((int) $inventory->stock < $quantity) {
                throw new Exception(
                    'La cantidad de ' . $inventory->product->name . ' supera el stock disponible.'
                );
            }

            $product = $inventory->product;
            $costPerUnit = round((float) $inventory->cost_per_unit, 4);
            $sellPriceUnit = round($this->getEffectiveSellPrice($product), 4);
            $subtotalCost = round($costPerUnit * $quantity, 2);
            $subtotalRetail = round($sellPriceUnit * $quantity, 2);

            $inventory->update([
                'stock' => $inventory->stock - $quantity,
            ]);

            $inventoryGeneral = InventoryGeneral::where('product_id', $product->id)->first();

            if ($inventoryGeneral) {
                $inventoryGeneral->update([
                    'stock' => $inventoryGeneral->stock - $quantity,
                ]);
            }

            ExpenseItem::create([
                'expense_id' => $expense->id,
                'product_id' => $product->id,
                'inventory_id' => $inventory->id,
                'product_name' => $product->name,
                'sale_type' => $product->sale_type ?? 'unit',
                'quantity' => $quantity,
                'cost_per_unit' => $costPerUnit,
                'sell_price_unit' => $sellPriceUnit,
                'subtotal_cost' => $subtotalCost,
                'subtotal_retail' => $subtotalRetail,
            ]);

            $totalCost += $subtotalCost;
            $totalRetail += $subtotalRetail;
        }

        $expense->update([
            'total_cost' => round($totalCost, 2),
            'total_retail' => round($totalRetail, 2),
        ]);

        return $expense;
    }

    public function delete(Expense $expense)
    {
        $expense->load('items');

        foreach ($expense->items as $item) {
            $inventory = $item->inventory_id ? Inventory::find($item->inventory_id) : null;

            if ($inventory) {
                $inventory->update([
                    'stock' => $inventory->stock + $item->quantity,
                ]);
            }

            if ($item->product_id) {
                $inventoryGeneral = InventoryGeneral::where('product_id', $item->product_id)->first();

                if ($inventoryGeneral) {
                    $inventoryGeneral->update([
                        'stock' => $inventoryGeneral->stock + $item->quantity,
                    ]);
                }
            }
        }

        $expense->items()->delete();
        $expense->delete();
    }

    private function getEffectiveSellPrice($product)
    {
        if ($product->sale_type === 'weight' && $product->price_per_kg) {
            return (float) $product->price_per_kg / 1000;
        }

        return (float) $product->sell_price;
    }
}
