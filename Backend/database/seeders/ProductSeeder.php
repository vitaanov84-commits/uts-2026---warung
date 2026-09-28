<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Nasi Goreng Spesial',
                'price' => 15000,
                'stock' => 10,
            ],
            [
                'name' => 'Es Teh Manis',
                'price' => 5000,
                'stock' => 25,
            ],
            [
                'name' => 'Mie Ayam Bakso',
                'price' => 12000,
                'stock' => 15,
            ],
            [
                'name' => 'Ayam Geprek',
                'price' => 18000,
                'stock' => 12,
            ],
            [
                'name' => 'Kentang Goreng',
                'price' => 8000,
                'stock' => 20,
            ],
            [
                'name' => 'Bakso Kuah',
                'price' => 16000,
                'stock' => 18,
            ],
            [
                'name' => 'Soto Ayam',
                'price' => 14000,
                'stock' => 14,
            ],
            [
                'name' => 'Sate Ayam',
                'price' => 17000,
                'stock' => 16,
            ],
            [
                'name' => 'Gado-Gado',
                'price' => 13000,
                'stock' => 12,
            ],
            [
                'name' => 'Roti Bakar Cokelat',
                'price' => 10000,
                'stock' => 15,
            ],
            [
                'name' => 'Pisang Goreng',
                'price' => 9000,
                'stock' => 20,
            ],
            [
                'name' => 'Es Jeruk',
                'price' => 6000,
                'stock' => 24,
            ],
            [
                'name' => 'Kopi Susu',
                'price' => 10000,
                'stock' => 18,
            ],
            [
                'name' => 'Nasi Uduk',
                'price' => 13000,
                'stock' => 11,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['name' => $product['name']],
                [
                    'price' => $product['price'],
                    'stock' => $product['stock'],
                ]
            );
        }
    }
}