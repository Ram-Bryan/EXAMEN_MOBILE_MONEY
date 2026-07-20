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
}