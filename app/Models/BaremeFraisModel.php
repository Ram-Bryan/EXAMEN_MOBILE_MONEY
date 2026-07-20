<?php

namespace App\Models;

use CodeIgniter\Model;

class BaremeFraisModel extends Model
{
    protected $table            = 'baremes_frais';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['type_operation_id', 'operateur_id'];

    public function getCurrentBaremes()
    {
        $db = \Config\Database::connect();
        $sql = "SELECT b.id AS bareme_id, b.type_operation_id, b.operateur_id, 
                       o.prefixe, t.nom AS type_nom,
                       h.montant_min, h.montant_max, h.frais_fixe, h.date_modif
                FROM baremes_frais b
                JOIN operateur_prefixes o ON b.operateur_id = o.id
                JOIN types_operation t ON b.type_operation_id = t.id
                JOIN baremes_frais_historique h ON h.bareme_id = b.id
                WHERE h.date_modif = (
                    SELECT MAX(h2.date_modif) 
                    FROM baremes_frais_historique h2 
                    WHERE h2.bareme_id = h.bareme_id
                )
                ORDER BY t.nom, o.prefixe, h.montant_min";
        return $db->query($sql)->getResultObject();
    }

    public function addTranche($type_operation_id, $operateur_id, $montant_min, $montant_max, $frais_fixe)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $bareme = $this->where(['type_operation_id' => $type_operation_id, 'operateur_id' => $operateur_id])->first();
        if (!$bareme) {
            $this->insert([
                'type_operation_id' => $type_operation_id,
                'operateur_id' => $operateur_id
            ]);
            $baremeId = $this->getInsertID();
        } else {
            $baremeId = $bareme->id;
        }

        $historiqueModel = new \App\Models\BaremeFraisHistoriqueModel();
        $historiqueModel->insert([
            'bareme_id' => $baremeId,
            'montant_min' => $montant_min,
            'montant_max' => $montant_max === '' ? null : $montant_max,
            'frais_fixe' => $frais_fixe
        ]);

        $db->transComplete();

        return $db->transStatus();
    }
}