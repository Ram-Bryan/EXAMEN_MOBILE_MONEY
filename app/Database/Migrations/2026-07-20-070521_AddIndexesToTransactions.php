<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndexesToTransactions extends Migration
{
    public function up()
    {
        $this->db->query('CREATE INDEX idx_transactions_expediteur ON transactions(expediteur_id)');
        $this->db->query('CREATE INDEX idx_transactions_destinataire ON transactions(destinataire_id)');
        $this->db->query('CREATE INDEX idx_transactions_created ON transactions(date_transaction)');
    }

    public function down()
    {
        $this->db->query('DROP INDEX IF EXISTS idx_transactions_expediteur');
        $this->db->query('DROP INDEX IF EXISTS idx_transactions_destinataire');
        $this->db->query('DROP INDEX IF EXISTS idx_transactions_created');
    }
}