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
    protected $useTimestamps    = false;
    protected $createdField     = 'date_transaction';
    protected $updatedField     = '';

    public function getGainsParType()
    {
        $db = \Config\Database::connect();
        $sql = "SELECT t.nom AS type_operation, SUM(COALESCE(vf.frais_applique, 0)) AS total_gains
                FROM v_transactions_frais vf
                JOIN types_operation t ON vf.type_operation_id = t.id
                GROUP BY vf.type_operation_id, t.nom";
        return $db->query($sql)->getResultObject();
    }

    public function getGainsParOperateur(int $operateurId)
    {
        $db = \Config\Database::connect();
        $sql = "
            SELECT
                t.code AS type_operation,
                t.nom,
                COUNT(*) AS nb_operations,
                SUM(tf.frais_applique) AS total_gains
            FROM v_transactions_frais tf
            JOIN types_operation t ON t.id = tf.type_operation_id
            WHERE tf.frais_applique IS NOT NULL
              AND tf.operateur_id = ?
            GROUP BY t.code, t.nom
        ";
        return $db->query($sql, [$operateurId])->getResultObject();
    }

    /**
     * Retourne les gains séparés : notre opérateur vs opérateurs externes
     * Utilise la vue v_situation_gains créée en v2
     */
    public function getGainsSepares()
    {
        $db = \Config\Database::connect();
        $sql = "SELECT * FROM v_situation_gains ORDER BY est_notre_operateur DESC, type_operation";
        return $db->query($sql)->getResultObject();
    }

    /**
     * Retourne les montants totaux à envoyer à chaque opérateur externe
     * Utilise la vue v_montants_a_envoyer créée en v2
     */
    public function getMontantsAEnvoyer()
    {
        $db = \Config\Database::connect();
        $sql = "SELECT * FROM v_montants_a_envoyer ORDER BY montant_total_a_envoyer DESC";
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