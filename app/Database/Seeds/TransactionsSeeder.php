<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TransactionsSeeder extends Seeder
{
    public function run()
    {
        if ($this->db->table('transactions')->countAll() > 0) {
            return;
        }

        $db = \Config\Database::connect();

        $depotId = $db->table('types_operation')->where('code', 'DEPOT')->get()->getRow()->id;
        $retraitId = $db->table('types_operation')->where('code', 'RETRAIT')->get()->getRow()->id;
        $transfertId = $db->table('types_operation')->where('code', 'TRANSFERT')->get()->getRow()->id;

        $clients = $db->table('clients')->get()->getResult();

        $depotAmount = 50000;

        $transactions = [
            // Dépôts initiaux (même montant pour tous)
            [
                'type_operation_id' => $depotId,
                'expediteur_id' => null,
                'destinataire_id' => $clients[0]->id,
                'montant_brut' => $depotAmount,
                'date_transaction' => '2026-07-10 10:00:00',
                'frais_inclus' => 0
            ],
            [
                'type_operation_id' => $depotId,
                'expediteur_id' => null,
                'destinataire_id' => $clients[1]->id,
                'montant_brut' => $depotAmount,
                'date_transaction' => '2026-07-10 10:00:00',
                'frais_inclus' => 0
            ],
            [
                'type_operation_id' => $depotId,
                'expediteur_id' => null,
                'destinataire_id' => $clients[2]->id,
                'montant_brut' => $depotAmount,
                'date_transaction' => '2026-07-10 10:00:00',
                'frais_inclus' => 0
            ],
            [
                'type_operation_id' => $depotId,
                'expediteur_id' => null,
                'destinataire_id' => $clients[3]->id,
                'montant_brut' => $depotAmount,
                'date_transaction' => '2026-07-10 10:00:00',
                'frais_inclus' => 0
            ],
            // Retraits modérés
            // Jean (033, op1): retrait 5000, frais 50 = -5050, solde = 44950
            [
                'type_operation_id' => $retraitId,
                'expediteur_id' => $clients[0]->id,
                'destinataire_id' => null,
                'montant_brut' => 5000,
                'date_transaction' => '2026-07-12 16:45:00',
                'frais_inclus' => 0
            ],
            // Marie (034, op2): retrait 10000, frais 25 = -10025, solde = 39975
            [
                'type_operation_id' => $retraitId,
                'expediteur_id' => $clients[1]->id,
                'destinataire_id' => null,
                'montant_brut' => 10000,
                'date_transaction' => '2026-07-18 11:20:00',
                'frais_inclus' => 0
            ],
            // Pierre (037, op3): retrait 20000, frais 38 = -20038, solde = 29962
            [
                'type_operation_id' => $retraitId,
                'expediteur_id' => $clients[2]->id,
                'destinataire_id' => null,
                'montant_brut' => 20000,
                'date_transaction' => '2026-07-19 14:00:00',
                'frais_inclus' => 0
            ],
            // Sophie (038, op4): retrait 5000, frais 50 = -5050, solde = 44950
            [
                'type_operation_id' => $retraitId,
                'expediteur_id' => $clients[3]->id,
                'destinataire_id' => null,
                'montant_brut' => 5000,
                'date_transaction' => '2026-07-20 09:30:00',
                'frais_inclus' => 0
            ],
            // Transferts inter-opérateurs (avec commission 1.5%)
            // Jean (033, op1) -> Marie (034, op2): 5000, frais fixe 25 + commission 75 = 100, total -5100, solde Jean = 39850
            [
                'type_operation_id' => $transfertId,
                'expediteur_id' => $clients[0]->id,
                'destinataire_id' => $clients[1]->id,
                'montant_brut' => 5000,
                'date_transaction' => '2026-07-13 08:30:00',
                'frais_inclus' => 0
            ],
            // Pierre (037, op3) -> Sophie (038, op4): 10000, frais fixe 25 + commission 150 = 175, total -10175, solde Pierre = 19787
            [
                'type_operation_id' => $transfertId,
                'expediteur_id' => $clients[2]->id,
                'destinataire_id' => $clients[3]->id,
                'montant_brut' => 10000,
                'date_transaction' => '2026-07-14 12:10:00',
                'frais_inclus' => 0
            ],
            // Marie (034, op2) -> Pierre (037, op3): 3000, frais fixe 125 + commission 45 = 170, total -3170, solde Marie = 39975 + 5000 - 3170 = 41805
            [
                'type_operation_id' => $transfertId,
                'expediteur_id' => $clients[1]->id,
                'destinataire_id' => $clients[2]->id,
                'montant_brut' => 3000,
                'date_transaction' => '2026-07-21 10:00:00',
                'frais_inclus' => 0
            ],
        ];

        $this->db->table('transactions')->insertBatch($transactions);
    }
}