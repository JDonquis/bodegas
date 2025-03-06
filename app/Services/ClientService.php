<?php  

namespace App\Services;

use App\Models\Client;
use Exception;

class ClientService
{
    public function get(){
        $products = Client::query()
        ->when(request()->input('search'), function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', [strtolower('%'.$search.'%')])
                  ->orWhereRaw('LOWER(phone_number) LIKE ?', [strtolower('%'.$search.'%')])
                  ->orWhereRaw('LOWER(ci) LIKE ?', [strtolower('%'.$search.'%')]);
            });
        })
        ->orderBy('id','desc')
        ->paginate(15);

        return $products;   
    }

    
    public function store($data){

        $client = Client::create([
        'name' => ucwords(strtolower($data->clientName)) , 
        'ci' => $data->clientCI ?? null ,
        'phone_number' => $data->clientPhoneNumber ?? null , 
        'address' => $data->clientAddress ?? null , 
    ]);

        return $client;

    }

    public function update($data, $client){

        $client->update([
            'name' => ucwords(strtolower($data->clientName)) , 
            'ci' => $data->clientCI ?? null ,
            'phone_number' => $data->clientPhoneNumber ?? null , 
            'address' => $data->clientAddress ?? null , 
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
