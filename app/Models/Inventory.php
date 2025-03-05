<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        
        'product_id',
        'entry_id',
        'cost',
        'cost_per_unit',
        'profits',
        'sold',
        'stock',
        'expired_date',
        'lote_number',

    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function entry(){
        return $this->belongsTo(Entry::class);
    }
}
