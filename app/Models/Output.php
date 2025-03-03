<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Output extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'output_general_id',
        'inventory_id',
        'quantity',
        'expired_date',
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function outputGeneral(){
        return $this->belongsTo(OutputGeneral::class);
    }

    public function inventory(){
        return $this->belongsTo(Inventory::class);
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('F j, Y');
    }
}
