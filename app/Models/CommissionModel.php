<?php

namespace App\Models;

use CodeIgniter\Model;

class CommissionModel extends Model
{
    protected $table            = 'commissions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['operateur_destination_id'];
    protected $useTimestamps    = false;
    protected $createdField     = '';
    protected $updatedField     = '';

    /**
     * Retourne la commission actuelle d'un opérateur externe
     * avec le dernier pourcentage en vigueur
     */
    public function getCommissionByOperateur(int $operateurId)
    {
        $sql = "
            SELECT c.id, c.operateur_destination_id, ch.pourcentage, ch.date_modif
            FROM commissions c
            JOIN commissions_historique ch ON ch.commission_id = c.id
            WHERE c.operateur_destination_id = ?
              AND ch.date_modif = (
                  SELECT MAX(ch2.date_modif)
                  FROM commissions_historique ch2
                  WHERE ch2.commission_id = ch.commission_id
              )
            LIMIT 1
        ";
        return $this->db->query($sql, [$operateurId])->getRow();
    }

    /**
     * Retourne toutes les commissions actuelles (une par opérateur externe)
     */
    public function getAllCommissions()
    {
        $sql = "
            SELECT
                c.id,
                o.prefixe,
                o.nom,
                o.id AS operateur_id,
                o.est_notre_operateur,
                COALESCE(ch.pourcentage, 0) AS pourcentage,
                ch.date_modif
            FROM commissions c
            JOIN operateur_prefixes o ON o.id = c.operateur_destination_id
            LEFT JOIN commissions_historique ch ON ch.commission_id = c.id
              AND ch.date_modif = (
                  SELECT MAX(ch2.date_modif)
                  FROM commissions_historique ch2
                  WHERE ch2.commission_id = ch.commission_id
              )
            ORDER BY o.nom
        ";
        return $this->db->query($sql)->getResult();
    }

    /**
     * Crée ou retourne la commission pour un opérateur externe
     */
    public function getOrCreate(int $operateurId): int
    {
        $existing = $this->where('operateur_destination_id', $operateurId)->first();
        if ($existing) {
            return $existing->id;
        }
        return $this->insert(['operateur_destination_id' => $operateurId], true);
    }
}
