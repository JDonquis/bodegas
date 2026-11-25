<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'description',
        'total_cost',
        'total_retail',
    ];

    protected $casts = [
        'total_cost' => 'decimal:2',
        'total_retail' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(ExpenseItem::class);
    }

    public function getTotalLostProfitAttribute()
    {
        return round((float) $this->total_retail - (float) $this->total_cost, 2);
    }
}
