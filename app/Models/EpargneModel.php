<?php

namespace App\Models;

use CodeIgniter\Model;

class EpargneModel extends Model
{
    protected $table            = 'epargne';
    protected $primaryKey       = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['client_id', 'montant', 'pourcentage', 'date_modif'];
    protected $useTimestamps    = false;
    protected $updatedField     = '';

    public function getEpargneOf($clientId) {
        $sql = "SELECT * FROM epargne WHERE client_id = ?";
        $query = $this->db->query($sql, [$clientId]);
        $row = $query->getRowObject();
        return $row;
    }

}
