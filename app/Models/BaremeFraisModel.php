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
        return $this->query($sql)->getResultObject();
    }

    public function getBaremesByOperateur(int $operateurId)
    {
        $sql = "SELECT b.id AS bareme_id, b.type_operation_id, b.operateur_id,
                       o.prefixe, t.code AS type_code, t.nom AS type_nom,
                       h.montant_min, h.montant_max, h.frais_fixe, h.date_modif
                FROM baremes_frais b
                JOIN operateur_prefixes o ON b.operateur_id = o.id
                JOIN types_operation t ON b.type_operation_id = t.id
                JOIN baremes_frais_historique h ON h.bareme_id = b.id
                WHERE b.operateur_id = ?
                  AND h.date_modif = (
                      SELECT MAX(h2.date_modif)
                      FROM baremes_frais_historique h2
                      WHERE h2.bareme_id = h.bareme_id
                  )
                ORDER BY t.nom, h.montant_min";
        return $this->query($sql, [$operateurId])->getResultObject();
    }

    public function getAllBaremesByOperateur(int $operateurId)
    {
        $sql = "SELECT b.id AS bareme_id, b.type_operation_id, b.operateur_id,
                       o.prefixe, t.code AS type_code, t.nom AS type_nom,
                       h.id AS historique_id,
                       h.montant_min, h.montant_max, h.frais_fixe, h.date_modif
                FROM baremes_frais b
                JOIN operateur_prefixes o ON b.operateur_id = o.id
                JOIN types_operation t ON b.type_operation_id = t.id
                JOIN baremes_frais_historique h ON h.bareme_id = b.id
                WHERE b.operateur_id = ?
                ORDER BY t.nom, h.date_modif DESC, h.montant_min";
        return $this->query($sql, [$operateurId])->getResultObject();
    }

    public function addTranche($type_operation_id, $operateur_id, $montant_min, $montant_max, $frais_fixe)
    {
       
        $this->transStart();

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
        $now = date('Y-m-d H:i:s');

        $latestDate = $historiqueModel->select('MAX(date_modif) AS max_date')
            ->where('bareme_id', $baremeId)
            ->first();

        if ($latestDate && $latestDate->max_date) {
            $currentTranches = $historiqueModel
                ->where('bareme_id', $baremeId)
                ->where('date_modif', $latestDate->max_date)
                ->findAll();

            foreach ($currentTranches as $t) {
                $historiqueModel->insert([
                    'bareme_id'   => $baremeId,
                    'montant_min' => $t->montant_min,
                    'montant_max' => $t->montant_max,
                    'frais_fixe'  => $t->frais_fixe,
                    'date_modif'  => $now
                ]);
            }
        }

        $historiqueModel->insert([
            'bareme_id'   => $baremeId,
            'montant_min' => $montant_min,
            'montant_max' => $montant_max === '' ? null : $montant_max,
            'frais_fixe'  => $frais_fixe,
            'date_modif'  => $now
        ]);

        $this->transComplete();

        return $this->transStatus();
    }
    
    public function getFrais(int $typeOperationId, int $operateurId, float $montantBrut, string $dateTransaction = null)
    {
        if ($dateTransaction === null) {
            $dateTransaction = date('Y-m-d H:i:s');
        }
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
        
        $query = $this->query($sql, [
            $typeOperationId,
            $operateurId,
            $montantBrut,
            $montantBrut,
            $dateTransaction
        ]);
        
        $row = $query->getRow();
        return $row ? (float)$row->frais_fixe : null;
    }

    public function getCommission(int $destinationOperateurId, float $montant): float
    {
        $sql = "
            SELECT ch.pourcentage
            FROM commissions_historique ch
            JOIN commissions c ON c.id = ch.commission_id
            WHERE c.operateur_destination_id = ?
              AND ch.date_modif = (
                  SELECT MAX(ch2.date_modif)
                  FROM commissions_historique ch2
                  WHERE ch2.commission_id = ch.commission_id
              )
            LIMIT 1
        ";
        $query = $this->query($sql, [$destinationOperateurId]);
        $row = $query->getRow();
        return $row ? (float)$row->pourcentage * $montant / 100 : 0.0;
    }

    public function isInterOperator(int $senderOperateurId, int $recipientOperateurId): bool
    {
        $sql = "SELECT est_notre_operateur FROM operateur_prefixes WHERE id = ?";
        $senderOp = $this->query($sql, [$senderOperateurId])->getRow();
        $recipientOp = $this->query($sql, [$recipientOperateurId])->getRow();

        if (!$senderOp || !$recipientOp) return false;
        return $senderOp->est_notre_operateur == 1 && $recipientOp->est_notre_operateur == 0;
    }

    /**
     * Get the active fee schedules/slabs for a type of operation and operator.
     */
    public function getFeesSchedules(string $typeCode, int $operateurId)
    {
        $now = date('Y-m-d H:i:s');
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
                    AND h2.date_modif <= ?
              )
            ORDER BY h.montant_min ASC
        ";
        
        $query = $this->query($sql, [$typeCode, $operateurId, $now]);
        return $query->getResult();
    }
}