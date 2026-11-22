<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutputGeneral extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [

        'client_id',
        'client_name',
        'quantity_products',
        'total_sold',
        'total_profit',
        'payment_method',

    ];

    public function outputs(){
        return $this->hasMany(Output::class,'output_general_id','id');
    }

    public function client(){
        return $this->belongsTo(Client::class);
    }

}
