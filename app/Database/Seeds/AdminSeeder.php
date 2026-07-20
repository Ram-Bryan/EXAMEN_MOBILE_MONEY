<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        if ($this->db->table('admin')->countAll() > 0) {
            return;
        }

        $data = [
            'email' => 'admin@gmail.com',
            'password' => '$2y$10$UTMMoAhVoCxpxMtQlLIB9eu9YOdhfch0IYWfk9tkCf9ToIIW8tqjK', // admin123
        ];

        $this->db->table('admin')->insert($data);
    }
}