<?php

namespace App\Database\Seeds;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        $users = [
            [
                'id'         => 1,
                'username'   => 'admin_reign',
                'full_name'  => 'Adrien Russel Tan',
                'password'   => password_hash('Admin123!', PASSWORD_DEFAULT),
                'avatar'     => null,
                'created_at' => $now,
            ],
            [
                'id'         => 2,
                'username'   => 'mgr_echo',
                'full_name'  => 'Jericho Macarang',
                'password'   => password_hash('Admin123!', PASSWORD_DEFAULT),
                'avatar'     => null,
                'created_at' => $now,
            ],
            [
                'id'         => 3,
                'username'   => 'cashier_jane',
                'full_name'  => 'Jane Doe',
                'password'   => password_hash('Admin123!', PASSWORD_DEFAULT),
                'avatar'     => null,
                'created_at' => $now,
            ],
        ];

        $this->db->table('users')->insertBatch($users);
        CLI::write('Seeded users with default password: Admin123!');
        CLI::write('Default login: admin_reign / Admin123!');
    }
}
