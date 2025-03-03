<?php  

namespace App\Services;

use App\Models\Output;
use App\Models\OutputGeneral;
use App\Models\Inventory;
use App\Models\InventoryGeneral;
use Exception;

class OutputService
{       
    public function create($products, $destiny){

        $outputGeneral = OutputGeneral::create(['quantity_products' => count($products), 'destiny' =>  $destiny]);
    
        foreach ($products as $product) {
            
            $this->validateStock($product);

            $inventory = Inventory::where('id',$product['inventoryID'])->first();
            $inventory->update(['stock' => $inventory->stock - $product['quantity']]);
            

            $inventoryGeneral = InventoryGeneral::where('product_id',$product['productID'])->first();
            $inventoryGeneral->update([
                'stock' => $inventoryGeneral->stock - $product['quantity'],
                'outputs' => $inventoryGeneral->outputs + $product['quantity']
            ]);


            Output::create([
                'product_id' => $product['productID'],     'quantity' => $product['quantity'],
                'inventory_id' => $product['inventoryID'], 'output_general_id' => $outputGeneral->id,
                'expired_date' => $inventory->expired_date, 
            ]);
        }

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

}
