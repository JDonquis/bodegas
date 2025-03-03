<?php

namespace Database\Seeders;

use App\Models\Pathology;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PathologySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pathologies')->insert([
            [
                'name' => 'Gripe',
            ],
            [
                'name' => 'Faringitis',
            ],
            [
                'name' => 'Bronquitis',
            ],
            [
                'name' => 'Neumonía',
            ],
            [
                'name' => 'Otitis Media',
            ],
            [
                'name' => 'Sinusitis',
            ],
            [
                'name' => 'Gastroenteritis',
            ],
            [
                'name' => 'Infección Urinaria',
            ],
            [
                'name' => 'Varicela',
            ],
            [
                'name' => 'Herpes Simple',
            ],
            [
                'name' => 'Mononucleosis',
            ],
            [
                'name' => 'Lumbalgia',
            ],
            [
                'name' => 'Alergias Estacionales',
            ],
            [
                'name' => 'Dermatitis',
            ],
            [
                'name' => 'Acné',
            ],
            [
                'name' => 'Hipertensión',
            ],
            [
                'name' => 'Diabetes Tipo 1',
            ],
            [
                'name' => 'Diabetes Tipo 2',
            ],
            [
                'name' => 'Hipotermia',
            ],
            [
                'name' => 'Intoxicación Alimentaria',
            ],
            [
                'name' => 'Cálculos Renales',
            ],
        ]);
    }
}
