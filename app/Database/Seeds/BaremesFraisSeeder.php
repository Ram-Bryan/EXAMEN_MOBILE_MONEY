<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BaremesFraisSeeder extends Seeder
{
    public function run()
    {
        // Barèmes
        $baremes = [];
        for ($type = 1; $type <= 3; $type++) {
            for ($op = 1; $op <= 4; $op++) {
                $baremes[] = [
                    'id' => (($type - 1) * 4) + $op,
                    'type_operation_id' => $type,
                    'operateur_id' => $op,
                ];
            }
        }
        $this->db->table('baremes_frais')->insertBatch($baremes);


        # Ces commandes fonctionnent si tu as installé CodeIgniter 4 Shield


        // Historique des frais - DÉPÔT
        $this->db->table('baremes_frais_historique')->insertBatch([
            ['bareme_id' => 1, 'montant_min' => 0, 'montant_max' => 100000, 'frais_fixe' => 100],
            ['bareme_id' => 1, 'montant_min' => 100001, 'montant_max' => null, 'frais_fixe' => 500],
            ['bareme_id' => 2, 'montant_min' => 0, 'montant_max' => 100000, 'frais_fixe' => 200],
            ['bareme_id' => 2, 'montant_min' => 100001, 'montant_max' => null, 'frais_fixe' => 1000],
            ['bareme_id' => 3, 'montant_min' => 0, 'montant_max' => 100000, 'frais_fixe' => 150],
            ['bareme_id' => 3, 'montant_min' => 100001, 'montant_max' => null, 'frais_fixe' => 750],
            ['bareme_id' => 4, 'montant_min' => 0, 'montant_max' => null, 'frais_fixe' => 100],
        ]);

        // Historique des frais - RETRAIT
        $this->db->table('baremes_frais_historique')->insertBatch([
            // Opérateur 1 (033)
            ['bareme_id' => 5, 'montant_min' => 0, 'montant_max' => 5000, 'frais_fixe' => 50],
            ['bareme_id' => 5, 'montant_min' => 5001, 'montant_max' => 50000, 'frais_fixe' => 500],
            ['bareme_id' => 5, 'montant_min' => 50001, 'montant_max' => 200000, 'frais_fixe' => 1000],
            ['bareme_id' => 5, 'montant_min' => 200001, 'montant_max' => null, 'frais_fixe' => 2000],
            // Opérateur 2 (034)
            ['bareme_id' => 6, 'montant_min' => 0, 'montant_max' => 5000, 'frais_fixe' => 25],
            ['bareme_id' => 6, 'montant_min' => 5001, 'montant_max' => 50000, 'frais_fixe' => 250],
            ['bareme_id' => 6, 'montant_min' => 50001, 'montant_max' => 200000, 'frais_fixe' => 750],
            ['bareme_id' => 6, 'montant_min' => 200001, 'montant_max' => null, 'frais_fixe' => 1500],
            // Opérateur 3 (037)
            ['bareme_id' => 7, 'montant_min' => 0, 'montant_max' => 5000, 'frais_fixe' => 38],
            ['bareme_id' => 7, 'montant_min' => 5001, 'montant_max' => 50000, 'frais_fixe' => 375],
            ['bareme_id' => 7, 'montant_min' => 50001, 'montant_max' => 200000, 'frais_fixe' => 875],
            ['bareme_id' => 7, 'montant_min' => 200001, 'montant_max' => null, 'frais_fixe' => 1750],
            // Opérateur 4 (038)
            ['bareme_id' => 8, 'montant_min' => 0, 'montant_max' => 5000, 'frais_fixe' => 50],
            ['bareme_id' => 8, 'montant_min' => 5001, 'montant_max' => 50000, 'frais_fixe' => 200],
            ['bareme_id' => 8, 'montant_min' => 50001, 'montant_max' => 200000, 'frais_fixe' => 500],
            ['bareme_id' => 8, 'montant_min' => 200001, 'montant_max' => null, 'frais_fixe' => 1000],
        ]);

        // Historique des frais - TRANSFERT
        $this->db->table('baremes_frais_historique')->insertBatch([
            // Opérateur 1 (033)
            ['bareme_id' => 9, 'montant_min' => 0, 'montant_max' => 5000, 'frais_fixe' => 25],
            ['bareme_id' => 9, 'montant_min' => 5001, 'montant_max' => 50000, 'frais_fixe' => 250],
            ['bareme_id' => 9, 'montant_min' => 50001, 'montant_max' => 200000, 'frais_fixe' => 750],
            ['bareme_id' => 9, 'montant_min' => 200001, 'montant_max' => null, 'frais_fixe' => 1500],
            // Opérateur 2 (034)
            ['bareme_id' => 10, 'montant_min' => 0, 'montant_max' => 5000, 'frais_fixe' => 13],
            ['bareme_id' => 10, 'montant_min' => 5001, 'montant_max' => 50000, 'frais_fixe' => 125],
            ['bareme_id' => 10, 'montant_min' => 50001, 'montant_max' => 200000, 'frais_fixe' => 500],
            ['bareme_id' => 10, 'montant_min' => 200001, 'montant_max' => null, 'frais_fixe' => 1000],
            // Opérateur 3 (037)
            ['bareme_id' => 11, 'montant_min' => 0, 'montant_max' => 5000, 'frais_fixe' => 25],
            ['bareme_id' => 11, 'montant_min' => 5001, 'montant_max' => 50000, 'frais_fixe' => 250],
            ['bareme_id' => 11, 'montant_min' => 50001, 'montant_max' => 200000, 'frais_fixe' => 1000],
            ['bareme_id' => 11, 'montant_min' => 200001, 'montant_max' => null, 'frais_fixe' => 2000],
            // Opérateur 4 (038)
            ['bareme_id' => 12, 'montant_min' => 0, 'montant_max' => 5000, 'frais_fixe' => 15],
            ['bareme_id' => 12, 'montant_min' => 5001, 'montant_max' => 50000, 'frais_fixe' => 150],
            ['bareme_id' => 12, 'montant_min' => 50001, 'montant_max' => 200000, 'frais_fixe' => 600],
            ['bareme_id' => 12, 'montant_min' => 200001, 'montant_max' => null, 'frais_fixe' => 1200],
        ]);

        // Modifications d'historique (pour tester les versions)
        $this->db->table('baremes_frais_historique')->insertBatch([
            // RETRAIT opérateur 033 à partir du 20/07
            ['bareme_id' => 5, 'montant_min' => 0, 'montant_max' => 5000, 'frais_fixe' => 75, 'date_modif' => '2026-07-20 00:00:00'],
            ['bareme_id' => 5, 'montant_min' => 5001, 'montant_max' => 50000, 'frais_fixe' => 750, 'date_modif' => '2026-07-20 00:00:00'],
            ['bareme_id' => 5, 'montant_min' => 50001, 'montant_max' => 200000, 'frais_fixe' => 1500, 'date_modif' => '2026-07-20 00:00:00'],
            ['bareme_id' => 5, 'montant_min' => 200001, 'montant_max' => null, 'frais_fixe' => 3000, 'date_modif' => '2026-07-20 00:00:00'],
            // TRANSFERT opérateur 034 à partir du 21/07
            ['bareme_id' => 10, 'montant_min' => 0, 'montant_max' => 5000, 'frais_fixe' => 20, 'date_modif' => '2026-07-21 00:00:00'],
            ['bareme_id' => 10, 'montant_min' => 5001, 'montant_max' => 50000, 'frais_fixe' => 200, 'date_modif' => '2026-07-21 00:00:00'],
            ['bareme_id' => 10, 'montant_min' => 50001, 'montant_max' => 200000, 'frais_fixe' => 800, 'date_modif' => '2026-07-21 00:00:00'],
            ['bareme_id' => 10, 'montant_min' => 200001, 'montant_max' => null, 'frais_fixe' => 1600, 'date_modif' => '2026-07-21 00:00:00'],
        ]);
    }
}