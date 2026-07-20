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

    public function getFrais(int $typeOperationId, int $operateurId, float $montantBrut, string $dateTransaction = null)
    {
        if ($dateTransaction === null) {
            $dateTransaction = date('Y-m-d H:i:s');
        }
        
        $db = $this->db;
        $sql = "
            SELECT h.frais_fixe
            FROM baremes_frais b
            JOIN baremes_frais_historique h ON h.bareme_id = b.id
            WHERE b.type_operation_id = ?
              AND b.operateur_id      = ?
              AND ? >= h.montant_min
              AND (h.montant_max IS NULL OR ? <= h.montant_max)
              AND h.date_modif = (
                  SELECT MAX(h2.date_modif)
                  FROM baremes_frais_historique h2
                  WHERE h2.bareme_id = h.bareme_id
                    AND h2.date_modif <= ?
              )
            LIMIT 1
        ";
        
        $query = $db->query($sql, [
            $typeOperationId,
            $operateurId,
            $montantBrut,
            $montantBrut,
            $dateTransaction
        ]);
        
        $row = $query->getRow();
        return $row ? (float)$row->frais_fixe : null;
    }

    /**
     * Get the active fee schedules/slabs for a type of operation and operator.
     */
    public function getFeesSchedules(string $typeCode, int $operateurId)
    {
        $db = $this->db;
        $sql = "
            SELECT h.montant_min, h.montant_max, h.frais_fixe
            FROM baremes_frais b
            JOIN baremes_frais_historique h ON h.bareme_id = b.id
            JOIN types_operation t ON t.id = b.type_operation_id
            WHERE t.code = ?
              AND b.operateur_id = ?
              AND h.date_modif = (
                  SELECT MAX(h2.date_modif)
                  FROM baremes_frais_historique h2
                  WHERE h2.bareme_id = h.bareme_id
                    AND h2.date_modif <= CURRENT_TIMESTAMP
              )
            ORDER BY h.montant_min ASC
        ";
        
        $query = $db->query($sql, [$typeCode, $operateurId]);
        return $query->getResult();
    }
}