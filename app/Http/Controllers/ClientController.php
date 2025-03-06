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

    public function index(){
        $clients = $this->clientService->get();
        return view('home.clients')->with(compact('clients'));
    }

    public function create(){
        return view('home.clients.create');
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
