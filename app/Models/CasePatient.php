<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CasePatient extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'user_id',
        'output_general_id',
        'observation',
    ];

    public function patient(){
        return $this->belongsTo(Patient::class);
    }

    public function pathologies(){
        return $this->belongsToMany(Pathology::class)->using(CasePatientPathology::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function outputGeneral(){
        return $this->belongsTo(OutputGeneral::class);
    }
}
