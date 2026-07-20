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

    public function getClientTransactions(int $clientId, int $limit = null)
    {
        $db = $this->db;
        $sql = "
            SELECT
                tf.*,
                top.code AS type_code,
                top.nom AS type_nom,
                c_exp.telephone AS expediteur_phone,
                c_exp.nom AS expediteur_nom,
                c_dest.telephone AS destinataire_phone,
                c_dest.nom AS destinataire_nom
            FROM v_transactions_frais tf
            JOIN types_operation top ON top.id = tf.type_operation_id
            LEFT JOIN clients c_exp ON c_exp.id = tf.expediteur_id
            LEFT JOIN clients c_dest ON c_dest.id = tf.destinataire_id
            WHERE tf.expediteur_id = ? OR tf.destinataire_id = ?
            ORDER BY tf.date_transaction DESC
        ";
        
        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit;
        }
        
        $query = $db->query($sql, [$clientId, $clientId]);
        return $query->getResult();
    }

    public function createTransaction(int $typeOperationId, ?int $expediteurId, ?int $destinataireId, float $montantBrut)
    {
        $data = [
            'type_operation_id' => $typeOperationId,
            'expediteur_id'     => $expediteurId,
            'destinataire_id'   => $destinataireId,
            'montant_brut'      => $montantBrut,
            'date_transaction'  => date('Y-m-d H:i:s')
        ];
        return $this->insert($data, true);
    }
}