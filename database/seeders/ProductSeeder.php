<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Services\BCVService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener tasa para el seeder
        $bcvService = new BCVService();
        $rate = $bcvService->getUSDValue() ?: 36.50; // Fallback por si falla el scraping

        $products = [
            ['name' => 'Harina PAN Maíz Blanco 1kg', 'barcode' => '7591001000111', 'sell_price' => 1.20],
            ['name' => 'Harina PAN Maíz Amarillo 1kg', 'barcode' => '7591001000222', 'sell_price' => 1.25],
            ['name' => 'Mantequilla Mavesa 500g', 'barcode' => '7591001000333', 'sell_price' => 2.50],
            ['name' => 'Mayonesa Mavesa 445g', 'barcode' => '7591001000444', 'sell_price' => 3.80],
            ['name' => 'Arroz Primor Clásico 1kg', 'barcode' => '7591001000555', 'sell_price' => 1.10],
            ['name' => 'Pasta Primor Codo Mediano 1kg', 'barcode' => '7591001000666', 'sell_price' => 1.40],
            ['name' => 'Aceite Vegetal Vatel 1L', 'barcode' => '7591001000777', 'sell_price' => 2.90],
            ['name' => 'Atún Margarita en Aceite 140g', 'barcode' => '7591001000888', 'sell_price' => 1.85],
            ['name' => 'Sardinas La Gaviota 170g', 'barcode' => '7591001000999', 'sell_price' => 0.85],
            ['name' => 'Café Fama de América 250g', 'barcode' => '7591001001010', 'sell_price' => 2.20],
            ['name' => 'Leche La Campesina 400g', 'barcode' => '7591001001111', 'sell_price' => 4.50],
            ['name' => 'Refresco Polar Black 1.5L', 'barcode' => '7591001001212', 'sell_price' => 1.50],
            ['name' => 'Malta Polar 355ml', 'barcode' => '7591001001313', 'sell_price' => 0.90],
            ['name' => 'Cerveza Polar Light (Tercio)', 'barcode' => '7591001001414', 'sell_price' => 1.10],
            ['name' => 'Jabón Las Llaves (Panela)', 'barcode' => '7591001001515', 'sell_price' => 1.30],
            ['name' => 'Detergente Las Llaves 1kg', 'barcode' => '7591001001616', 'sell_price' => 3.20],
            ['name' => 'Papel Higiénico Scott 4 rollos', 'barcode' => '7591001001717', 'sell_price' => 2.10],
            ['name' => 'Crema Dental Colgate 100ml', 'barcode' => '7591001001818', 'sell_price' => 1.95],
            ['name' => 'Shampoo Every Night 400ml', 'barcode' => '7591001001919', 'sell_price' => 3.50],
            ['name' => 'Adobo La Comadre 200g', 'barcode' => '7591001002020', 'sell_price' => 1.15],
        ];

        foreach ($products as $p) {
            Product::create([
                'name' => $p['name'],
                'barcode' => $p['barcode'],
                'sell_price' => $p['sell_price'],
                'sell_price_bs' => round($p['sell_price'] * $rate, 2),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
