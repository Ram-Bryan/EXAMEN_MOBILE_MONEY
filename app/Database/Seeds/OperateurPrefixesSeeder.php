<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class OperateurPrefixesSeeder extends Seeder
{
    public function run()
    {
        // Check if operators already exist (migration V2 creates them)
        $existing = $this->db->table('operateur_prefixes')->countAll();
        if ($existing > 0) {
            return;
        }

        // V1 operators (4 operators) + V2 operator (Vodacom)
        $data = [
            ['prefixe' => '033', 'nom' => 'Mobile Money (Notre Opérateur)', 'est_notre_operateur' => 1],
            ['prefixe' => '034', 'nom' => 'Airtel', 'est_notre_operateur' => 0],
            ['prefixe' => '037', 'nom' => 'Telma', 'est_notre_operateur' => 0],
            ['prefixe' => '038', 'nom' => 'Bip', 'est_notre_operateur' => 0],
            ['prefixe' => '031', 'nom' => 'Vodacom', 'est_notre_operateur' => 0],
        ];

        $this->db->table('operateur_prefixes')->insertBatch($data);
    }
}