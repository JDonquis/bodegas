<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private $productService;

    public function __construct(){
        $this->productService = new ProductService;
    }

    public function index(){
        
        $products = $this->productService->get();
        return view('home.products')->with(compact('products'));
    }

    public function search($search){

        $products = Product::whereRaw('LOWER(name) LIKE ?', [strtolower('%'.$search.'%')])
        ->orderBy('name','asc')
        ->get();
        
        return response()->json(['products' => $products]);
    }

    public function store(ProductRequest $request){

        $product = Product::create(['name' => ucwords($request->productName)]);

        return response()->json(['message' => 'OK', 'product' => $product, 'success' => 'Producto creado exitosamente']);
    
    }
}
