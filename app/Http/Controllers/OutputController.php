<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Output;
use App\Models\OutputGeneral;
use App\Services\OutputService;
use App\Services\PDFService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OutputController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $outputs = OutputGeneral::with('client')->orderBy('created_at','desc')->paginate(10);
        return view('home.outputs')->with(compact('outputs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {   
        $clients = Client::get();
        return view('home.outputs.create')->with(compact('clients'));
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request,$json = null)
    {
        DB::beginTransaction();
        
        try{

            $products = $request->input('products');
            $client = $request->input('client_id');
            $totalSold = $request->input('total_sold');


            $outputService = new OutputService();
            $newOutputGeneral = $outputService->create($products, $client, $totalSold);
            
            DB::commit();


            if($json == null)
                return redirect('home/salidas')->with(['success' => 'Salida creada exitosamente']);
            else
                return response()->json(['success' => 'Salida creada exitosamente', 'outputGeneralID' => $newOutputGeneral->id]);

        }catch(\Exception $error){
        
            DB::rollBack();

            Log::info('ERROR AL CREAR SALIDA');
            Log::error($error->getMessage());
            
            return back()->withErrors(['error' => $error->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(OutputGeneral $output)
    {
        $outputs = Output::with('product','inventory')->where('output_general_id', $output->id)->get();

        return response()->json(['outputs' => $outputs]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OutputGeneral $output)
    {
        $outputs = Output::with('product','inventory')->where('output_general_id',$output->id)->get();
        $clients = Client::get();
        return view('home.outputs.edit')->with(['outputs' => $outputs, 'outputGeneral' => $output, 'clients' => $clients]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OutputGeneral $output)
    {
        DB::beginTransaction();
        
        try{
            
            $outputService = new OutputService();
            $outputService->delete($output);

            $products = $request->input('products');
            $client = $request->input('client_id');
            $totalSold = $request->input('total_sold');

            $outputService->create($products, $client, $totalSold);


            DB::commit();
            return redirect()->route('outputs')->with(['success' => 'Salida actualizada exitosamente']);

        }catch(\Exception $error){
        
            DB::rollBack();

            Log::info('ERROR AL ACTUALIZAR SALIDA');
            Log::error($error->getMessage());
            
            return back()->withErrors(['error' => $error->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OutputGeneral $output)
    {
        DB::beginTransaction();
        
        try{
            
            $outputService = new OutputService();
            $outputService->delete($output);

            DB::commit();
            return redirect()->route('outputs')->with(['success' => 'Salida eliminada exitosamente']);

        }catch(\Exception $error){
        
            DB::rollBack();

            Log::info('ERROR AL ELIMINAR SALIDA');
            Log::error($error->getMessage() . '-- Linea: ' . $error->getLine() . ' -- Archivo:' . $error->getFile());
            
            return back()->withErrors(['error' => $error->getMessage()]);
        }
    }
}
