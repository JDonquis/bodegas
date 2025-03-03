<?php

namespace Database\Seeders;

use App\Models\TypePatient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypePatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TypePatient::create(['name' => 'Estudiante']);
        TypePatient::create(['name' => 'Personal']);
    }
}
