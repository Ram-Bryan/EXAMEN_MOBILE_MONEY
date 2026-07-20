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
        
        $results = $db->query($sql, [$clientId, $clientId])->getResult();
        
        // Fallback pour les frais manquants (dus aux dates)
        $baremeModel = new \App\Models\BaremeFraisModel();
        foreach ($results as $tx) {
            if ($tx->frais_applique === null && $tx->type_code !== 'DEPOT') {
                $tx->frais_applique = (float)$baremeModel->getFrais($tx->type_operation_id, $tx->operateur_id, $tx->montant_brut);
            }
        }
        
        return $results;
    }

    public function createTransaction(int $typeOperationId, ?int $expediteurId, ?int $destinataireId, float $montantBrut)
    {
        $data = [
            'type_operation_id' => $typeOperationId,
            'montant_brut'      => $montantBrut,
            'date_transaction'  => date('Y-m-d H:i:s')
        ];
        
        if ($expediteurId !== null) {
            $data['expediteur_id'] = $expediteurId;
        }
        if ($destinataireId !== null) {
            $data['destinataire_id'] = $destinataireId;
        }
        
        return $this->insert($data, true);
    }
}