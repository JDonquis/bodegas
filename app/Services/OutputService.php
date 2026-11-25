<?php

namespace App\Services;

use App\Models\Client;
use App\Models\ClientDebt;
use App\Models\Inventory;
use App\Models\InventoryGeneral;
use App\Models\Output;
use App\Models\OutputGeneral;
use Exception;

class OutputService
{
    public function create($products, $client_id, $totalSold, $client_name = null, $paymentMethod = null)
    {

        $hasCredit = collect($products)->contains(function ($product) {
            return ! empty($product['is_credit']);
        });

        if ($hasCredit && ! $client_id) {
            throw new Exception('Para fiar productos debes seleccionar un cliente registrado.');
        }

        $totalOutputProfit = 0;
        $creditAmount = 0;
        $outputGeneral = OutputGeneral::create([
            'quantity_products' => count($products),
            'client_id' => $client_id ?: null,
            'client_name' => $client_name,
            'total_sold' => round(floatval($totalSold), 2),
            'total_profit' => 0,
            'payment_method' => $paymentMethod ?: null,
        ]);

        foreach ($products as $product) {

            $this->validateStock($product);

            $inventory = Inventory::where('id', $product['inventoryID'])->with('product')->first();
            $detailProfit = $this->calculateDetailProfit($inventory, $product['quantity']);
            $inventory->update([
                'stock' => $inventory->stock - $product['quantity'],
                'sold' => $inventory->sold + $product['quantity'],
                'profits' => $inventory->profits + $detailProfit,
            ]);

            $inventoryGeneral = InventoryGeneral::where('product_id', $product['productID'])->first();
            $effectivePrice = $this->getEffectiveSellPrice($inventory->product);
            $inventoryGeneral->update([
                'stock' => $inventoryGeneral->stock - $product['quantity'],
                'outputs' => $inventoryGeneral->outputs + $product['quantity'],
                'sold' => $inventoryGeneral->sold + ($product['quantity'] * $effectivePrice),
                'profits' => $inventoryGeneral->profits + $detailProfit,
            ]);

            $isCredit = ! empty($product['is_credit']);

            Output::create([
                'product_id' => $product['productID'],
                'output_general_id' => $outputGeneral->id,
                'inventory_id' => $product['inventoryID'],
                'quantity' => $product['quantity'],
                'is_credit' => $isCredit,
                'expired_date' => $inventory->expired_date ?? now(),
                'profit' => $detailProfit,
            ]);

            if ($isCredit) {
                $creditAmount += round($effectivePrice * $product['quantity'], 2);
            }

            $totalOutputProfit = $totalOutputProfit + $detailProfit;
        }

        $outputGeneral->update(['total_profit' => $totalOutputProfit]);

        if ($creditAmount > 0) {
            $client = Client::find($client_id);

            if (! $client) {
                throw new Exception('Para fiar productos debes seleccionar un cliente registrado.');
            }

            ClientDebt::create([
                'client_id' => $client->id,
                'output_general_id' => $outputGeneral->id,
                'amount' => $creditAmount,
                'description' => 'Fiado de venta #' . $outputGeneral->id,
            ]);

            $client->update([
                'debt' => round((float) $client->debt + $creditAmount, 2),
            ]);
        }

        return $outputGeneral;

    }

    public function delete($output)
    {

        $output->load('outputs');
        $this->removeCreditDebts($output);
        $this->addInventory($output);
        $output->outputs()->delete();
        $output->delete();

    }

    private function removeCreditDebts($output)
    {
        $debts = ClientDebt::where('output_general_id', $output->id)
            ->with(['client', 'payments'])
            ->get();

        foreach ($debts as $debt) {
            $paid = $debt->payments->sum('amount');
            $remaining = max(0, round((float) $debt->amount - (float) $paid, 2));

            if ($debt->client && $remaining > 0) {
                $debt->client->update([
                    'debt' => max(0, round((float) $debt->client->debt - $remaining, 2)),
                ]);
            }

            $debt->delete();
        }
    }

    private function addInventory($output)
    {
        $outputs = $output->outputs;

        $entryIds = $outputs->pluck('id')->toArray();

        foreach ($outputs as $outputDetail) {

            $inventory = Inventory::where('id', $outputDetail->inventory_id)->with('product')->first();
            $inventory->update([
                'stock' => $inventory->stock + $outputDetail->quantity,
                'profits' => $inventory->profits - $outputDetail->profit,
                'sold' => $inventory->sold - $outputDetail->quantity,
            ]);

            $inventoryGeneral = InventoryGeneral::with('product')->where('product_id', $outputDetail->product_id)->first();
            $effectivePrice = $this->getEffectiveSellPrice($inventory->product);
            $newStock = $inventoryGeneral->stock + $outputDetail->quantity;
            $newOutputs = $inventoryGeneral->outputs - $outputDetail->quantity;
            $newProfits = $inventoryGeneral->profits - $outputDetail->profit;
            $newSold = $inventoryGeneral->sold - ($outputDetail->quantity * $effectivePrice);

            $inventoryGeneral->update([
                'stock' => $newStock,
                'outputs' => $newOutputs,
                'sold' => $newSold,
                'profits' => $newProfits,
            ]);
        }

    }

    private function validateStock($product)
    {
        $inventory = Inventory::with('product')->where('id', $product['inventoryID'])->first();

        if ($inventory->stock < $product['quantity']) {
            throw new Exception('La cantidad del producto: '.$inventory->product->name.' - '.$inventory->expired_date.' supera el stock disponible', 500);
        }

    }

    public function calculateDetailProfit($inventory, $quantity)
    {
        $product = $inventory->product;
        $sellPrice = $product->sale_type === 'weight' && $product->price_per_kg
            ? $product->price_per_kg / 1000
            : $product->sell_price;

        $totalCost = round($inventory->cost_per_unit * $quantity, 2);
        $totalSold = round($sellPrice * $quantity, 2);
        $totalProfit = round($totalSold - $totalCost, 2);

        return $totalProfit;
    }

    public function getEffectiveSellPrice($product)
    {
        return $product->sale_type === 'weight' && $product->price_per_kg
            ? $product->price_per_kg / 1000
            : $product->sell_price;
    }
}
