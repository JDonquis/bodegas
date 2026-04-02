<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'barcode',
        'sell_price',
        'sell_price_bs',
        'sale_type',
        'price_per_kg',
    ];

    protected $casts = [
        'sale_type' => 'string',
    ];

    public function getUnitLabelAttribute()
    {
        return $this->sale_type === 'weight' ? 'g' : 'uds';
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
