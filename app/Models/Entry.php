<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'product_id',
        'entry_general_id',
        'quantity',
        'cost',
        'cost_bs',
        'expired_date',
        'lote_number',

    ];

    public $casts = [
        'expired_date' => 'date',
        'quantity' => 'float',
        'cost' => 'float',
        'cost_bs' => 'float',
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function entryGeneral(){
        return $this->belongsTo(EntryGeneral::class);
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('F j, Y');
    }


}
