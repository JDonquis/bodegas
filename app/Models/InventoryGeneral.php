<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryGeneral extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        
        'product_id',
        'stock',
        'entries',
        'outputs',
        'expense',
        'sold',
        'profits',
    ];

    public function product(){
        return $this->belongsTo(Product::class)->orderBy('name','desc');
    }
}
