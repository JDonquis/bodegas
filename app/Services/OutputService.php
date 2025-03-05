<?php  

namespace App\Services;

use App\Models\Output;
use App\Models\OutputGeneral;
use App\Models\Inventory;
use App\Models\InventoryGeneral;
use Exception;

class OutputService
{       
    public function create($products, $client_id, $totalSold){

        
        $totalOutputProfit = 0;
        $outputGeneral = OutputGeneral::create([
            'quantity_products' => count($products),
            'client_id' =>  $client_id,
            'total_sold' => $totalSold,
            'total_profit' => 0,
        ]);
    
        foreach ($products as $product) {
            
            $this->validateStock($product);

            $inventory = Inventory::where('id',$product['inventoryID'])->with('product')->first();
            $detailProfit = $this->calculateDetailProfit($inventory,$product['quantity']);
            $inventory->update([
                'stock' => $inventory->stock - $product['quantity'],
                'sold' => $inventory->sold + $product['quantity'],
                'profits' => $inventory->profits + $detailProfit,
            ]);
            

            $inventoryGeneral = InventoryGeneral::where('product_id',$product['productID'])->first();
            $inventoryGeneral->update([
                'stock' => $inventoryGeneral->stock - $product['quantity'],
                'outputs' => $inventoryGeneral->outputs + $product['quantity'],
                'profits' => $inventoryGeneral->profits + $detailProfit
            ]);


            Output::create([
                'product_id' => $product['productID'],
                'output_general_id' => $outputGeneral->id,
                'inventory_id' => $product['inventoryID'],
                'quantity' => $product['quantity'],
                'expired_date' => $inventory->expired_date, 
                'profit' => $detailProfit,
            ]);

            $totalOutputProfit = $totalOutputProfit + $detailProfit;
        }

        $outputGeneral->update(['total_profit' => $totalOutputProfit]);

        return $outputGeneral;

    }

    public function delete($output){
        
        $output->load('outputs');
        $this->addInventory($output);
        $output->outputs()->delete();
        $output->delete();

        
    }

    private function addInventory($output){
        $outputs = $output->outputs;

        $entryIds = $outputs->pluck('id')->toArray();

        foreach($outputs as $outputDetail){
            
            $inventory = Inventory::where('id',$outputDetail->inventory_id)->first();
            $inventory ->update(['stock' => $inventory->stock + $outputDetail->quantity]);



            $inventoryGeneral = InventoryGeneral::with('product')->where('product_id',$outputDetail->product_id)->first();
            $newStock = $inventoryGeneral->stock + $outputDetail->quantity;
            $newOutputs = $inventoryGeneral->outputs - $outputDetail->quantity;
                
            $inventoryGeneral->update(['stock' => $newStock, 'outputs' => $newOutputs]);
        }

    }

    private function validateStock($product){
        $inventory = Inventory::with('product')->where('id',$product['inventoryID'])->first();
        
        if($inventory->stock < $product['quantity'])
            throw new Exception("La cantidad del producto: " . $inventory->product->name . " - " . $inventory->expired_date . " supera el stock disponible", 500);
            
    }

    public function calculateDetailProfit($inventory,$quantity){
        $totalCost = $inventory->cost_per_unit * $quantity;
        $totalSold = $inventory->product->sell_price * $quantity;
        $totalProfit = $totalSold - $totalCost;
        return $totalProfit;
    }

}
