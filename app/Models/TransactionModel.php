<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table            = 'transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['type_operation_id', 'expediteur_id', 'destinataire_id', 'montant_brut', 'date_transaction'];
    protected $useTimestamps    = true;
    protected $createdField     = 'date_transaction';
    protected $updatedField     = null;

    public function getGainsParType()
    {
        $db = \Config\Database::connect();
        $sql = "SELECT t.nom AS type_operation, SUM(COALESCE(vf.frais_applique, 0)) AS total_gains
                FROM v_transactions_frais vf
                JOIN types_operation t ON vf.type_operation_id = t.id
                GROUP BY vf.type_operation_id, t.nom";
        return $db->query($sql)->getResultObject();
    }
}