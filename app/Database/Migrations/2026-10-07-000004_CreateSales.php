<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSales extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'product_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'customer_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'sold_by' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'quantity' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'total_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('product_id', 'products', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('sold_by', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('sales');
    }

    public function down(): void
    {
        $this->forge->dropTable('sales', true);
    }
}
