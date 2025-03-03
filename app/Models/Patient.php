<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'last_name',
        'ci',
        'type_patient_id',
        'age',
        'sex',
        'career_id',

    ];

    public function typePatient(){
        return $this->belongsTo(TypePatient::class);
    }

    public function career(){
        return $this->belongsTo(Career::class);
    }
}
