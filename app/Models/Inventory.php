<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        
        'product_id',
        'expired_date',
        'stock',
        'entry_id',
        'condition_id',
        
    
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function entry(){
        return $this->belongsTo(Entry::class);
    }
}
