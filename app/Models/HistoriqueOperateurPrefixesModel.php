<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoriqueOperateurPrefixesModel extends Model
{
    protected $table            = 'historique_operateur_prefixes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['operateur_prefixe_id', 'prefixe', 'date_modif'];
    protected $useTimestamps    = false;
    protected $createdField     = 'date_modif';
    protected $updatedField     = '';

    /**
     * Retourne tous les préfixes associés à un opérateur
     */
    public function getByOperateur(int $operateurId)
    {
        return $this->where('operateur_prefixe_id', $operateurId)
                    ->orderBy('date_modif', 'DESC')
                    ->findAll();
    }

    /**
     * Ajoute un préfixe à l'historique d'un opérateur
     */
    public function addPrefixe(int $operateurId, string $prefixe)
    {
        return $this->insert([
            'operateur_prefixe_id' => $operateurId,
            'prefixe'              => $prefixe,
            'date_modif'           => date('Y-m-d H:i:s'),
        ]);
    }
}
