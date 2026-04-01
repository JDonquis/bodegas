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
        return view('home.products')->with(compact('products'));
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

            $this->productService->store($request);

            DB::commit();
            return redirect()->route('products')->with(['success' => 'Producto creado exitosamente']);
        } catch (Exception $e) {

            DB::rollback();
            Log::info('Error creando producto: ' . $e->getMessage());
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
