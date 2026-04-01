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
    ];

    public function entries()
    {
        return $this->hasMany(Entry::class);
    }

    public function outputs()
    {
        return $this->hasMany(Output::class);
    }
}
