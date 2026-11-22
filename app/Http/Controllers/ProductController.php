<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Services\BCVService;
use App\Services\ProductService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    private $productService;

    public function __construct()
    {
        $this->productService = new ProductService;
    }

    public function index()
    {
        $products = $this->productService->get();

        $totalProducts = Product::count();

        $activeProducts = Product::whereHas('inventoryGeneral', function ($query) {
            $query->where('stock', '>', 0);
        })->count();

        $lowStockProducts = Product::where(function ($query) {
            $query->whereDoesntHave('inventoryGeneral')
                ->orWhereHas('inventoryGeneral', function ($inventory) {
                    $inventory->whereColumn('inventory_generals.stock', '<=', 'products.low_stock_threshold');
                });
        })->count();

        $unitProducts = Product::where('sale_type', 'unit')->count();
        $weightProducts = Product::where('sale_type', 'weight')->count();

        return view('home.products')->with(compact(
            'products',
            'totalProducts',
            'activeProducts',
            'lowStockProducts',
            'unitProducts',
            'weightProducts'
        ));
    }

    public function export()
    {
        $products = Product::with('inventoryGeneral')->orderBy('name')->get();

        return response()->streamDownload(function () use ($products) {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'Código de barras',
                'Producto',
                'Tipo de venta',
                'Precio USD',
                'Precio Bs',
                'Stock',
                'Unidad',
            ]);

            foreach ($products as $product) {
                fputcsv($handle, [
                    $product->barcode,
                    $product->name,
                    $product->sale_type === 'weight' ? 'Peso' : 'Unidad',
                    number_format($product->display_price, 2, '.', ''),
                    $product->sell_price_bs !== null ? number_format($product->sell_price_bs, 2, '.', '') : '',
                    $product->inventoryGeneral->stock ?? 0,
                    $product->unit_label,
                ]);
            }

            fclose($handle);
        }, 'productos-' . now()->format('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function create()
    {
        $bcvService = new BCVService();
        $usdRate = $bcvService->getUSDValue();

        return view('home.products.create')->with(compact('usdRate'));
    }

    public function store(ProductRequest $request)
    {

        DB::beginTransaction();

        try {

            $product = $this->productService->store($request);

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['product' => $product], 201);
            }

            return redirect()->route('products')->with(['success' => 'Producto creado exitosamente']);
        } catch (Exception $e) {

            DB::rollback();
            Log::info('Error creando producto: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit(Product $product)
    {
        $bcvService = new BCVService();
        $usdRate = $bcvService->getUSDValue();
        return view('home.products.edit')->with(compact('product', 'usdRate'));
    }

    public function update(ProductRequest $request, Product $product)
    {

        DB::beginTransaction();

        try {

            $this->productService->update($request, $product);

            DB::commit();
            return redirect()->route('products')->with(['success' => 'Producto actualizado exitosamente']);
        } catch (Exception $e) {

            DB::rollback();
            Log::info('Error actualizando producto: ' . $e->getMessage());
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Product $product)
    {
        DB::beginTransaction();

        try {

            $this->productService->delete($product);

            DB::commit();
            return redirect()->route('products')->with(['success' => 'Producto eliminado exitosamente']);
        } catch (Exception $e) {

            DB::rollback();
            Log::info('Error eliminando producto: ' . $e->getMessage());
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function search($search)
    {

        $products = Product::where(function ($query) use ($search) {
            $query->whereRaw('LOWER(name) LIKE ?', [strtolower('%' . $search . '%')])
                ->orWhere('barcode', 'LIKE', '%' . $search . '%');
        })
            ->orderBy('name', 'asc')
            ->get();

        return response()->json(['products' => $products]);
    }
}
