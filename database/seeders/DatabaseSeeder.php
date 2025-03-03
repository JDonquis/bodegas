<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\TypePatient;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
           
            TypeUserSeeder::class,
            TypePatientSeeder::class,
            CareerSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            ProductSeeder::class,
            ConditionSeeder::class,
            PathologySeeder::class,
            PatientSeeder::class,
        ]);

    }
}
