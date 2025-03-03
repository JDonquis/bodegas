<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use App\Models\EntryGeneral;
use App\Models\Inventory;
use App\Models\InventoryGeneral;
use App\Services\EntryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EntryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        $entries = EntryGeneral::orderBy('created_at','desc')->paginate(10);
        return view('home.entries')->with(compact('entries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('home.entries.create');
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {   
        DB::beginTransaction();
        
        try{

            $products = $request->input('products');
            $entryService = new EntryService();
            $entryService->create($products);
            
            DB::commit();

            return redirect('home/entradas')->with(['success' => 'Entrada creada exitosamente']);

        }catch(\Exception $error){
        
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
        $entries = Entry::with('product')->where('entry_general_id',$entry->id)->get();
        
        return view('home.entries.edit')->with(['entries' => $entries, 'entryGeneral' => $entry]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EntryGeneral $entry)
    {
        DB::beginTransaction();
        
        try{
            
            $entryService = new EntryService();
            $entryService->delete($entry);

            $products = $request->input('products');
            $entryService->create($products);

            DB::commit();
            return redirect()->route('entries')->with(['success' => 'Entrada actualizada exitosamente']);

        }catch(\Exception $error){
        
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
        
        try{
            
            $entryService = new EntryService();
            $entryService->delete($entry);

            DB::commit();
            return redirect()->route('entries')->with(['success' => 'Entrada eliminada exitosamente']);

        }catch(\Exception $error){
        
            DB::rollBack();

            Log::info('ERROR AL ELIMINAR ENTRADA');
            Log::error($error->getMessage() . '-- Linea: ' . $error->getLine() . ' -- Archivo:' . $error->getFile());
            
            return back()->withErrors(['error' => $error->getMessage() ]);
        }
    }
}
