<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryGeneral;
use App\Models\Product;
use App\Services\BCVService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'all');

        $query = InventoryGeneral::with('product')->whereHas('product');

        $this->applyFilter($query, $filter);

        $inventories = $query->paginate(10)->withQueryString();

        $validInventory = InventoryGeneral::whereHas('product');

        $totalExpense = (clone $validInventory)->sum('expense');
        $totalProfits = (clone $validInventory)->sum('profits');
        $totalStock = (clone $validInventory)->sum('stock');
        $totalSold = (clone $validInventory)->sum('sold');

        $profitMargin = $totalSold > 0 ? round(($totalProfits / $totalSold) * 100, 1) : 0;

        $bcvService = new BCVService;
        $usdRate = $bcvService->getUSDValue() ?? 0;
        $totalExpenseBs = $totalExpense * $usdRate;

        $totalCount = (clone $validInventory)->count();
        $lowStockCount = (clone $validInventory)->where('stock', '>', 0)
            ->whereHas('product', function ($product) {
                $product->whereColumn('inventory_generals.stock', '<=', 'products.low_stock_threshold');
            })
            ->count();
        $criticalCount = (clone $validInventory)->where('stock', '<=', 0)->count();

        return view('home.inventory')->with(compact(
            'inventories',
            'filter',
            'totalExpense',
            'totalProfits',
            'totalStock',
            'profitMargin',
            'usdRate',
            'totalExpenseBs',
            'totalCount',
            'lowStockCount',
            'criticalCount'
        ));
    }

    public function show(InventoryGeneral $inventory)
    {
        $details = Inventory::with('product')
            ->where('product_id', $inventory->product_id)
            ->whereHas('product')
            ->where('stock', '>', 0)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json(['details' => $details]);
    }

    public function search($search)
    {
        $filter = request()->input('filter', 'all');

        $products = Product::select('id')->where(function ($query) use ($search) {
            $query->whereRaw('LOWER(name) LIKE ?', [strtolower('%' . $search . '%')])
                ->orWhere('barcode', 'LIKE', '%' . $search . '%');
        })->get()->pluck('id')->toArray();

        $query = InventoryGeneral::with('product')->whereIn('product_id', $products);

        $this->applyFilter($query, $filter);

        $inventories = $query->get();

        return response()->json(['inventories' => $inventories]);
    }

    public function searchLots($search)
    {
        $inventories = Inventory::with('product')
            ->where('stock', '>', 0)
            ->whereHas('product', function ($query) use ($search) {
                $query->whereRaw('LOWER(name) LIKE ?', [strtolower('%' . $search . '%')])
                    ->orWhere('barcode', 'LIKE', '%' . $search . '%');
            })
            ->orderBy('expired_date', 'asc')
            ->get();

        return response()->json(['inventories' => $inventories]);
    }

    private function applyFilter($query, $filter)
    {
        if ($filter === 'low') {
            $query->where('stock', '>', 0)
                ->whereHas('product', function ($product) {
                    $product->whereColumn('inventory_generals.stock', '<=', 'products.low_stock_threshold');
                });
        } elseif ($filter === 'critical') {
            $query->where('stock', '<=', 0);
        } elseif ($filter === 'rotation') {
            $query->orderByDesc('outputs');
        }

        return $query;
    }
}
