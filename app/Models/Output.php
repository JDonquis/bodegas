<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Output extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'product_id',
        'output_general_id',
        'inventory_id',
        'quantity',
        'is_credit',
        'expired_date',
        'profit',
    ];

    protected $casts = [
        'is_credit' => 'boolean',
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

    public function getUnitPriceAttribute()
    {
        $product = $this->product;

        if (! $product) {
            return 0;
        }

        return $product->sale_type === 'weight' && $product->price_per_kg
            ? (float) $product->price_per_kg / 1000
            : (float) $product->sell_price;
    }

    public function getSubtotalAttribute()
    {
        return round($this->unit_price * (float) $this->quantity, 2);
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('F j, Y');
    }
}
