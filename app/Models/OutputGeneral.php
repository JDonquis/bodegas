<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutputGeneral extends Model
{
    use HasFactory;

    protected $fillable = [
        'quantity_products',
        'destiny',

    ];

    public function outputs(){
        return $this->hasMany(Output::class,'output_general_id','id');
    }
}
