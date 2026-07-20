<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class OperateurPrefixesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['id' => 1, 'prefixe' => '033'],
            ['id' => 2, 'prefixe' => '034'],
            ['id' => 3, 'prefixe' => '037'],
            ['id' => 4, 'prefixe' => '038'],
        ];

        $this->db->table('operateur_prefixes')->insertBatch($data);
    }
}