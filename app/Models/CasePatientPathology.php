<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CasePatientPathology extends Pivot
{

    protected $fillable = [
        'case_patient_id',
        'pathology_id',

    ];

   

    
}
