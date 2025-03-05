<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutputGeneral extends Model
{
    use HasFactory;

    protected $fillable = [

        'client_id',
        'quantity_products',
        'total_sold',
        'total_profit',

    ];

    public function outputs(){
        return $this->hasMany(Output::class,'output_general_id','id');
    }

    public function client(){
        return $this->belongsTo(Client::class);
    }

}
