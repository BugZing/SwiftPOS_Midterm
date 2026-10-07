<?php

namespace App\Database\Seeds;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Seeder;

class ResetUserPasswordsSeeder extends Seeder
{
    public function run(): void
    {
        $users = $this->db->table('users')->select(['id', 'username'])->get()->getResultArray();

        if ($users === []) {
            CLI::error('No users found in database to reset.');
            return;
        }

        foreach ($users as $user) {
            $defaultPass = 'Admin123!';
            $this->db->table('users')
                ->where('id', $user['id'])
                ->update(['password' => password_hash($defaultPass, PASSWORD_DEFAULT)]);
            CLI::write("Reset password for {$user['username']} to: {$defaultPass}");
        }

        CLI::write('All user passwords successfully reset to: Admin123!');
    }
}
