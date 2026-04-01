<?php  

namespace App\Services;

use App\Models\Product;
use Exception;

class ProductService
{
    public function get(){
        $products = Product::query()
        ->when(request()->input('search'), function ($query, $search) {
            return $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', [strtolower('%'.$search.'%')])
                  ->orWhere('barcode', 'LIKE', '%'.$search.'%');
            });
        })
        ->orderBy('id','desc')
        ->paginate(15);

        return $products;   
    }
    
    public function store($data){

        $product = Product::create([
        'name' => ucwords(strtolower($data->productName)) , 
        'barcode' => $data->barcode,
        'sell_price' => round(floatval($data->sellPrice), 2),
        'sell_price_bs' => $data->sellPriceBs ? round(floatval($data->sellPriceBs), 2) : null
        ]);

        return $product;

    }

    public function update($data, $product){

        $product->update([
        'name' => ucwords(strtolower($data->productName)) , 
        'barcode' => $data->barcode,
        'sell_price' => round(floatval($data->sellPrice), 2),
        'sell_price_bs' => $data->sellPriceBs ? round(floatval($data->sellPriceBs), 2) : null
        ]);

        return $product;

    }

    public function delete($product){
        
        if($product->entries()->exists()){
            throw new Exception('No se puede eliminar el producto porque tiene entradas registradas.', 400);
        }

        if($product->outputs()->exists()){
            throw new Exception('No se puede eliminar el producto porque tiene salidas registradas.', 400);
        }

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
