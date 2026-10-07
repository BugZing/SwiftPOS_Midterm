<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SaleSeeder extends Seeder
{
    public function run(): void
    {
        $sales = [
            [
                'id'          => 1,
                'product_id'  => 1,
                'customer_id' => 101,
                'sold_by'     => 1,
                'quantity'    => 2,
                'total_price' => 240.00,
                'created_at'  => '2026-10-01 09:15:00',
            ],
            [
                'id'          => 2,
                'product_id'  => 4,
                'customer_id' => 102,
                'sold_by'     => 1,
                'quantity'    => 1,
                'total_price' => 175.00,
                'created_at'  => '2026-10-02 11:30:00',
            ],
            [
                'id'          => 3,
                'product_id'  => 6,
                'customer_id' => null,
                'sold_by'     => 2,
                'quantity'    => 3,
                'total_price' => 285.00,
                'created_at'  => '2026-10-03 14:20:00',
            ],
            [
                'id'          => 4,
                'product_id'  => 2,
                'customer_id' => 103,
                'sold_by'     => 1,
                'quantity'    => 2,
                'total_price' => 300.00,
                'created_at'  => '2026-10-04 16:45:00',
            ],
            [
                'id'          => 5,
                'product_id'  => 3,
                'customer_id' => null,
                'sold_by'     => 3,
                'quantity'    => 1,
                'total_price' => 155.00,
                'created_at'  => '2026-10-05 10:05:00',
            ],
        ];

        $this->db->table('sales')->insertBatch($sales);
    }
}
