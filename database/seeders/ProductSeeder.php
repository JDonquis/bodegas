<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')->insert([
            ['name' => 'Arroz', 'sell_price' => 2.50, 'created_at' => now()],
            ['name' => 'Frijoles', 'sell_price' => 1.80, 'created_at' => now()],
            ['name' => 'Lentejas', 'sell_price' => 2.00, 'created_at' => now()],
            ['name' => 'Aceite de Oliva', 'sell_price' => 5.75, 'created_at' => now()],
            ['name' => 'Azúcar', 'sell_price' => 1.20, 'created_at' => now()],
            ['name' => 'Sal', 'sell_price' => 0.99, 'created_at' => now()],
            ['name' => 'Pasta', 'sell_price' => 1.50, 'created_at' => now()],
            ['name' => 'Tomates', 'sell_price' => 3.00, 'created_at' => now()],
            ['name' => 'Cebollas', 'sell_price' => 1.00, 'created_at' => now()],
            ['name' => 'Zanahorias', 'sell_price' => 1.25, 'created_at' => now()],
            ['name' => 'Leche', 'sell_price' => 2.99, 'created_at' => now()],
            ['name' => 'Huevos', 'sell_price' => 3.50, 'created_at' => now()],
            ['name' => 'Pan', 'sell_price' => 2.00, 'created_at' => now()],
            ['name' => 'Mantequilla', 'sell_price' => 4.00, 'created_at' => now()],
            ['name' => 'Manzanas', 'sell_price' => 3.25, 'created_at' => now()],
            ['name' => 'Plátanos', 'sell_price' => 1.15, 'created_at' => now()],
            ['name' => 'Naranjas', 'sell_price' => 2.40, 'created_at' => now()],
            ['name' => 'Pollo (kg)', 'sell_price' => 8.00, 'created_at' => now()],
            ['name' => 'Carne de Res (kg)', 'sell_price' => 10.50, 'created_at' => now()],
            ['name' => 'Pescado (kg)', 'sell_price' => 12.00, 'created_at' => now()],
        ]);
    }
}
