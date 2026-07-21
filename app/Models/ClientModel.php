<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table            = 'clients';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['nom', 'telephone', 'code', 'operateur_id'];
    protected $useTimestamps    = false;
    protected $createdField     = 'created_at';
    protected $updatedField     = '';

    public function getClientsSoldes()
    {
        $db = $this->db;
        $sql = "SELECT
                    c.id AS client_id,
                    c.telephone,
                    c.nom,
                    o.prefixe AS operateur,
                    COALESCE(SUM(
                        CASE
                            WHEN tf.destinataire_id = c.id THEN tf.montant_brut
                            WHEN tf.expediteur_id  = c.id THEN -(tf.montant_brut + COALESCE(tf.frais_applique, 0))
                        END
                    ), 0) AS solde
                FROM clients c
                JOIN operateur_prefixes o ON c.operateur_id = o.id
                LEFT JOIN v_transactions_frais tf
                       ON (tf.expediteur_id = c.id OR tf.destinataire_id = c.id)
                GROUP BY c.id, c.telephone, c.nom, o.prefixe
                ORDER BY c.nom ASC";
        return $db->query($sql)->getResultObject();
    }
    public function getByTelephone(string $telephone)
    {
        return $this->where('telephone', $telephone)->first();
    }

    public function verifyClient(string $telephone, string $code)
    {
        return $this->where('telephone', $telephone)
                     ->where('code', $code)
                     ->first();
    }

    public function getBalance(int $clientId): float
    {
        $db = $this->db;
        $sql = "
            SELECT
                COALESCE(SUM(
                    CASE
                        WHEN tf.destinataire_id = ? THEN tf.montant_brut
                        WHEN tf.expediteur_id  = ? THEN -(tf.montant_brut + COALESCE(tf.frais_applique, 0))
                    END
                ), 0) AS solde
            FROM clients c
            LEFT JOIN v_transactions_frais tf
                   ON (tf.expediteur_id = c.id OR tf.destinataire_id = c.id)
            WHERE c.id = ?
            GROUP BY c.id
        ";
        
        $query = $db->query($sql, [$clientId, $clientId, $clientId]);
        $row = $query->getRow();
        return $row ? (float)$row->solde : 0.0;
    }

    public function isNotreOperateur(int $clientId): bool
    {
        $db = $this->db;
        $row = $db->query(
            "SELECT o.est_notre_operateur
             FROM clients c
             JOIN operateur_prefixes o ON o.id = c.operateur_id
             WHERE c.id = ?",
            [$clientId]
        )->getRow();
        return $row && (int)$row->est_notre_operateur === 1;
    }

    public function createClient(string $telephone, int $operateurId, string $code = null): int
    {
        $nom = 'Client ' . $telephone;

        if ($code === null) {
            do {
                $code = sprintf('%06d', rand(0, 999999));
                $existing = $this->where('code', $code)->first();
            } while ($existing !== null);
        } else {
            $code = trim($code);
        }

        $data = [
            'nom'          => $nom,
            'telephone'    => $telephone,
            'code'         => $code,
            'operateur_id' => $operateurId
        ];

        return $this->insert($data, true);
    }

    
}