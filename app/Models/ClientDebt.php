<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientDebt extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'client_id',
        'output_general_id',
        'amount',
        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function outputGeneral()
    {
        return $this->belongsTo(OutputGeneral::class);
    }

    public function payments()
    {
        return $this->hasMany(ClientPayment::class);
    }
}
