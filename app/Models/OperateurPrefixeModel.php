<?php

namespace App\Models;

use CodeIgniter\Model;

class OperateurPrefixeModel extends Model
{
    protected $table            = 'operateur_prefixes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['prefixe', 'nom', 'est_notre_operateur'];
    protected $useTimestamps    = false;
    protected $createdField     = 'created_at';
    protected $updatedField     = '';

    /**
     * Retourne tous les opérateurs externes (pas le nôtre)
     */
    public function getExternes()
    {
        return $this->where('est_notre_operateur', 0)->findAll();
    }

    /**
     * Retourne notre opérateur
     */
    public function getNotreOperateur()
    {
        return $this->where('est_notre_operateur', 1)->first();
    }
}