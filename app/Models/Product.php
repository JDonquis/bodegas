<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, BelongsToTenant;

    public const DEFAULT_LOW_STOCK_THRESHOLD = 10;

    protected $fillable = [
        'name',
        'barcode',
        'sell_price',
        'sell_price_bs',
        'sale_type',
        'pricing_mode',
        'profit_margin',
        'price_per_kg',
        'low_stock_threshold',
    ];

    protected $casts = [
        'sale_type' => 'string',
        'pricing_mode' => 'string',
        'profit_margin' => 'float',
        'low_stock_threshold' => 'integer',
    ];

    public function isMarginPricing()
    {
        return $this->pricing_mode === 'margin';
    }

    public function getUnitLabelAttribute()
    {
        return $this->sale_type === 'weight' ? 'g' : 'uds';
    }

    public function inventoryGeneral()
    {
        return $this->hasOne(InventoryGeneral::class);
    }

    public function getEffectivePriceAttribute()
    {
        if ($this->sale_type === 'weight' && $this->price_per_kg) {
            return $this->price_per_kg / 1000;
        }

        return $this->sell_price;
    }

    public function getDisplayPriceAttribute()
    {
        if ($this->sale_type === 'weight' && $this->price_per_kg) {
            return $this->price_per_kg;
        }

        return $this->sell_price;
    }

    public function entries()
    {
        return $this->hasMany(Entry::class);
    }

    public function outputs()
    {
        return $this->hasMany(Output::class);
    }
}
