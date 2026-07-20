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
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = null;

    public function getClientsSoldes()
    {
        $db = \Config\Database::connect();
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
}