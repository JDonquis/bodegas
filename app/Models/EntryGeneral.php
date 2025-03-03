<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntryGeneral extends Model
{
    use HasFactory;

    protected $fillable = [
        'quantity_products'
    ];


    public function entries(){
        return $this->hasMany(Entry::class,'entry_general_id','id');
    }
}
