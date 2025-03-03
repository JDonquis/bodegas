<?php

namespace App\Listeners;

use App\Models\Inventory;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RefreshInventoryGeneral
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $products = $event->products;
        $stocks = [];
        $inventories = Inventory::whereIn('product_id',$products)->get();

        foreach($inventories as $inventory)
        {
            if(array_key_exists($inventory->product_id,$stocks)){
                $stocks[$inventory->product_id]['stock'] += $inventory->stock; 
            }
            else{
                $stocks[$inventory->product_id] = [
                    'stock' => $inventory->stock,
                    'product_id' => $inventory->product_id,
                ]; 
            }
                
        }
    }
}
