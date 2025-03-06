<?php  

namespace App\Services;

use App\Models\Entry;
use App\Models\EntryGeneral;
use App\Models\Inventory;
use App\Models\InventoryGeneral;
use Exception;

class EntryService
{       
    public function create($products){

        $costs = array_map('floatval', array_column($products, 'cost'));
        $totalExpense = array_sum($costs);

        $entryGeneral = EntryGeneral::create(['quantity_products' => count($products), 'total_expense' => $totalExpense ]);
    
        foreach ($products as $product) {
            
            $entry = Entry::create([
                'product_id' => $product['productID'],
                'quantity' => $product['quantity'],
                'expired_date' => $product['expiredDate'],
                'entry_general_id' => $entryGeneral->id,
                'cost' => round(floatval($product['cost']),3), 
                'lote_number' => $product['lote_number'],
            ]);
            
            $costPerUnit = round(floatval($entry->cost / $entry->quantity), 4);

            Inventory::create([
                'product_id' => $product['productID'],
                'entry_id' => $entry->id,
                'cost' => $entry->cost,
                'cost_per_unit' => $costPerUnit,
                'profits' => 0,
                'sold' => 0,
                'stock' => $product['quantity'] , 
                'expired_date' => $product['expiredDate'],
                'lote_number' => $entry->lote_number,
                ]);
            
            $inventoryGeneral = InventoryGeneral::where('product_id',$product['productID'])->first();

            if(!isset($inventoryGeneral->id)){

                InventoryGeneral::create([
                    'product_id' => $product['productID'],
                    'stock' => $product['quantity'] ,
                    'entries' => $product['quantity'],
                    'outputs' => 0,
                    'expense' => $entry->cost,
                    'profits' => 0,
                ]);        
            }
            else{

                $inventoryGeneral->update([
                    'stock' => $inventoryGeneral->stock + $product['quantity'],
                    'entries' => $inventoryGeneral->entries + $product['quantity'],
                    'expense' => $inventoryGeneral->expense + $entry->cost,
                ]);
            }
            
        }


    }

    public function delete($entry){
        
        $entry->load('entries');
        $this->deleteInventory($entry);
        $entry->entries()->delete();
        $entry->delete();

        
    }

    private function deleteInventory($entry){
        $entries = $entry->entries;

        $entryIds = $entries->pluck('id')->toArray();

        foreach($entries as $entryDetail){
            

            $inventoryGeneral = InventoryGeneral::with('product')->where('product_id',$entryDetail->product_id)->first();
            $newStock = $inventoryGeneral->stock - $entryDetail->quantity;
            $newEntries = $inventoryGeneral->entries - $entryDetail->quantity;
            $newExpense = $inventoryGeneral->expense - $entryDetail->cost; 

            if($newStock < 0)
                throw new Exception("No se puede eliminar esta entrada ya que el producto: ". $inventoryGeneral->product->name . ' quedaría en negativo', 500);
                
            $inventoryGeneral->update(['stock' => $newStock, 'entries' => $newEntries, 'expense' => $newExpense]);
        }

        Inventory::whereIn('entry_id',$entryIds)->delete();
    }

}
