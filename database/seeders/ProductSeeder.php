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
            [
                'name' => 'Paracetamol Tabletas 500mg',
            ],
            [
                'name' => 'Ibuprofeno Tabletas 400mg',
            ],
            [
                'name' => 'Aspirina Tabletas 100mg',
            ],
            [
                'name' => 'Amoxicilina Cápsulas 500mg',
            ],
            [
                'name' => 'Ciprofloxacino Tabletas 500mg',
            ],
            [
                'name' => 'Azitromicina Tabletas 500mg',
            ],
            [
                'name' => 'Metformina Tabletas 850mg',
            ],
            [
                'name' => 'Lisinopril Tabletas 10mg',
            ],
            [
                'name' => 'Amlodipino Tabletas 5mg',
            ],
            [
                'name' => 'Simvastatina Tabletas 20mg',
            ],
            [
                'name' => 'Atorvastatina Tabletas 10mg',
            ],
            [
                'name' => 'Omeprazol Cápsulas 20mg',
            ],
            [
                'name' => 'Esomeprazol Cápsulas 20mg',
            ],
            [
                'name' => 'Ranitidina Tabletas 150mg',
            ],
            [
                'name' => 'Levotiroxina Tabletas 50mcg',
            ],
            [
                'name' => 'Albuterol Inhalador 90mcg/pulso',
            ],
            [
                'name' => 'Sertralina Tabletas 50mg',
            ],
            [
                'name' => 'Fluoxetina Tabletas 20mg',
            ],
            [
                'name' => 'Citalopram Tabletas 20mg',
            ],
            [
                'name' => 'Loratadina Tabletas 10mg',
            ],
            [
                'name' => 'Cetirizina Tabletas 10mg',
            ],
            [
                'name' => 'Diphenhidramina Tabletas 25mg',
            ],
            [
                'name' => 'Furosemida Tabletas 40mg',
            ],
            [
                'name' => 'Metoprolol Tabletas 50mg',
            ],
            [
                'name' => 'Propranolol Tabletas 40mg',
            ],
            [
                'name' => 'Warfarina Tabletas 5mg',
            ],
            [
                'name' => 'Clopidogrel Tabletas 75mg',
            ],
            [
                'name' => 'Duloxetina Cápsulas 30mg',
            ],
            [
                'name' => 'Gabapentina Cápsulas 300mg',
            ],
            [
                'name' => 'Tamsulosina Cápsulas 0.4mg',
            ],
            [
                'name' => 'Prednisona Tabletas 5mg',
            ],
            [
                'name' => 'Acetaminofen Tabletas 500mg',
            ],
        ]);
    }
}
