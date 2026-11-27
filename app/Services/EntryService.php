<?php  

namespace App\Services;

use App\Models\Entry;
use App\Models\EntryGeneral;
use App\Models\Inventory;
use App\Models\InventoryGeneral;
use App\Models\Product;
use Exception;

class EntryService
{       
    public function create($products){

        $costs = array_map('floatval', array_column($products, 'cost'));
        $totalExpense = array_sum($costs);

        $entryGeneral = EntryGeneral::create(['quantity_products' => count($products), 'total_expense' => round($totalExpense, 2) ]);
    
        foreach ($products as $product) {
            $productModel = Product::find($product['productID']);

            if (! $productModel) {
                throw new Exception('El producto seleccionado no existe.', 400);
            }

            $quantity = (float) $product['quantity'];

            if ($productModel->sale_type === 'weight') {
                $quantity = round($quantity, 3);

                if ($quantity < 0.001) {
                    throw new Exception('La cantidad del producto debe ser mayor o igual a 0.001 kg.', 400);
                }
            } else {
                if ($quantity < 1 || (float) (int) $quantity !== $quantity) {
                    throw new Exception('La cantidad de este producto debe ser un número entero.', 400);
                }

                $quantity = (int) $quantity;
            }

            $entry = Entry::create([
                'product_id' => $product['productID'],
                'quantity' => $quantity,
                'expired_date' => $product['expiredDate'] ?: null,
                'entry_general_id' => $entryGeneral->id,
                'cost' => round(floatval($product['cost']), 2), 
                'cost_bs' => isset($product['cost_bs']) ? round(floatval($product['cost_bs']), 2) : null,
                'lote_number' => $product['lote_number'],
            ]);
            
            $costPerUnit = round(floatval($entry->cost / $quantity), 6);

            Inventory::create([
                'product_id' => $product['productID'],
                'entry_id' => $entry->id,
                'cost' => $entry->cost,
                'cost_per_unit' => $costPerUnit,
                'profits' => 0,
                'sold' => 0,
                'stock' => $quantity,
                'expired_date' => $product['expiredDate'] ?: null,
                'lote_number' => $entry->lote_number,
                ]);
            
            $inventoryGeneral = InventoryGeneral::where('product_id',$product['productID'])->first();

            if(!isset($inventoryGeneral->id)){

                InventoryGeneral::create([
                    'product_id' => $product['productID'],
                    'stock' => $quantity ,
                    'entries' => $quantity,
                    'outputs' => 0,
                    'expense' => $entry->cost,
                    'profits' => 0,
                ]);        
            }
            else{

                $inventoryGeneral->update([
                    'stock' => $inventoryGeneral->stock + $quantity,
                    'entries' => $inventoryGeneral->entries + $quantity,
                    'expense' => round($inventoryGeneral->expense + $entry->cost, 2),
                ]);
            }

            (new ProductService)->applyMarginPrice($productModel, $costPerUnit);
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
        $productIds = $entries->pluck('product_id')->unique()->toArray();

        foreach($entries as $entryDetail){
            

            $inventoryGeneral = InventoryGeneral::with('product')->where('product_id',$entryDetail->product_id)->first();
            $newStock = $inventoryGeneral->stock - $entryDetail->quantity;
            $newEntries = $inventoryGeneral->entries - $entryDetail->quantity;
            $newExpense = round($inventoryGeneral->expense - $entryDetail->cost, 2); 

            if($newStock < 0)
                throw new Exception("No se puede eliminar esta compra ya que el producto: ". $inventoryGeneral->product->name . ' quedaría en negativo', 500);
                
            $inventoryGeneral->update(['stock' => $newStock, 'entries' => $newEntries, 'expense' => $newExpense]);
        }

        Inventory::whereIn('entry_id',$entryIds)->delete();

        foreach (Product::whereIn('id', $productIds)->get() as $product) {
            (new ProductService)->recalculateMarginPrice($product);
        }
    }

}
