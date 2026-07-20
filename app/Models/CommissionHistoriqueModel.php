<?php

namespace App\Models;

use CodeIgniter\Model;

class CommissionHistoriqueModel extends Model
{
    protected $table            = 'commissions_historique';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['commission_id', 'pourcentage', 'date_modif'];
    protected $useTimestamps    = false;
    protected $createdField     = 'date_modif';
    protected $updatedField     = '';

    /**
     * Ajoute une nouvelle version du pourcentage (INSERT uniquement, jamais d'UPDATE)
     * L'historique est ainsi préservé.
     */
    public function addPourcentage(int $commissionId, float $pourcentage): bool
    {
        return (bool) $this->insert([
            'commission_id' => $commissionId,
            'pourcentage'   => $pourcentage,
            'date_modif'    => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Retourne l'historique complet d'une commission
     */
    public function getHistorique(int $commissionId)
    {
        return $this->where('commission_id', $commissionId)
                    ->orderBy('date_modif', 'DESC')
                    ->findAll();
    }
}
