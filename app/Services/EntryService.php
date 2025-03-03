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
        $entryGeneral = EntryGeneral::create(['quantity_products' => count($products) ]);
    
        $condition = 1;
        foreach ($products as $product) {
            
            $entry = Entry::create([
                'product_id' => $product['productID'],     'quantity' => $product['quantity'],
                'expired_date' => $product['expiredDate'], 'entry_general_id' => $entryGeneral->id 
            ]);
                    
            Inventory::create([
                                'product_id' => $product['productID'], 'expired_date' => $product['expiredDate'],
                                'stock' => $product['quantity'] ,      'condition_id' => $condition, 
                                'entry_id' => $entry->id,
                            ]);
            
            $inventoryGeneral = InventoryGeneral::where('product_id',$product['productID'])->first();
            if(!isset($inventoryGeneral->id)){

                InventoryGeneral::create(['product_id' => $product['productID'],'stock' => $product['quantity'] ,'entries' => $product['quantity'] ]);        
            }
            else{

                $inventoryGeneral->update([
                    'stock' => $inventoryGeneral->stock + $product['quantity'],
                    'entries' => $inventoryGeneral->entries + $product['quantity']

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

            if($newStock < 0)
                throw new Exception("No se puede eliminar esta entrada ya que el producto: ". $inventoryGeneral->product->name . ' quedaría en negativo', 500);
                
            $inventoryGeneral->update(['stock' => $newStock, 'entries' => $newEntries]);
        }

        Inventory::whereIn('entry_id',$entryIds)->delete();
    }

}
