<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BaremesFraisSeeder extends Seeder
{
    public function run()
    {
        // Barèmes - 5 opérateurs × 3 types = 15 baremes
        $baremes = [];
        $id = 1;
        for ($type = 1; $type <= 3; $type++) {
            for ($op = 1; $op <= 5; $op++) {
                $baremes[] = [
                    'id' => $id++,
                    'type_operation_id' => $type,
                    'operateur_id' => $op,
                ];
            }
        }
        $this->db->table('baremes_frais')->insertBatch($baremes);


        # Ces commandes fonctionnent si tu as installé CodeIgniter 4 Shield


        // Historique des frais - DÉPÔT
        $this->db->table('baremes_frais_historique')->insertBatch([
            ['bareme_id' => 1, 'montant_min' => 0, 'montant_max' => 100000, 'frais_fixe' => 100, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 1, 'montant_min' => 100001, 'montant_max' => null, 'frais_fixe' => 500, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 2, 'montant_min' => 0, 'montant_max' => 100000, 'frais_fixe' => 200, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 2, 'montant_min' => 100001, 'montant_max' => null, 'frais_fixe' => 1000, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 3, 'montant_min' => 0, 'montant_max' => 100000, 'frais_fixe' => 150, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 3, 'montant_min' => 100001, 'montant_max' => null, 'frais_fixe' => 750, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 4, 'montant_min' => 0, 'montant_max' => null, 'frais_fixe' => 100, 'date_modif' => '2026-01-01 00:00:00'],
            // Opérateur 5 (031 - Vodacom) - copie opérateur 1
            ['bareme_id' => 5, 'montant_min' => 0, 'montant_max' => 100000, 'frais_fixe' => 100, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 5, 'montant_min' => 100001, 'montant_max' => null, 'frais_fixe' => 500, 'date_modif' => '2026-01-01 00:00:00'],
        ]);

        // Historique des frais - RETRAIT
        $this->db->table('baremes_frais_historique')->insertBatch([
            // Opérateur 1 (033)
            ['bareme_id' => 6, 'montant_min' => 0, 'montant_max' => 5000, 'frais_fixe' => 50, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 6, 'montant_min' => 5001, 'montant_max' => 50000, 'frais_fixe' => 500, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 6, 'montant_min' => 50001, 'montant_max' => 200000, 'frais_fixe' => 1000, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 6, 'montant_min' => 200001, 'montant_max' => null, 'frais_fixe' => 2000, 'date_modif' => '2026-01-01 00:00:00'],
            // Opérateur 2 (034)
            ['bareme_id' => 7, 'montant_min' => 0, 'montant_max' => 5000, 'frais_fixe' => 25, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 7, 'montant_min' => 5001, 'montant_max' => 50000, 'frais_fixe' => 250, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 7, 'montant_min' => 50001, 'montant_max' => 200000, 'frais_fixe' => 750, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 7, 'montant_min' => 200001, 'montant_max' => null, 'frais_fixe' => 1500, 'date_modif' => '2026-01-01 00:00:00'],
            // Opérateur 3 (037)
            ['bareme_id' => 8, 'montant_min' => 0, 'montant_max' => 5000, 'frais_fixe' => 38, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 8, 'montant_min' => 5001, 'montant_max' => 50000, 'frais_fixe' => 375, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 8, 'montant_min' => 50001, 'montant_max' => 200000, 'frais_fixe' => 875, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 8, 'montant_min' => 200001, 'montant_max' => null, 'frais_fixe' => 1750, 'date_modif' => '2026-01-01 00:00:00'],
            // Opérateur 4 (038)
            ['bareme_id' => 9, 'montant_min' => 0, 'montant_max' => 5000, 'frais_fixe' => 50, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 9, 'montant_min' => 5001, 'montant_max' => 50000, 'frais_fixe' => 200, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 9, 'montant_min' => 50001, 'montant_max' => 200000, 'frais_fixe' => 500, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 9, 'montant_min' => 200001, 'montant_max' => null, 'frais_fixe' => 1000, 'date_modif' => '2026-01-01 00:00:00'],
            // Opérateur 5 (031 - Vodacom) - copie opérateur 1
            ['bareme_id' => 10, 'montant_min' => 0, 'montant_max' => 5000, 'frais_fixe' => 50, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 10, 'montant_min' => 5001, 'montant_max' => 50000, 'frais_fixe' => 500, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 10, 'montant_min' => 50001, 'montant_max' => 200000, 'frais_fixe' => 1000, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 10, 'montant_min' => 200001, 'montant_max' => null, 'frais_fixe' => 2000, 'date_modif' => '2026-01-01 00:00:00'],
        ]);

        // Historique des frais - TRANSFERT
        $this->db->table('baremes_frais_historique')->insertBatch([
            // Opérateur 1 (033)
            ['bareme_id' => 11, 'montant_min' => 0, 'montant_max' => 5000, 'frais_fixe' => 25, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 11, 'montant_min' => 5001, 'montant_max' => 50000, 'frais_fixe' => 250, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 11, 'montant_min' => 50001, 'montant_max' => 200000, 'frais_fixe' => 750, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 11, 'montant_min' => 200001, 'montant_max' => null, 'frais_fixe' => 1500, 'date_modif' => '2026-01-01 00:00:00'],
            // Opérateur 2 (034)
            ['bareme_id' => 12, 'montant_min' => 0, 'montant_max' => 5000, 'frais_fixe' => 13, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 12, 'montant_min' => 5001, 'montant_max' => 50000, 'frais_fixe' => 125, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 12, 'montant_min' => 50001, 'montant_max' => 200000, 'frais_fixe' => 500, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 12, 'montant_min' => 200001, 'montant_max' => null, 'frais_fixe' => 1000, 'date_modif' => '2026-01-01 00:00:00'],
            // Opérateur 3 (037)
            ['bareme_id' => 13, 'montant_min' => 0, 'montant_max' => 5000, 'frais_fixe' => 25, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 13, 'montant_min' => 5001, 'montant_max' => 50000, 'frais_fixe' => 250, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 13, 'montant_min' => 50001, 'montant_max' => 200000, 'frais_fixe' => 1000, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 13, 'montant_min' => 200001, 'montant_max' => null, 'frais_fixe' => 2000, 'date_modif' => '2026-01-01 00:00:00'],
            // Opérateur 4 (038)
            ['bareme_id' => 14, 'montant_min' => 0, 'montant_max' => 5000, 'frais_fixe' => 15, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 14, 'montant_min' => 5001, 'montant_max' => 50000, 'frais_fixe' => 150, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 14, 'montant_min' => 50001, 'montant_max' => 200000, 'frais_fixe' => 600, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 14, 'montant_min' => 200001, 'montant_max' => null, 'frais_fixe' => 1200, 'date_modif' => '2026-01-01 00:00:00'],
            // Opérateur 5 (031 - Vodacom) - copie opérateur 1
            ['bareme_id' => 15, 'montant_min' => 0, 'montant_max' => 5000, 'frais_fixe' => 25, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 15, 'montant_min' => 5001, 'montant_max' => 50000, 'frais_fixe' => 250, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 15, 'montant_min' => 50001, 'montant_max' => 200000, 'frais_fixe' => 750, 'date_modif' => '2026-01-01 00:00:00'],
            ['bareme_id' => 15, 'montant_min' => 200001, 'montant_max' => null, 'frais_fixe' => 1500, 'date_modif' => '2026-01-01 00:00:00'],
        ]);

        // Modifications d'historique (pour tester les versions)
        $this->db->table('baremes_frais_historique')->insertBatch([
            // RETRAIT opérateur 033 (op1 = bareme_id 6) à partir du 20/07
            ['bareme_id' => 6, 'montant_min' => 0, 'montant_max' => 5000, 'frais_fixe' => 75, 'date_modif' => '2026-07-20 00:00:00'],
            ['bareme_id' => 6, 'montant_min' => 5001, 'montant_max' => 50000, 'frais_fixe' => 750, 'date_modif' => '2026-07-20 00:00:00'],
            ['bareme_id' => 6, 'montant_min' => 50001, 'montant_max' => 200000, 'frais_fixe' => 1500, 'date_modif' => '2026-07-20 00:00:00'],
            ['bareme_id' => 6, 'montant_min' => 200001, 'montant_max' => null, 'frais_fixe' => 3000, 'date_modif' => '2026-07-20 00:00:00'],
            // TRANSFERT opérateur 034 (op2 = bareme_id 12) à partir du 21/07
            ['bareme_id' => 12, 'montant_min' => 0, 'montant_max' => 5000, 'frais_fixe' => 20, 'date_modif' => '2026-07-21 00:00:00'],
            ['bareme_id' => 12, 'montant_min' => 5001, 'montant_max' => 50000, 'frais_fixe' => 200, 'date_modif' => '2026-07-21 00:00:00'],
            ['bareme_id' => 12, 'montant_min' => 50001, 'montant_max' => 200000, 'frais_fixe' => 800, 'date_modif' => '2026-07-21 00:00:00'],
            ['bareme_id' => 12, 'montant_min' => 200001, 'montant_max' => null, 'frais_fixe' => 1600, 'date_modif' => '2026-07-21 00:00:00'],
        ]);
    }
}