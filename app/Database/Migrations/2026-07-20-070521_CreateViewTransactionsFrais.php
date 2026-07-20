<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateViewTransactionsFrais extends Migration
{
    public function up()
    {
        $this->db->query("
            CREATE VIEW v_transactions_frais AS
            SELECT
                tc.transaction_id,
                tc.type_operation_id,
                tc.expediteur_id,
                tc.destinataire_id,
                tc.montant_brut,
                tc.date_transaction,
                tc.operateur_id,
                (
                    SELECT h.frais_fixe
                    FROM baremes_frais b
                    JOIN baremes_frais_historique h ON h.bareme_id = b.id
                    WHERE b.type_operation_id = tc.type_operation_id
                      AND b.operateur_id      = tc.operateur_id
                      AND tc.montant_brut >= h.montant_min
                      AND (h.montant_max IS NULL OR tc.montant_brut <= h.montant_max)
                      AND h.date_modif = (
                          SELECT MAX(h2.date_modif)
                          FROM baremes_frais_historique h2
                          WHERE h2.bareme_id = h.bareme_id
                            AND h2.date_modif <= tc.date_transaction
                      )
                    LIMIT 1
                ) AS frais_applique
            FROM v_transactions_operateur tc
        ");
    }

    public function down()
    {
        $this->db->query('DROP VIEW IF EXISTS v_transactions_frais');
    }
}