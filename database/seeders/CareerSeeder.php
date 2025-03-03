<?php

namespace Database\Seeders;

use App\Models\Career;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CareerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Career::create(['name' => 'No aplica']);
        Career::create(['name' => 'Ing. de Sistemas']);
        Career::create(['name' => 'Ing. de Telecomunicaciones']);
        Career::create(['name' => 'Turismo']);

    }
}
