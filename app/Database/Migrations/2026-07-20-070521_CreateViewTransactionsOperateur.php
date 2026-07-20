<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateViewTransactionsOperateur extends Migration
{
    public function up()
    {
        $this->db->query("
            CREATE VIEW v_transactions_operateur AS
            SELECT
                tr.id AS transaction_id,
                tr.type_operation_id,
                tr.expediteur_id,
                tr.destinataire_id,
                tr.montant_brut,
                tr.date_transaction,
                COALESCE(ce.operateur_id, cd.operateur_id) AS operateur_id
            FROM transactions tr
            LEFT JOIN clients ce ON ce.id = tr.expediteur_id
            LEFT JOIN clients cd ON cd.id = tr.destinataire_id
        ");
    }

    public function down()
    {
        $this->db->query('DROP VIEW IF EXISTS v_transactions_operateur');
    }
}