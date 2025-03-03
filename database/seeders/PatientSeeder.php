<?php

namespace Database\Seeders;

use App\Models\Patient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Patient::create(['name' => 'Carlos', 'last_name' => 'Rodriguez', 'ci' => '1234567', 'type_patient_id' => 1, 'age' => 21, 'sex' => 'Masculino', 'career_id' => 2]);
    }
}
