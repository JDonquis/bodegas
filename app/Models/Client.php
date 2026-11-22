<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable =  [
        'name',
        'ci',
        'phone_number',
        'address',
        'debt',
        'credit_limit',
    ];

    public function scopeExcludingDefault($query)
    {
        return $query->where(function ($query) {
            $query->where('name', '!=', 'Cliente')
                ->orWhereNotNull('ci');
        });
    }

    public function debts()
    {
        return $this->hasMany(ClientDebt::class);
    }

    public function payments()
    {
        return $this->hasMany(ClientPayment::class);
    }
}
