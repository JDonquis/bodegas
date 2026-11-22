<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\Client;
use App\Services\ClientService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{   
    private ClientService $clientService;

    public function __construct(){
        $this->clientService = new ClientService;
    }

    public function index(Request $request)
    {
        $clients = $this->clientService->get();

        $filter = $request->input('filter', 'all');
        $activeFilter = in_array($filter, ['debt', 'solvent'], true) ? $filter : 'all';

        $totalClients = Client::excludingDefault()->count();
        $totalDebt = Client::excludingDefault()->sum('debt');
        $debtorsCount = Client::excludingDefault()->where('debt', '>', 0)->count();
        $solventCount = $totalClients - $debtorsCount;
        $solventPercentage = $totalClients > 0 ? round(($solventCount / $totalClients) * 100) : 0;
        $newThisMonth = Client::excludingDefault()
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();

        return view('home.clients')->with(compact(
            'clients',
            'activeFilter',
            'totalClients',
            'totalDebt',
            'debtorsCount',
            'solventCount',
            'solventPercentage',
            'newThisMonth'
        ));
    }

    public function create(){
        return view('home.clients.create');
    }

    public function history(Client $client)
    {
        $debts = $client->debts()
            ->with(['outputGeneral', 'payments'])
            ->get()
            ->map(function ($debt) {
                $paid = $debt->payments->sum('amount');

                return [
                    'type' => 'debt',
                    'id' => $debt->id,
                    'amount' => (float) $debt->amount,
                    'paid' => (float) $paid,
                    'remaining' => round((float) $debt->amount - $paid, 2),
                    'description' => $debt->description,
                    'sale_id' => $debt->output_general_id,
                    'date' => $debt->created_at->toISOString(),
                ];
            });

        $payments = $client->payments()
            ->with('debt')
            ->get()
            ->map(function ($payment) {
                return [
                    'type' => 'payment',
                    'id' => $payment->id,
                    'amount' => (float) $payment->amount,
                    'description' => $payment->description,
                    'debt_id' => $payment->client_debt_id,
                    'date' => $payment->created_at->toISOString(),
                ];
            });

        $history = $debts->concat($payments)->sortByDesc('date')->values();

        return response()->json([
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'ci' => $client->ci,
            ],
            'debt' => (float) $client->debt,
            'credit_limit' => (float) ($client->credit_limit ?? 0),
            'history' => $history,
        ]);
    }

    public function store(ClientRequest $request){
    
        DB::beginTransaction();

        try{

            $this->clientService->store($request);

            DB::commit();
            return redirect()->route('clients')->with(['success' => 'Cliente creado exitosamente']);

        }catch(Exception $e){
            
            DB::rollback();
            Log::info('Error creando cliente: ' . $e->getMessage());
            return back()->withErrors(['error' => $e->getMessage()]);

        }
    
    }

    public function edit(Client $client){
        return view('home.clients.edit')->with(compact('client'));
    }

    public function update(ClientRequest $request, Client $client){

        DB::beginTransaction();

        try{

            $this->clientService->update($request, $client);

            DB::commit();
            return redirect()->route('clients')->with(['success' => 'Cliente actualizado exitosamente']);

        }catch(Exception $e){
            
            DB::rollback();
            Log::info('Error actualizando cliente: ' . $e->getMessage());
            return back()->withErrors(['error' => $e->getMessage()]);

        }
    }

    public function destroy(Client $client){
        DB::beginTransaction();

        try{

            $this->clientService->delete($client);

            DB::commit();
            return redirect()->route('clients')->with(['success' => 'Cliente eliminado exitosamente']);

        }catch(Exception $e){
            
            DB::rollback();
            Log::info('Error eliminando cliente: ' . $e->getMessage());
            return back()->withErrors(['error' => $e->getMessage()]);

        }
    }
}
