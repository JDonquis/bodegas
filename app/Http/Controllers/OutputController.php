<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Output;
use App\Models\OutputGeneral;
use App\Services\BCVService;
use App\Services\OutputService;
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
        $search = $request->input('search');

        $query = OutputGeneral::with(['client', 'outputs.product']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('outputs.product', function ($q3) use ($search) {
                        $q3->where('name', 'like', "%{$search}%")
                            ->orWhere('barcode', 'like', "%{$search}%");
                    });
            });
        }

        $outputs = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('home.outputs')->with(compact('outputs', 'search'));
    }

    /**
     * Search outputs via AJAX.
     */
    public function search(Request $request)
    {
        $search = $request->input('q');

        $query = OutputGeneral::with(['client', 'outputs.product']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('client_name', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('outputs.product', function ($q3) use ($search) {
                        $q3->where('name', 'like', "%{$search}%")
                            ->orWhere('barcode', 'like', "%{$search}%");
                    });
            });
        }

        $outputs = $query->orderBy('created_at', 'desc')->limit(50)->get();

        return response()->json(['outputs' => $outputs]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::get();
        $bcvService = new BCVService;
        $usdRate = $bcvService->getUSDValue();

        return view('home.outputs.create')->with(compact('clients', 'usdRate'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $json = null)
    {
        DB::beginTransaction();

        try {
            $products = $request->input('products');
            $client_id = $request->input('client_id');
            $client_name = $request->input('client_name');
            $totalSold = $request->input('total_sold');

            $outputService = new OutputService;
            $newOutputGeneral = $outputService->create($products, $client_id, $totalSold, $client_name);

            DB::commit();

            if ($json == null) {
                return redirect('home/salidas')->with(['success' => 'Salida creada exitosamente']);
            } else {
                return response()->json(['success' => 'Salida creada exitosamente', 'outputGeneralID' => $newOutputGeneral->id]);
            }

        } catch (\Exception $error) {
            DB::rollBack();
            Log::error('ERROR AL CREAR SALIDA: '.$error->getMessage());

            return back()->withErrors(['error' => $error->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(OutputGeneral $output)
    {
        $outputs = Output::with('product', 'inventory')->where('output_general_id', $output->id)->get();

        return response()->json(['outputs' => $outputs]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OutputGeneral $output)
    {
        $outputs = Output::with('product', 'inventory')->where('output_general_id', $output->id)->get();
        $clients = Client::get();
        $bcvService = new BCVService;
        $usdRate = $bcvService->getUSDValue();

        return view('home.outputs.edit')->with([
            'outputs' => $outputs,
            'outputGeneral' => $output,
            'clients' => $clients,
            'usdRate' => $usdRate,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OutputGeneral $output)
    {
        DB::beginTransaction();

        try {
            $outputService = new OutputService;
            $outputService->delete($output);

            $products = $request->input('products');
            $client_id = $request->input('client_id');
            $client_name = $request->input('client_name');
            $totalSold = $request->input('total_sold');

            $outputService->create($products, $client_id, $totalSold, $client_name);

            DB::commit();

            return redirect()->route('outputs')->with(['success' => 'Salida actualizada exitosamente']);

        } catch (\Exception $error) {
            DB::rollBack();
            Log::error('ERROR AL ACTUALIZAR SALIDA: '.$error->getMessage());

            return back()->withErrors(['error' => $error->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OutputGeneral $output)
    {
        DB::beginTransaction();

        try {
            $outputService = new OutputService;
            $outputService->delete($output);

            DB::commit();

            return redirect()->route('outputs')->with(['success' => 'Salida eliminada exitosamente']);

        } catch (\Exception $error) {
            DB::rollBack();
            Log::error('ERROR AL ELIMINAR SALIDA: '.$error->getMessage());

            return back()->withErrors(['error' => $error->getMessage()]);
        }
    }
}
