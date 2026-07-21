<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EpargneSeeder extends Seeder
{
    public function run()
    {
        if ($this->db->table('epargne')->countAll() > 0) {
            return;
        }

        $data = [
            ['client_id' => 1, 'montant' => 0, 'pourcentage' => 0.5,'date_modif' => '2026-01-01 10:00:00'],
            ['client_id' => 2, 'montant' => 0, 'pourcentage' => 0.2,'date_modif' => '2026-01-01 10:00:00'],
            ['client_id' => 3, 'montant' => 0, 'pourcentage' => 0.3,'date_modif' => '2026-01-01 10:00:00'],
            ['client_id' => 4, 'montant' => 0, 'pourcentage' => 0.5,'date_modif' => '2026-01-01 10:00:00'],
        ];

        $this->db->table('epargne')->insertBatch($data);
    }
}