<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Inventory;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'sku' => 'P001',
                'name' => 'Notebook Dell Inspiron',
                'description' => 'Notebook Dell i5 com 8GB RAM',
                'cost_price' => 2500.00,
                'sale_price' => 3200.00,
            ],
            [
                'sku' => 'P002',
                'name' => 'Mouse Gamer Logitech',
                'description' => 'Mouse gamer com 6 botões programáveis',
                'cost_price' => 80.00,
                'sale_price' => 150.00,
            ],
            [
                'sku' => 'P003',
                'name' => 'Monitor LG 24"',
                'description' => 'Monitor Full HD IPS',
                'cost_price' => 700.00,
                'sale_price' => 950.00,
            ],
            [
                'sku' => 'P004',
                'name' => 'Teclado USB',
                'description' => 'Teclado PTBR USB',
                'cost_price' => 300.00,
                'sale_price' => 600.00,
            ],
            [
                'sku' => 'P005',
                'name' => 'Fone de ouvido"',
                'description' => 'Fone headset sem fio',
                'cost_price' => 200.00,
                'sale_price' => 300.00,
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::create($productData);

            Inventory::create([
                'product_id' => $product->id,
                'quantity' => rand(5, 20),
                'last_updated' => now(),
            ]);
        }
    }
}

