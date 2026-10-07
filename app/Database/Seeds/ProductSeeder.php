<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        $products = [
            [
                'id'             => 1,
                'name'           => 'Espresso Roast',
                'price'          => 120.00,
                'stock_quantity' => 50,
                'image'          => null,
                'created_at'     => $now,
            ],
            [
                'id'             => 2,
                'name'           => 'Caffe Latte',
                'price'          => 150.00,
                'stock_quantity' => 40,
                'image'          => null,
                'created_at'     => $now,
            ],
            [
                'id'             => 3,
                'name'           => 'Cappuccino Classic',
                'price'          => 155.00,
                'stock_quantity' => 35,
                'image'          => null,
                'created_at'     => $now,
            ],
            [
                'id'             => 4,
                'name'           => 'Caramel Macchiato',
                'price'          => 175.00,
                'stock_quantity' => 25,
                'image'          => null,
                'created_at'     => $now,
            ],
            [
                'id'             => 5,
                'name'           => 'Iced Mocha Frappe',
                'price'          => 185.00,
                'stock_quantity' => 20,
                'image'          => null,
                'created_at'     => $now,
            ],
            [
                'id'             => 6,
                'name'           => 'Butter Croissant',
                'price'          => 95.00,
                'stock_quantity' => 15,
                'image'          => null,
                'created_at'     => $now,
            ],
            [
                'id'             => 7,
                'name'           => 'Blueberry Muffin',
                'price'          => 85.00,
                'stock_quantity' => 4,
                'image'          => null,
                'created_at'     => $now,
            ],
            [
                'id'             => 8,
                'name'           => 'Matcha Green Tea Latte',
                'price'          => 165.00,
                'stock_quantity' => 0,
                'image'          => null,
                'created_at'     => $now,
            ],
        ];

        $this->db->table('products')->insertBatch($products);
    }
}
