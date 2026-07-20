<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ClientsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['nom' => 'Jean Rakoto', 'telephone' => '0331234567', 'code' => '1234', 'operateur_id' => 1],
            ['nom' => 'Marie Rabe', 'telephone' => '0349876543', 'code' => '5678', 'operateur_id' => 2],
            ['nom' => 'Pierre Randria', 'telephone' => '0371122334', 'code' => '9012', 'operateur_id' => 3],
            ['nom' => 'Sophie Rasoa', 'telephone' => '0385566778', 'code' => '3456', 'operateur_id' => 4],
        ];

        $this->db->table('clients')->insertBatch($data);
    }
}