<?php  

namespace App\Services;

use App\Models\Client;
use Exception;

class ClientService
{
    public function get(){
        $products = Client::query()
        ->excludingDefault()
        ->when(request()->input('search'), function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', [strtolower('%'.$search.'%')])
                  ->orWhereRaw('LOWER(phone_number) LIKE ?', [strtolower('%'.$search.'%')])
                  ->orWhereRaw('LOWER(ci) LIKE ?', [strtolower('%'.$search.'%')]);
            });
        })
        ->when(request()->input('filter') === 'debt', function ($query) {
            return $query->where('debt', '>', 0);
        })
        ->when(request()->input('filter') === 'solvent', function ($query) {
            return $query->where('debt', '<=', 0);
        })
        ->orderBy('id','desc')
        ->paginate(15)
        ->withQueryString();

        return $products;   
    }

    
    public function store($data){

        $client = Client::create([
        'name' => ucwords(strtolower($data->clientName)) , 
        'ci' => $data->clientCI ?? null ,
        'phone_number' => $data->clientPhoneNumber ?? null , 
        'address' => $data->clientAddress ?? null , 
        'debt' => $data->clientDebt ?? 0,
        'credit_limit' => $data->clientCreditLimit ?? 0,
    ]);

        return $client;

    }

    public function update($data, $client){

        $client->update([
            'name' => ucwords(strtolower($data->clientName)) , 
            'ci' => $data->clientCI ?? null ,
            'phone_number' => $data->clientPhoneNumber ?? null , 
            'address' => $data->clientAddress ?? null , 
            'debt' => $data->clientDebt ?? 0,
            'credit_limit' => $data->clientCreditLimit ?? 0,
        ]);

        return $client;

    }

    public function delete($client){
        // Validar relaciones: 
        // if($config->products()->exists()){
        //     throw new Exception('Hay productos que contienen este registro, no puede ser eliminado',400);
        // }

        $client->delete();

        return 0;

    }

}
