<?php

namespace App\Models;

use CodeIgniter\Model;

class BaremeFraisHistoriqueModel extends Model
{
    protected $table            = 'baremes_frais_historique';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['bareme_id', 'montant_min', 'montant_max', 'frais_fixe', 'date_modif'];
    protected $useTimestamps    = true;
    protected $createdField     = 'date_modif';
    protected $updatedField     = null;

    public function addHistorique($bareme_id, $montant_min, $montant_max, $frais_fixe)
    {
        return $this->insert([
            'bareme_id' => $bareme_id,
            'montant_min' => $montant_min,
            'montant_max' => $montant_max === '' ? null : $montant_max,
            'frais_fixe' => $frais_fixe
        ]);
    }
}