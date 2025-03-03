<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pathology extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function casePatient(){
        return $this->belongsToMany(CasePatient::class)->using(CasePatientPathology::class);
    }
}
