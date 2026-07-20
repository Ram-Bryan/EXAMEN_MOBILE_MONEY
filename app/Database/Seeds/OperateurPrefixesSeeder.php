<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class OperateurPrefixesSeeder extends Seeder
{
    public function run()
    {
        // V1 operators (4 operators) + V2 operator (Vodacom)
        $data = [
            ['id' => 1, 'prefixe' => '033', 'nom' => 'Mobile Money (Notre Opérateur)', 'est_notre_operateur' => 1],
            ['id' => 2, 'prefixe' => '034', 'nom' => 'Airtel', 'est_notre_operateur' => 0],
            ['id' => 3, 'prefixe' => '037', 'nom' => 'Telma', 'est_notre_operateur' => 0],
            ['id' => 4, 'prefixe' => '038', 'nom' => 'Bip', 'est_notre_operateur' => 0],
            ['id' => 5, 'prefixe' => '031', 'nom' => 'Vodacom', 'est_notre_operateur' => 0],
        ];

        $this->db->table('operateur_prefixes')->insertBatch($data);
    }
}