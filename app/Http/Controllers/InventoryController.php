<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryGeneral;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(){
        
        $inventories = InventoryGeneral::with('product')->paginate(10);
        return view('home.inventory')->with(['inventories' => $inventories]);
    }

    public function show(InventoryGeneral $inventory){
        
        $details = Inventory::with('product')->where('product_id',$inventory->product_id)->where('stock','>',0)->orderBy('id','desc')->get();

        return response()->json(['details' => $details]);
    }

    public function search($search){
        
        $products = Product::select('id')->whereRaw('LOWER(name) LIKE ?', [strtolower('%'.$search.'%')])
        ->get()
        ->pluck('id')
        ->toArray();

        $inventories = InventoryGeneral::with('product')->whereIn('product_id',$products)->get();

        return response()->json(['inventories' => $inventories]);

    }
}
