<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use App\Models\EntryGeneral;
use App\Services\BCVService;
use App\Services\EntryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EntryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = EntryGeneral::with(['entries.product']);

        if ($search) {
            $query->whereHas('entries.product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $entries = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        $todayEntries = EntryGeneral::whereDate('created_at', now()->toDateString())->count();
        $monthInvestment = EntryGeneral::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total_expense');

        $bcvService = new BCVService;
        $usdRate = $bcvService->getUSDValue() ?? 0;
        $monthInvestmentBs = round($monthInvestment * $usdRate, 2);

        return view('home.entries')->with(compact(
            'entries',
            'search',
            'todayEntries',
            'monthInvestment',
            'monthInvestmentBs',
            'usdRate'
        ));
    }

    /**
     * Search entries via AJAX.
     */
    public function search(Request $request)
    {
        $search = $request->input('q');

        $query = EntryGeneral::with(['entries.product']);

        if ($search) {
            $query->whereHas('entries.product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $entries = $query->orderBy('created_at', 'desc')->limit(50)->get();

        return response()->json(['entries' => $entries]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $bcvService = new BCVService;
        $usdRate = $bcvService->getUSDValue();

        $preselectedProduct = null;

        if ($request->filled('product')) {
            $product = \App\Models\Product::find($request->input('product'));

            if ($product) {
                $preselectedProduct = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'barcode' => $product->barcode,
                    'sale_type' => $product->sale_type,
                ];
            }
        }

        $oldProducts = [];
        if (old('products')) {
            foreach (old('products') as $oldProduct) {
                $product = \App\Models\Product::find($oldProduct['productID']);
                if ($product) {
                    $oldProducts[] = [
                        'id' => $product->id,
                        'name' => $product->name,
                        'barcode' => $product->barcode,
                        'sale_type' => $product->sale_type,
                        'quantity' => $oldProduct['quantity'],
                        'cost' => $oldProduct['cost'],
                        'cost_bs' => $oldProduct['cost_bs'] ?? 0,
                        'lote_number' => $oldProduct['lote_number'],
                        'date' => $oldProduct['expiredDate'] ?? '',
                    ];
                }
            }
        }

        return view('home.entries.create')->with(compact('usdRate', 'oldProducts', 'preselectedProduct'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            Log::info($request->input('products'));

            $products = $request->input('products');
            $entryService = new EntryService;
            $entryService->create($products);

            DB::commit();

            return redirect()->route('entries')->with(['success' => 'Compra creada exitosamente']);
        } catch (\Exception $error) {

            DB::rollBack();

            Log::info('ERROR AL CREAR ENTRADA');
            Log::error($error->getMessage());

            return back()->withErrors(['error' => $error->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(EntryGeneral $entry)
    {
        $entries = Entry::with('product')->where('entry_general_id', $entry->id)->get();

        return response()->json(['entries' => $entries]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EntryGeneral $entry)
    {
        $entries = Entry::with('product')->where('entry_general_id', $entry->id)->get();

        $bcvService = new BCVService;
        $usdRate = $bcvService->getUSDValue();

        $entryGeneral = $entry;

        return view('home.entries.edit')->with(compact('entries', 'entryGeneral', 'usdRate'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EntryGeneral $entry)
    {
        DB::beginTransaction();

        try {

            $entryService = new EntryService;
            $entryService->delete($entry);

            $products = $request->input('products');
            $entryService->create($products);

            DB::commit();

            return redirect()->route('entries')->with(['success' => 'Compra actualizada exitosamente']);
        } catch (\Exception $error) {

            DB::rollBack();

            Log::info('ERROR AL ACTUALIZAR ENTRADA');
            Log::error($error->getMessage());

            return back()->withErrors(['error' => $error->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EntryGeneral $entry)
    {
        DB::beginTransaction();

        try {

            $entryService = new EntryService;
            $entryService->delete($entry);

            DB::commit();

            return redirect()->route('entries')->with(['success' => 'Compra eliminada exitosamente']);
        } catch (\Exception $error) {

            DB::rollBack();

            Log::info('ERROR AL ELIMINAR ENTRADA');
            Log::error($error->getMessage().'-- Linea: '.$error->getLine().' -- Archivo:'.$error->getFile());

            return back()->withErrors(['error' => $error->getMessage()]);
        }
    }
}
