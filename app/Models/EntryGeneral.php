<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntryGeneral extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'quantity_products',
        'total_expense'
    ];


    public function entries(){
        return $this->hasMany(Entry::class,'entry_general_id','id');
    }
}
