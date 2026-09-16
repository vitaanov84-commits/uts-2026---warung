<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'name' => 'Nasi Goreng Spesial',
            'price' => 15000,
            'stock' => 10,
            'description' => 'Nasi goreng dengan telor dan suwiran ayam'
        ]);

        Product::create([
            'name' => 'Es Teh Manis',
            'price' => 5000,
            'stock' => 25,
            'description' => 'Teh manis dingin menyegarkan'
        ]);

        Product::create([
            'name' => 'Mie Ayam Bakso',
            'price' => 12000,
            'stock' => 15,
            'description' => 'Mie ayam pangsit dengan tambahan bakso sapi'
        ]);
    }
}