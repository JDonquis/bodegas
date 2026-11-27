<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseItem extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'expense_id',
        'product_id',
        'inventory_id',
        'product_name',
        'sale_type',
        'quantity',
        'cost_per_unit',
        'sell_price_unit',
        'subtotal_cost',
        'subtotal_retail',
    ];

    protected $casts = [
        'quantity' => 'float',
        'cost_per_unit' => 'decimal:4',
        'sell_price_unit' => 'decimal:4',
        'subtotal_cost' => 'decimal:2',
        'subtotal_retail' => 'decimal:2',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function getUnitLabelAttribute()
    {
        return $this->sale_type === 'weight' ? 'kg' : 'uds';
    }
}
