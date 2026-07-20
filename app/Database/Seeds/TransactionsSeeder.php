<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TransactionsSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // Dépôts
        $depotId = $db->table('types_operation')->where('code', 'DEPOT')->get()->getRow()->id;
        $retraitId = $db->table('types_operation')->where('code', 'RETRAIT')->get()->getRow()->id;
        $transfertId = $db->table('types_operation')->where('code', 'TRANSFERT')->get()->getRow()->id;

        $clients = $db->table('clients')->get()->getResult();

        $transactions = [
            // Dépôts
            [
                'type_operation_id' => $depotId,
                'expediteur_id' => null,
                'destinataire_id' => $clients[0]->id,
                'montant_brut' => 5000,
                'date_transaction' => '2026-07-10 10:00:00'
            ],
            [
                'type_operation_id' => $depotId,
                'expediteur_id' => null,
                'destinataire_id' => $clients[1]->id,
                'montant_brut' => 12000,
                'date_transaction' => '2026-07-11 14:30:00'
            ],
            [
                'type_operation_id' => $depotId,
                'expediteur_id' => null,
                'destinataire_id' => $clients[2]->id,
                'montant_brut' => 250000,
                'date_transaction' => '2026-07-15 09:15:00'
            ],
            [
                'type_operation_id' => $depotId,
                'expediteur_id' => null,
                'destinataire_id' => $clients[3]->id,
                'montant_brut' => 3000,
                'date_transaction' => '2026-07-16 11:00:00'
            ],
            // Retraits
            [
                'type_operation_id' => $retraitId,
                'expediteur_id' => $clients[0]->id,
                'destinataire_id' => null,
                'montant_brut' => 2000,
                'date_transaction' => '2026-07-12 16:45:00'
            ],
            [
                'type_operation_id' => $retraitId,
                'expediteur_id' => $clients[1]->id,
                'destinataire_id' => null,
                'montant_brut' => 75000,
                'date_transaction' => '2026-07-18 11:20:00'
            ],
            [
                'type_operation_id' => $retraitId,
                'expediteur_id' => $clients[2]->id,
                'destinataire_id' => null,
                'montant_brut' => 100000,
                'date_transaction' => '2026-07-19 14:00:00'
            ],
            [
                'type_operation_id' => $retraitId,
                'expediteur_id' => $clients[3]->id,
                'destinataire_id' => null,
                'montant_brut' => 5000,
                'date_transaction' => '2026-07-20 09:30:00'
            ],
            // Transferts
            [
                'type_operation_id' => $transfertId,
                'expediteur_id' => $clients[0]->id,
                'destinataire_id' => $clients[1]->id,
                'montant_brut' => 1500,
                'date_transaction' => '2026-07-13 08:30:00'
            ],
            [
                'type_operation_id' => $transfertId,
                'expediteur_id' => $clients[2]->id,
                'destinataire_id' => $clients[3]->id,
                'montant_brut' => 8000,
                'date_transaction' => '2026-07-14 12:10:00'
            ],
            [
                'type_operation_id' => $transfertId,
                'expediteur_id' => $clients[0]->id,
                'destinataire_id' => $clients[3]->id,
                'montant_brut' => 300000,
                'date_transaction' => '2026-07-19 17:00:00'
            ],
            [
                'type_operation_id' => $transfertId,
                'expediteur_id' => $clients[1]->id,
                'destinataire_id' => $clients[2]->id,
                'montant_brut' => 2500,
                'date_transaction' => '2026-07-21 10:00:00'
            ],
        ];

        $this->db->table('transactions')->insertBatch($transactions);
    }
}