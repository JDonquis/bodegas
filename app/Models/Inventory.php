<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory, BelongsToTenant;

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

    protected $casts = [
        'cost' => 'float',
        'cost_per_unit' => 'float',
        'profits' => 'float',
        'sold' => 'float',
        'stock' => 'float',
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function entry(){
        return $this->belongsTo(Entry::class);
    }
}
