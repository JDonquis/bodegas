<?php

namespace Database\Seeders;

use App\Models\Condition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Condition::create(['name' => 'Buen estado']);
        Condition::create(['name' => 'Por vencer']);
        Condition::create(['name' => 'Vencido']);

    }
}
